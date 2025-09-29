/*
# VisionNex Kiosk JavaScript (Fixed)
Features:
- Face recognition with updated API links
- Settings, stats, recent activity, time, toasts
- Auto-capture, sound notifications
*/

// Global variables
let cameraStream = null;
let isRecognizing = false;
let settings = {
    confidenceThreshold: 0.9,
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
let faceDetectionInterval = null;

// ------------------- INIT -------------------
document.addEventListener('DOMContentLoaded', function () {
    initializeKiosk();
    updateDateTime();
    setInterval(updateDateTime, 1000);
    loadSettings();
    loadTodayStats();
    checkSystemStatus();
});

// ------------------- INITIALIZE -------------------
function initializeKiosk() {
    console.log('VisionNex Kiosk initialized');

    // Confidence slider
    const confidenceSlider = document.getElementById('confidenceThreshold');
    const confidenceValue = document.getElementById('confidenceValue');
    if (confidenceSlider && confidenceValue) {
        confidenceSlider.addEventListener('input', function () {
            const value = Math.round(this.value * 100);
            confidenceValue.textContent = value + '%';
        });
    }

    // Load classes
    loadAvailableClasses();
}

// ------------------- TIME -------------------
function updateDateTime() {
    const now = new Date();
    const timeEl = document.getElementById('currentTime');
    const dateEl = document.getElementById('currentDate');

    if (timeEl) timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    if (dateEl) dateEl.textContent = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

// ------------------- CAMERA -------------------
async function startCamera() {
    if (cameraStream) return;
    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' }
        });
        const video = document.getElementById('cameraFeed');
        video.srcObject = cameraStream;

        document.getElementById('startCameraBtn').classList.add('hidden');
        document.getElementById('stopCameraBtn').classList.remove('hidden');
        document.getElementById('captureBtn').classList.remove('hidden');
        document.getElementById('cameraOffOverlay').classList.add('hidden');

        updateCameraStatus('Online');
        showToast('Camera Started', 'Camera is now active and ready for face recognition', 'success');

        if (settings.enableAutoCapture) startFaceDetection();

    } catch (error) {
        console.error('Error starting camera:', error);
        updateCameraStatus('Error');
        showToast('Camera Error', 'Unable to access camera. Please check permissions.', 'error');
    }
}

function stopCamera() {
    if (!cameraStream) return;
    cameraStream.getTracks().forEach(track => track.stop());
    cameraStream = null;
    const video = document.getElementById('cameraFeed');
    video.srcObject = null;

    document.getElementById('startCameraBtn').classList.remove('hidden');
    document.getElementById('stopCameraBtn').classList.add('hidden');
    document.getElementById('captureBtn').classList.add('hidden');
    document.getElementById('cameraOffOverlay').classList.remove('hidden');

    hideAllOverlays();
    updateCameraStatus('Offline');

    if (faceDetectionInterval) { clearInterval(faceDetectionInterval); faceDetectionInterval = null; }
}

// ------------------- FACE DETECTION / AUTO-CAPTURE -------------------
function startFaceDetection() {
    if (faceDetectionInterval) clearInterval(faceDetectionInterval);
    faceDetectionInterval = setInterval(() => {
        if (!isRecognizing && cameraStream) {
            setTimeout(captureAndRecognize, settings.autoCaptureDelay * 1000);
        }
    }, 2000);
}

// ------------------- CAPTURE & RECOGNITION -------------------
// ------------------- CAPTURE & RECOGNITION -------------------
async function captureAndRecognize() {
    if (isRecognizing || !cameraStream) return;
    isRecognizing = true;

    try {
        hideAllOverlays();
        document.getElementById('scanningOverlay').classList.remove('hidden');
        if (settings.enableSound) playSound('scan');

        const video = document.getElementById('cameraFeed');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
        if (!blob) throw new Error('Failed to capture image');

        const formData = new FormData();
        formData.append('image', blob, 'capture.jpg');
        if (settings.defaultClass) formData.append('class_id', settings.defaultClass);

        // DEBUG: Log FormData to ensure image is attached
        for (let pair of formData.entries()) {
            console.log('FormData:', pair[0], pair[1]);
        }

        // Use correct API path (encode spaces)
        const response = await fetch('/php_project%20-%20Copy/attendance_project/backend_php/api/face_recognition.php?action=recognize', {
            method: 'POST',
            body: formData
        });

        let data;
        try {
            data = await response.json();
        } catch (e) {
            const text = await response.text();
            console.error('Invalid JSON response:', text);
            handleFailedRecognition('Recognition system error (invalid response)');
            return;
        }

        if (data.success && data.data && data.data.confidence >= settings.confidenceThreshold) {
            handleSuccessfulRecognition(data.data);
        } else {
            handleFailedRecognition(data.message || 'Face not recognized');
        }
    } catch (error) {
        console.error('Recognition error:', error);
        handleFailedRecognition('Recognition system error');
    } finally {
        isRecognizing = false;
    }
}

// ------------------- SUCCESS/FAIL -------------------
function handleSuccessfulRecognition(userData) {
    hideAllOverlays();
    document.getElementById('successOverlay').classList.remove('hidden');
    document.getElementById('recognizedUser').textContent = userData.name;

    stats.todayRecognitions++; stats.successfulRecognitions++;
    updateStatsDisplay();

    addToRecentActivity({ type: 'success', name: userData.name, confidence: userData.confidence, time: new Date(), attendanceMarked: userData.attendance_marked || false });
    if (settings.enableSound) playSound('success');

    const msg = userData.attendance_marked ? 'Attendance marked' : 'Face recognized';
    showToast('Recognition Successful', `${userData.name} - ${msg}`, 'success');

    setTimeout(() => { hideAllOverlays(); }, 3000);
}

function handleFailedRecognition(message) {
    hideAllOverlays();
    document.getElementById('errorOverlay').classList.remove('hidden');

    stats.todayRecognitions++; stats.failedRecognitions++;
    updateStatsDisplay();

    addToRecentActivity({ type: 'error', message: message, time: new Date() });
    if (settings.enableSound) playSound('error');

    showToast('Recognition Failed', message, 'error');
    setTimeout(() => { hideAllOverlays(); }, 3000);
}

// ------------------- OVERLAYS -------------------
function hideAllOverlays() {
    ['scanningOverlay', 'successOverlay', 'errorOverlay'].forEach(id => document.getElementById(id).classList.add('hidden'));
}

// ------------------- CAMERA STATUS -------------------
function updateCameraStatus(status) {
    const el = document.getElementById('cameraStatus');
    if (!el) return;
    el.textContent = status;
    el.className = 'px-3 py-1 text-white text-sm rounded-full';
    switch (status) {
        case 'Online': el.classList.add('bg-green-500'); break;
        case 'Offline': el.classList.add('bg-red-500'); break;
        case 'Error': el.classList.add('bg-red-600'); break;
        default: el.classList.add('bg-gray-500');
    }
}

// ------------------- STATS -------------------
function updateStatsDisplay() {
    document.getElementById('todayRecognitions').textContent = stats.todayRecognitions;
    document.getElementById('successfulRecognitions').textContent = stats.successfulRecognitions;
    document.getElementById('failedRecognitions').textContent = stats.failedRecognitions;
}

// ------------------- RECENT ACTIVITY -------------------
function addToRecentActivity(activity) {
    const container = document.getElementById('recentActivity');
    if (container.children.length === 1 && container.children[0].textContent.includes('No recent activity')) container.innerHTML = '';

    const timeStr = activity.time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const activityEl = document.createElement('div');
    activityEl.className = 'flex items-center space-x-3 p-3 bg-white/10 rounded-lg';

    if (activity.type === 'success') {
        activityEl.innerHTML = `
            <div class="flex-shrink-0"><i class="fas fa-check-circle text-green-400"></i></div>
            <div class="flex-1 min-w-0"><p class="text-white text-sm font-medium truncate">${activity.name}</p>
            <p class="text-blue-200 text-xs">Confidence: ${Math.round(activity.confidence * 100)}%</p></div>
            <div class="text-xs text-blue-300">${timeStr}</div>
        `;
    } else {
        activityEl.innerHTML = `
            <div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div>
            <div class="flex-1 min-w-0"><p class="text-white text-sm font-medium">Recognition Failed</p>
            <p class="text-blue-200 text-xs truncate">${activity.message}</p></div>
            <div class="text-xs text-blue-300">${timeStr}</div>
        `;
    }

    container.insertBefore(activityEl, container.firstChild);
    while (container.children.length > 5) container.removeChild(container.lastChild);
}

// ------------------- SETTINGS -------------------
function showSettingsModal() { document.getElementById('settingsModal').classList.remove('hidden'); }
function hideSettingsModal() { document.getElementById('settingsModal').classList.add('hidden'); }
function saveSettings() {
    settings.confidenceThreshold = parseFloat(document.getElementById('confidenceThreshold').value);
    settings.autoCaptureDelay = parseInt(document.getElementById('autoCaptureDelay').value);
    settings.defaultClass = document.getElementById('defaultClass').value || null;
    settings.enableSound = document.getElementById('enableSound').checked;
    settings.enableAutoCapture = document.getElementById('enableAutoCapture').checked;

    localStorage.setItem('kioskSettings', JSON.stringify(settings));

    if (settings.enableAutoCapture && cameraStream) startFaceDetection();
    else if (faceDetectionInterval) { clearInterval(faceDetectionInterval); faceDetectionInterval = null; }

    hideSettingsModal();
    showToast('Settings Saved', 'Kiosk settings updated', 'success');
}
function loadSettings() {
    const saved = localStorage.getItem('kioskSettings');
    if (saved) settings = { ...settings, ...JSON.parse(saved) };
    document.getElementById('confidenceThreshold').value = settings.confidenceThreshold;
    document.getElementById('confidenceValue').textContent = Math.round(settings.confidenceThreshold * 100) + '%';
    document.getElementById('autoCaptureDelay').value = settings.autoCaptureDelay;
    document.getElementById('enableSound').checked = settings.enableSound;
    document.getElementById('enableAutoCapture').checked = settings.enableAutoCapture;
    if (settings.defaultClass) document.getElementById('defaultClass').value = settings.defaultClass;
}

// ------------------- CLASS LOADING -------------------
async function loadAvailableClasses() {
    try {
        const response = await fetch('../backend_php/api/classes.php');
        const data = await response.json();
        if (data.success) {
            const select = document.getElementById('defaultClass');
            select.innerHTML = '<option value="">No default class</option>';
            data.data.forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.id;
                option.textContent = `${cls.subject_name} - ${cls.batch}/${cls.division}`;
                select.appendChild(option);
            });
        }
    } catch (err) { console.error(err); }
}

// ------------------- STATS STORAGE -------------------
function loadTodayStats() {
    const saved = localStorage.getItem('kioskStats');
    if (saved) {
        const parsed = JSON.parse(saved);
        const today = new Date().toDateString();
        if (parsed.date === today) { stats = { ...stats, ...parsed }; updateStatsDisplay(); }
    }
}
function saveStats() { localStorage.setItem('kioskStats', JSON.stringify({ ...stats, date: new Date().toDateString() })); }
setInterval(saveStats, 30000);
window.addEventListener('beforeunload', () => { saveStats(); if (cameraStream) stopCamera(); });

// ------------------- SOUND -------------------
function playSound(type) {
    if (!settings.enableSound) return;
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.connect(gain); gain.connect(audioCtx.destination);

    switch (type) {
        case 'scan':
            osc.frequency.setValueAtTime(800, audioCtx.currentTime);
            osc.frequency.setValueAtTime(600, audioCtx.currentTime + 0.1);
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
            osc.start(audioCtx.currentTime); osc.stop(audioCtx.currentTime + 0.2); break;
        case 'success':
            osc.frequency.setValueAtTime(523, audioCtx.currentTime);
            osc.frequency.setValueAtTime(659, audioCtx.currentTime + 0.1);
            osc.frequency.setValueAtTime(784, audioCtx.currentTime + 0.2);
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
            osc.start(audioCtx.currentTime); osc.stop(audioCtx.currentTime + 0.4); break;
        case 'error':
            osc.frequency.setValueAtTime(300, audioCtx.currentTime);
            osc.frequency.setValueAtTime(250, audioCtx.currentTime + 0.1);
            osc.frequency.setValueAtTime(200, audioCtx.currentTime + 0.2);
            gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
            osc.start(audioCtx.currentTime); osc.stop(audioCtx.currentTime + 0.3); break;
    }
}

// ------------------- TOAST -------------------
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const tTitle = document.getElementById('toastTitle');
    const tMsg = document.getElementById('toastMessage');
    switch (type) {
        case 'success': icon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>'; break;
        case 'error': icon.innerHTML = '<i class="fas fa-times-circle text-red-500 text-xl"></i>'; break;
        case 'warning': icon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>'; break;
        default: icon.innerHTML = '<i class="fas fa-info-circle text-blue-500 text-xl"></i>';
    }
    tTitle.textContent = title; tMsg.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => { toast.classList.add('hidden'); }, 5000);
}

// ------------------- SYSTEM STATUS -------------------
async function checkSystemStatus() {
    try {
        // Use action=faces to avoid POST errors
const response = await fetch('/php_project%20-%20Copy/attendance_project/backend_php/api/face_recognition.php?action=faces');
        const apiEl = document.getElementById('apiStatus');

        if (response.ok) {
            const data = await response.json();
            apiEl.textContent = 'Online';
            apiEl.className = 'px-3 py-1 bg-green-500 text-white text-sm rounded-full';
            console.log('Face recognition service:', data);

            if (data.data && data.data.students_loaded) {
                showToast('Service Connected', `PHP Face Recognition API - ${data.data.students_loaded} students loaded`, 'info');
            } else {
                showToast('Service Connected', 'PHP Face Recognition API - Ready', 'info');
            }
        } else {
            apiEl.textContent = 'Error';
            apiEl.className = 'px-3 py-1 bg-red-500 text-white text-sm rounded-full';
            showToast('Service Error', 'Face recognition service returned an error', 'error');
        }
    } catch (err) {
        const apiEl = document.getElementById('apiStatus');
        apiEl.textContent = 'Offline';
        apiEl.className = 'px-3 py-1 bg-red-500 text-white text-sm rounded-full';
        console.error('Face recognition service error:', err);
        showToast('Service Unavailable', 'Face recognition service is not available', 'warning');
    }
}
