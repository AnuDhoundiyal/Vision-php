/**
 * VisionNEX Kiosk JavaScript
 * Handles camera operations, face recognition, and UI interactions
 */

// Global variables
let cameraStream = null;
let isRecognizing = false;
let autoRecognitionInterval = null;
let settings = {
    confidenceThreshold: 0.85,
    autoCaptureDelay: 5,
    defaultClass: null,
    enableSound: true,
    enableAutoCapture: false
};
let stats = {
    todayRecognitions: 0,
    successfulRecognitions: 0,
    failedRecognitions: 0
};

// Initialize kiosk when page loads
document.addEventListener('DOMContentLoaded', function () {
    initializeKiosk();
    updateDateTime();
    setInterval(updateDateTime, 1000);
    loadSettings();
    loadTodayStats();
    checkSystemStatus();
    loadAvailableClasses();
});

/**
 * Initialize kiosk functionality
 */
function initializeKiosk() {
    console.log('VisionNEX Kiosk initialized');

    // Confidence slider
    const confidenceSlider = document.getElementById('confidenceThreshold');
    const confidenceValue = document.getElementById('confidenceValue');
    if (confidenceSlider && confidenceValue) {
        confidenceSlider.addEventListener('input', function () {
            const value = Math.round(this.value * 100);
            confidenceValue.textContent = value + '%';
        });
    }
}

/**
 * Update date and time display
 */
function updateDateTime() {
    const now = new Date();
    const timeEl = document.getElementById('currentTime');
    const dateEl = document.getElementById('currentDate');

    if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    });
    if (dateEl) dateEl.textContent = now.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

/**
 * Start camera feed
 */
async function startCamera() {
    if (cameraStream) return;
    
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { 
                width: { ideal: 1280 }, 
                height: { ideal: 720 }, 
                facingMode: 'user' 
            }
        });
        
        const video = document.getElementById('cameraFeed');
        video.srcObject = cameraStream;

        // Update UI
        document.getElementById('startCameraBtn').classList.add('hidden');
        document.getElementById('stopCameraBtn').classList.remove('hidden');
        document.getElementById('captureBtn').classList.remove('hidden');
        document.getElementById('cameraOffOverlay').classList.add('hidden');

        updateCameraStatus('Online');
        showToast('Camera Started', 'Camera is now active and ready for face recognition', 'success');

        // Start auto-capture if enabled
        if (settings.enableAutoCapture && settings.autoCaptureDelay > 0) {
            startAutoRecognition();
        }

    } catch (error) {
        console.error('Error starting camera:', error);
        updateCameraStatus('Error');
        showToast('Camera Error', 'Unable to access camera. Please check permissions.', 'error');
    }
}

/**
 * Stop camera feed
 */
function stopCamera() {
    if (!cameraStream) return;
    
    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;
    
    const video = document.getElementById('cameraFeed');
    video.srcObject = null;

    // Update UI
    document.getElementById('startCameraBtn').classList.remove('hidden');
    document.getElementById('stopCameraBtn').classList.add('hidden');
    document.getElementById('captureBtn').classList.add('hidden');
    document.getElementById('cameraOffOverlay').classList.remove('hidden');

    hideAllOverlays();
    updateCameraStatus('Offline');
    
    if (autoRecognitionInterval) {
        clearInterval(autoRecognitionInterval);
        autoRecognitionInterval = null;
    }
}

/**
 * Start automatic face recognition
 */
function startAutoRecognition() {
    if (autoRecognitionInterval) clearInterval(autoRecognitionInterval);
    
    autoRecognitionInterval = setInterval(() => {
        if (!isRecognizing && cameraStream) {
            captureAndRecognize();
        }
    }, settings.autoCaptureDelay * 1000);
}

/**
 * Capture image and perform face recognition
 */
async function captureAndRecognize() {
    if (isRecognizing || !cameraStream) return;
    
    isRecognizing = true;

    try {
        hideAllOverlays();
        document.getElementById('scanningOverlay').classList.remove('hidden');
        if (settings.enableSound) playSound('scan');

        const video = document.getElementById('cameraFeed');
        const canvas = document.getElementById('captureCanvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw current video frame to canvas
        ctx.drawImage(video, 0, 0);

        // Convert canvas to blob
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
        if (!blob) throw new Error('Failed to capture image');

        // Prepare form data
        const formData = new FormData();
        formData.append('image', blob, 'capture.jpg');
        formData.append('confidence_threshold', settings.confidenceThreshold);
        if (settings.defaultClass) {
            formData.append('class_id', settings.defaultClass);
        }

        // Send to recognition API
        const response = await fetch('api/recognition/process.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success && data.data && data.data.confidence >= settings.confidenceThreshold) {
            handleSuccessfulRecognition(data.data);
        } else {
            handleFailedRecognition(data.message || 'Face not recognized');
        }
        
    } catch (error) {
        console.error('Recognition error:', error);
        handleFailedRecognition('Recognition system error: ' + error.message);
    } finally {
        isRecognizing = false;
    }
}

/**
 * Handle successful face recognition
 */
function handleSuccessfulRecognition(userData) {
    hideAllOverlays();
    document.getElementById('successOverlay').classList.remove('hidden');
    document.getElementById('recognizedUser').textContent = userData.name;
    document.getElementById('recognizedDetails').textContent = 
        `${userData.user_type.toUpperCase()} • ${userData.id_number || 'N/A'} • Confidence: ${Math.round(userData.confidence * 100)}%`;

    // Update stats
    stats.todayRecognitions++;
    stats.successfulRecognitions++;
    updateStatsDisplay();

    // Add to recent activity
    addToRecentActivity({
        type: 'success',
        name: userData.name,
        user_type: userData.user_type,
        confidence: userData.confidence,
        time: new Date(),
        attendanceMarked: userData.attendance_marked || false
    });

    if (settings.enableSound) playSound('success');

    const msg = userData.attendance_marked ? 'Attendance marked successfully' : 'Face recognized';
    showToast('Recognition Successful', `${userData.name} - ${msg}`, 'success');

    // Auto-hide after 3 seconds
    setTimeout(() => {
        hideAllOverlays();
    }, 3000);
}

/**
 * Handle failed face recognition
 */
function handleFailedRecognition(message) {
    hideAllOverlays();
    document.getElementById('errorOverlay').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = message;

    // Update stats
    stats.todayRecognitions++;
    stats.failedRecognitions++;
    updateStatsDisplay();

    // Add to recent activity
    addToRecentActivity({
        type: 'error',
        message: message,
        time: new Date()
    });

    if (settings.enableSound) playSound('error');
    showToast('Recognition Failed', message, 'error');

    // Auto-hide after 3 seconds
    setTimeout(() => {
        hideAllOverlays();
    }, 3000);
}

/**
 * Hide all overlay states
 */
function hideAllOverlays() {
    ['scanningOverlay', 'successOverlay', 'errorOverlay'].forEach(id => {
        document.getElementById(id).classList.add('hidden');
    });
}

/**
 * Update camera status indicator
 */
function updateCameraStatus(status) {
    const el = document.getElementById('cameraStatus');
    if (!el) return;
    
    el.textContent = status;
    el.className = 'px-3 py-1 text-white text-sm rounded-full';
    
    switch (status) {
        case 'Online':
            el.classList.add('bg-green-500');
            break;
        case 'Offline':
            el.classList.add('bg-red-500');
            break;
        case 'Error':
            el.classList.add('bg-red-600');
            break;
        default:
            el.classList.add('bg-gray-500');
    }
}

/**
 * Update statistics display
 */
function updateStatsDisplay() {
    document.getElementById('todayRecognitions').textContent = stats.todayRecognitions;
    document.getElementById('successfulRecognitions').textContent = stats.successfulRecognitions;
    document.getElementById('failedRecognitions').textContent = stats.failedRecognitions;
}

/**
 * Add activity to recent activity list
 */
function addToRecentActivity(activity) {
    const container = document.getElementById('recentActivity');
    
    // Clear "no activity" message if present
    if (container.children.length === 1 && container.children[0].textContent.includes('No recent activity')) {
        container.innerHTML = '';
    }

    const timeStr = activity.time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const activityEl = document.createElement('div');
    activityEl.className = 'flex items-center space-x-3 p-3 bg-white/10 rounded-lg';

    if (activity.type === 'success') {
        activityEl.innerHTML = `
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">${activity.name}</p>
                <p class="text-blue-200 text-xs">${activity.user_type.toUpperCase()} • Confidence: ${Math.round(activity.confidence * 100)}%</p>
            </div>
            <div class="text-xs text-blue-300">${timeStr}</div>
        `;
    } else {
        activityEl.innerHTML = `
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-red-400"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium">Recognition Failed</p>
                <p class="text-blue-200 text-xs truncate">${activity.message}</p>
            </div>
            <div class="text-xs text-blue-300">${timeStr}</div>
        `;
    }

    container.insertBefore(activityEl, container.firstChild);
    
    // Keep only last 5 activities
    while (container.children.length > 5) {
        container.removeChild(container.lastChild);
    }
}

/**
 * Settings modal functions
 */
function showSettingsModal() {
    document.getElementById('settingsModal').classList.remove('hidden');
}

function hideSettingsModal() {
    document.getElementById('settingsModal').classList.add('hidden');
}

function saveSettings() {
    settings.confidenceThreshold = parseFloat(document.getElementById('confidenceThreshold').value);
    settings.autoCaptureDelay = parseInt(document.getElementById('autoCaptureDelay').value);
    settings.defaultClass = document.getElementById('defaultClass').value || null;
    settings.enableSound = document.getElementById('enableSound').checked;
    settings.enableAutoCapture = document.getElementById('enableAutoCapture').checked;

    // Save to localStorage
    localStorage.setItem('kioskSettings', JSON.stringify(settings));

    // Restart auto-capture if settings changed
    if (settings.enableAutoCapture && cameraStream && settings.autoCaptureDelay > 0) {
        startAutoRecognition();
    } else if (autoRecognitionInterval) {
        clearInterval(autoRecognitionInterval);
        autoRecognitionInterval = null;
    }

    hideSettingsModal();
    showToast('Settings Saved', 'Kiosk settings updated successfully', 'success');
}

/**
 * Load settings from localStorage
 */
function loadSettings() {
    const saved = localStorage.getItem('kioskSettings');
    if (saved) {
        settings = { ...settings, ...JSON.parse(saved) };
    }
    
    // Update UI elements
    document.getElementById('confidenceThreshold').value = settings.confidenceThreshold;
    document.getElementById('confidenceValue').textContent = Math.round(settings.confidenceThreshold * 100) + '%';
    document.getElementById('autoCaptureDelay').value = settings.autoCaptureDelay;
    document.getElementById('enableSound').checked = settings.enableSound;
    document.getElementById('enableAutoCapture').checked = settings.enableAutoCapture;
    
    if (settings.defaultClass) {
        document.getElementById('defaultClass').value = settings.defaultClass;
    }
}

/**
 * Load available classes for default selection
 */
async function loadAvailableClasses() {
    try {
        const response = await fetch('api/admin/classes.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('defaultClass');
            select.innerHTML = '<option value="">No default class</option>';
            
            data.data.forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.id;
                option.textContent = `${cls.class_name} - ${cls.section} (${cls.academic_year})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading classes:', error);
    }
}

/**
 * Load today's statistics from localStorage
 */
function loadTodayStats() {
    const saved = localStorage.getItem('kioskStats');
    if (saved) {
        const parsed = JSON.parse(saved);
        const today = new Date().toDateString();
        
        if (parsed.date === today) {
            stats = { ...stats, ...parsed };
            updateStatsDisplay();
        }
    }
}

/**
 * Save statistics to localStorage
 */
function saveStats() {
    localStorage.setItem('kioskStats', JSON.stringify({
        ...stats,
        date: new Date().toDateString()
    }));
}

/**
 * Check system status
 */
async function checkSystemStatus() {
    try {
        // Check recognition API
        const response = await fetch('api/recognition/status.php');
        const apiEl = document.getElementById('apiStatus');

        if (response.ok) {
            const data = await response.json();
            apiEl.textContent = 'Online';
            apiEl.className = 'px-3 py-1 bg-green-500 text-white text-sm rounded-full';
            
            if (data.data && data.data.users_loaded) {
                showToast('Service Connected', `Recognition API - ${data.data.users_loaded} users loaded`, 'info');
            }
        } else {
            apiEl.textContent = 'Error';
            apiEl.className = 'px-3 py-1 bg-red-500 text-white text-sm rounded-full';
            showToast('Service Error', 'Recognition service returned an error', 'error');
        }
    } catch (error) {
        const apiEl = document.getElementById('apiStatus');
        apiEl.textContent = 'Offline';
        apiEl.className = 'px-3 py-1 bg-red-500 text-white text-sm rounded-full';
        console.error('Recognition service error:', error);
        showToast('Service Unavailable', 'Recognition service is not available', 'warning');
    }
}

/**
 * Play sound notification
 */
function playSound(type) {
    if (!settings.enableSound) return;
    
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    
    osc.connect(gain);
    gain.connect(audioCtx.destination);

    switch (type) {
        case 'scan':
            osc.frequency.setValueAtTime(800, audioCtx.currentTime);
            osc.frequency.setValueAtTime(600, audioCtx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.2);
            break;
            
        case 'success':
            osc.frequency.setValueAtTime(523, audioCtx.currentTime);
            osc.frequency.setValueAtTime(659, audioCtx.currentTime + 0.1);
            osc.frequency.setValueAtTime(784, audioCtx.currentTime + 0.2);
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.4);
            break;
            
        case 'error':
            osc.frequency.setValueAtTime(300, audioCtx.currentTime);
            osc.frequency.setValueAtTime(250, audioCtx.currentTime + 0.1);
            osc.frequency.setValueAtTime(200, audioCtx.currentTime + 0.2);
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
            osc.start(audioCtx.currentTime);
            osc.stop(audioCtx.currentTime + 0.3);
            break;
    }
}

/**
 * Show toast notification
 */
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const titleEl = document.getElementById('toastTitle');
    const messageEl = document.getElementById('toastMessage');
    
    // Set icon based on type
    switch (type) {
        case 'success':
            icon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
            break;
        case 'error':
            icon.innerHTML = '<i class="fas fa-times-circle text-red-500 text-xl"></i>';
            break;
        case 'warning':
            icon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>';
            break;
        default:
            icon.innerHTML = '<i class="fas fa-info-circle text-blue-500 text-xl"></i>';
    }
    
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    toast.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);
}

// Save stats periodically and on page unload
setInterval(saveStats, 30000); // Save every 30 seconds
window.addEventListener('beforeunload', () => {
    saveStats();
    if (cameraStream) stopCamera();
});