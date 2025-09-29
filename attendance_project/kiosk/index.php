<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisionNex Kiosk - Face Recognition Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .kiosk-bg {
            background: linear-gradient(135deg, #0f1a62ff 0%, #060d5fff 100%);
            min-height: 100vh;
        }
        .camera-container {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .recognition-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ff00, transparent);
            animation: scan 3s linear infinite;
        }
        @keyframes scan {
            0% { top: 0; }
            100% { top: 100%; }
        }
        .success-animation {
            animation: successPulse 0.6s ease-out;
        }
        @keyframes successPulse {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .floating-card {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="kiosk-bg">
    <!-- Header -->
    <header class="bg-white/10 backdrop-blur-md border-b border-white/20 p-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-desktop text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">VisionNex Kiosk</h1>
                    <p class="text-blue-200 text-sm">Face Recognition Attendance System</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-white text-right">
                    <div id="currentTime" class="text-lg font-semibold"></div>
                    <div id="currentDate" class="text-sm text-blue-200"></div>
                </div>
                <button onclick="showSettingsModal()" class="bg-white/20 hover:bg-white/30 text-white p-3 rounded-xl transition-colors">
                    <i class="fas fa-cog"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Camera Section -->
            <div class="lg:col-span-2">
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                    <div class="text-center mb-6">
                        <h2 class="text-3xl font-bold text-white mb-2">Face Recognition Scanner</h2>
                        <p class="text-blue-200">Position your face in the camera frame for attendance marking</p>
                    </div>
                    
                    <!-- Camera Feed -->
                    <div class="camera-container bg-gray-900 aspect-video mb-6 relative">
                        <video id="cameraFeed" autoplay muted class="w-full h-full object-cover"></video>
                        
                        <!-- Camera Overlay States -->
                        <div id="cameraOffOverlay" class="recognition-overlay">
                            <div class="text-center text-white">
                                <i class="fas fa-camera-retro text-6xl mb-4 opacity-50"></i>
                                <h3 class="text-2xl font-semibold mb-2">Camera Ready</h3>
                                <p class="text-blue-200">Click "Start Camera" to begin face recognition</p>
                            </div>
                        </div>
                        
                        <div id="scanningOverlay" class="recognition-overlay hidden">
                            <div class="text-center text-white">
                                <div class="relative mb-4">
                                    <i class="fas fa-user-circle text-6xl pulse-animation"></i>
                                    <div class="scan-line"></div>
                                </div>
                                <h3 class="text-2xl font-semibold mb-2">Scanning Face...</h3>
                                <p class="text-blue-200">Please hold still</p>
                            </div>
                        </div>
                        
                        <div id="successOverlay" class="recognition-overlay hidden">
                            <div class="text-center text-white success-animation">
                                <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
                                <h3 class="text-2xl font-semibold mb-2">Recognition Successful!</h3>
                                <p id="recognizedUser" class="text-green-300 text-lg"></p>
                            </div>
                        </div>
                        
                        <div id="errorOverlay" class="recognition-overlay hidden">
                            <div class="text-center text-white">
                                <i class="fas fa-times-circle text-6xl text-red-400 mb-4"></i>
                                <h3 class="text-2xl font-semibold mb-2">Recognition Failed</h3>
                                <p class="text-red-300">Face not recognized or not registered</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Camera Controls -->
                    <div class="flex justify-center space-x-4">
                        <button id="startCameraBtn" onclick="startCamera()" class="bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors">
                            <i class="fas fa-video mr-2"></i>Start Camera
                        </button>
                        <button id="stopCameraBtn" onclick="stopCamera()" class="bg-red-500 hover:bg-red-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors hidden">
                            <i class="fas fa-stop mr-2"></i>Stop Camera
                        </button>
                        <button id="captureBtn" onclick="captureAndRecognize()" class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-colors hidden">
                            <i class="fas fa-camera mr-2"></i>Capture
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Status Panel -->
            <div class="space-y-6">
                <!-- Current Status -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 floating-card">
                    <h3 class="text-xl font-semibold text-white mb-4">System Status</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-blue-200">Camera</span>
                            <span id="cameraStatus" class="px-3 py-1 bg-red-500 text-white text-sm rounded-full">Offline</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-blue-200">Recognition API</span>
                            <span id="apiStatus" class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-full">Checking...</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-blue-200">Database</span>
                            <span id="dbStatus" class="px-3 py-1 bg-green-500 text-white text-sm rounded-full">Online</span>
                        </div>
                    </div>
                </div>
                
                <!-- Today's Stats -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 floating-card" style="animation-delay: -2s;">
                    <h3 class="text-xl font-semibold text-white mb-4">Today's Activity</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <div id="todayRecognitions" class="text-3xl font-bold text-white">0</div>
                            <div class="text-blue-200 text-sm">Recognitions</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div id="successfulRecognitions" class="text-xl font-semibold text-green-300">0</div>
                                <div class="text-blue-200 text-xs">Successful</div>
                            </div>
                            <div>
                                <div id="failedRecognitions" class="text-xl font-semibold text-red-300">0</div>
                                <div class="text-blue-200 text-xs">Failed</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 floating-card" style="animation-delay: -4s;">
                    <h3 class="text-xl font-semibold text-white mb-4">Recent Activity</h3>
                    <div id="recentActivity" class="space-y-3">
                        <div class="text-center text-blue-200 py-4">
                            <i class="fas fa-clock text-2xl mb-2"></i>
                            <p class="text-sm">No recent activity</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Settings Modal -->
    <div id="settingsModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Kiosk Settings</h3>
                <button onclick="hideSettingsModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recognition Confidence Threshold</label>
                    <input type="range" id="confidenceThreshold" min="0.5" max="1.0" step="0.05" value="0.9" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>50%</span>
                        <span id="confidenceValue">90%</span>
                        <span>100%</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto-capture Delay (seconds)</label>
                    <select id="autoCaptureDelay" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="0">Disabled</option>
                        <option value="3">3 seconds</option>
                        <option value="5" selected>5 seconds</option>
                        <option value="10">10 seconds</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Class (Optional)</label>
                    <select id="defaultClass" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">No default class</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="enableSound" checked class="mr-2">
                    <label for="enableSound" class="text-sm text-gray-700">Enable sound notifications</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="enableAutoCapture" class="mr-2">
                    <label for="enableAutoCapture" class="text-sm text-gray-700">Enable auto-capture when face detected</label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 mt-8">
                <button onclick="hideSettingsModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="saveSettings()" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Save Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Success/Error Toast -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-4 max-w-sm">
            <div class="flex items-center">
                <div id="toastIcon" class="flex-shrink-0 mr-3"></div>
                <div>
                    <div id="toastTitle" class="font-semibold text-gray-800"></div>
                    <div id="toastMessage" class="text-sm text-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="kiosk.js"></script>
</body>
</html>