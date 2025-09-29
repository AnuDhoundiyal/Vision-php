<?php
require_once "config/config.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Mode - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        accent: '#8B5CF6',
                        dark: '#1E293B',
                        light: '#F8FAFC'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .camera-container {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .camera-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #3B82F6;
            border-radius: 1rem;
            z-index: 10;
            pointer-events: none;
        }
        
        .scanning-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(to right, transparent, #3B82F6, transparent);
            width: 100%;
            top: 50%;
            animation: scan 2s linear infinite;
            box-shadow: 0 0 8px #3B82F6;
        }
        
        @keyframes scan {
            0% { top: 5%; }
            50% { top: 95%; }
            100% { top: 5%; }
        }
        
        .corner {
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: #3B82F6;
            border-width: 4px;
            z-index: 20;
        }
        
        .corner-top-left {
            top: 0;
            left: 0;
            border-top: 4px solid #3B82F6;
            border-left: 4px solid #3B82F6;
            border-bottom: none;
            border-right: none;
            border-top-left-radius: 0.5rem;
        }
        
        .corner-top-right {
            top: 0;
            right: 0;
            border-top: 4px solid #3B82F6;
            border-right: 4px solid #3B82F6;
            border-bottom: none;
            border-left: none;
            border-top-right-radius: 0.5rem;
        }
        
        .corner-bottom-left {
            bottom: 0;
            left: 0;
            border-bottom: 4px solid #3B82F6;
            border-left: 4px solid #3B82F6;
            border-top: none;
            border-right: none;
            border-bottom-left-radius: 0.5rem;
        }
        
        .corner-bottom-right {
            bottom: 0;
            right: 0;
            border-bottom: 4px solid #3B82F6;
            border-right: 4px solid #3B82F6;
            border-top: none;
            border-left: none;
            border-bottom-right-radius: 0.5rem;
        }
        
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <?php include 'components/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">VisionNex <span class="text-primary">Kiosk</span> Mode</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">Experience our facial recognition attendance system in action. Stand in front of the camera to check in.</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Camera Feed Section -->
                <div class="camera-container bg-white dark:bg-gray-800 p-4 aspect-video">
                    <div class="relative w-full h-full bg-black rounded-lg overflow-hidden">
                        <!-- Video element will be inserted here by JavaScript -->
                        <video id="camera-feed" class="w-full h-full object-cover" autoplay playsinline></video>
                        
                        <!-- Camera Overlay with scanning effect -->
                        <div class="camera-overlay">
                            <div class="scanning-line"></div>
                            <div class="corner corner-top-left"></div>
                            <div class="corner corner-top-right"></div>
                            <div class="corner corner-bottom-left"></div>
                            <div class="corner corner-bottom-right"></div>
                        </div>
                        
                        <!-- Status Indicator -->
                        <div id="status-indicator" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-900/80 text-white px-4 py-2 rounded-full text-sm font-medium hidden">
                            <span class="flex items-center">
                                <span id="status-icon" class="mr-2"><i class="fas fa-spinner fa-spin"></i></span>
                                <span id="status-text">Initializing camera...</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Recognition Results Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-id-card text-primary mr-3"></i>
                        Recognition Results
                    </h2>
                    
                    <!-- Initial State -->
                    <div id="initial-state" class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-user text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">Stand in front of the camera to begin</p>
                    </div>
                    
                    <!-- Processing State (Hidden initially) -->
                    <div id="processing-state" class="text-center py-12 hidden">
                        <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full mx-auto mb-6 flex items-center justify-center pulse">
                            <i class="fas fa-spinner fa-spin text-4xl text-primary"></i>
                        </div>
                        <p class="text-primary text-lg font-medium">Processing...</p>
                    </div>
                    
                    <!-- Success State (Hidden initially) -->
                    <div id="success-state" class="hidden">
                        <div class="flex items-start mb-6">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-check text-3xl text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="user-name">John Doe</h3>
                                <p class="text-gray-600 dark:text-gray-400" id="user-role">Student</p>
                                <div class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Successfully recognized
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">ID Number</p>
                                    <p class="font-medium text-gray-900 dark:text-white" id="user-id">STU12345</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                                    <p class="font-medium text-gray-900 dark:text-white" id="user-department">Computer Science</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Check-in Time</p>
                                    <p class="font-medium text-gray-900 dark:text-white" id="checkin-time">09:15 AM</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                    <p class="font-medium text-green-600 dark:text-green-400" id="attendance-status">On Time</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button id="reset-btn" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out">
                                <i class="fas fa-redo mr-2"></i> Reset
                            </button>
                        </div>
                    </div>
                    
                    <!-- Error State (Hidden initially) -->
                    <div id="error-state" class="text-center py-12 hidden">
                        <div class="w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-4xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <p class="text-red-600 dark:text-red-400 text-lg font-medium mb-2" id="error-message">Face not recognized</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Please try again or contact administrator</p>
                        
                        <div class="mt-6">
                            <button id="retry-btn" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out">
                                <i class="fas fa-redo mr-2"></i> Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Demo Controls -->
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Demo Controls</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">For demonstration purposes, you can simulate different recognition scenarios:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button id="demo-success" class="bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-800/30 text-green-800 dark:text-green-300 font-medium py-3 px-4 rounded-lg transition duration-300 ease-in-out flex items-center justify-center">
                        <i class="fas fa-check-circle mr-2"></i> Simulate Success
                    </button>
                    
                    <button id="demo-processing" class="bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-800/30 text-blue-800 dark:text-blue-300 font-medium py-3 px-4 rounded-lg transition duration-300 ease-in-out flex items-center justify-center">
                        <i class="fas fa-spinner mr-2"></i> Simulate Processing
                    </button>
                    
                    <button id="demo-error" class="bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-800/30 text-red-800 dark:text-red-300 font-medium py-3 px-4 rounded-lg transition duration-300 ease-in-out flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Simulate Error
                    </button>
                </div>
            </div>
            
            <!-- Demo Login Section -->
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Demo Login</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Use these demo credentials to explore different user dashboards:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Admin Login -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-shield text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Admin</h3>
                        </div>
                        <div class="space-y-2 text-sm mb-4">
                            <p><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium text-gray-900 dark:text-white">admin@example.com</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">Password:</span> <span class="font-medium text-gray-900 dark:text-white">password123</span></p>
                        </div>
                        <a href="login.php" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out">
                            Login as Admin
                        </a>
                    </div>
                    
                    <!-- Teacher Login -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-chalkboard-teacher text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Teacher</h3>
                        </div>
                        <div class="space-y-2 text-sm mb-4">
                            <p><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium text-gray-900 dark:text-white">teacher@example.com</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">Password:</span> <span class="font-medium text-gray-900 dark:text-white">password123</span></p>
                        </div>
                        <a href="login.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out">
                            Login as Teacher
                        </a>
                    </div>
                    
                    <!-- Student Login -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-graduate text-green-600 dark:text-green-400"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Student</h3>
                        </div>
                        <div class="space-y-2 text-sm mb-4">
                            <p><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium text-gray-900 dark:text-white">student@example.com</span></p>
                            <p><span class="text-gray-500 dark:text-gray-400">Password:</span> <span class="font-medium text-gray-900 dark:text-white">password123</span></p>
                        </div>
                        <a href="login.php" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out">
                            Login as Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'components/footer.php'; ?>

    <script>
        // Theme toggle functionality
        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });
        
        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
        
        // Demo functionality
        document.addEventListener('DOMContentLoaded', function() {
            const initialState = document.getElementById('initial-state');
            const processingState = document.getElementById('processing-state');
            const successState = document.getElementById('success-state');
            const errorState = document.getElementById('error-state');
            const statusIndicator = document.getElementById('status-indicator');
            
            // Demo buttons
            document.getElementById('demo-success').addEventListener('click', function() {
                initialState.classList.add('hidden');
                processingState.classList.remove('hidden');
                errorState.classList.add('hidden');
                successState.classList.add('hidden');
                
                statusIndicator.classList.remove('hidden');
                document.getElementById('status-text').textContent = 'Processing...';
                
                setTimeout(function() {
                    processingState.classList.add('hidden');
                    successState.classList.remove('hidden');
                    statusIndicator.classList.add('hidden');
                    
                    // Set demo data
                    document.getElementById('user-name').textContent = 'John Smith';
                    document.getElementById('user-role').textContent = 'Student';
                    document.getElementById('user-id').textContent = 'STU12345';
                    document.getElementById('user-department').textContent = 'Computer Science';
                    document.getElementById('checkin-time').textContent = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    document.getElementById('attendance-status').textContent = 'On Time';
                }, 2000);
            });
            
            document.getElementById('demo-processing').addEventListener('click', function() {
                initialState.classList.add('hidden');
                processingState.classList.remove('hidden');
                errorState.classList.add('hidden');
                successState.classList.add('hidden');
                
                statusIndicator.classList.remove('hidden');
                document.getElementById('status-text').textContent = 'Processing...';
            });
            
            document.getElementById('demo-error').addEventListener('click', function() {
                initialState.classList.add('hidden');
                processingState.classList.remove('hidden');
                errorState.classList.add('hidden');
                successState.classList.add('hidden');
                
                statusIndicator.classList.remove('hidden');
                document.getElementById('status-text').textContent = 'Processing...';
                
                setTimeout(function() {
                    processingState.classList.add('hidden');
                    errorState.classList.remove('hidden');
                    statusIndicator.classList.add('hidden');
                    document.getElementById('error-message').textContent = 'Face not recognized';
                }, 2000);
            });
            
            // Reset button
            document.getElementById('reset-btn').addEventListener('click', function() {
                successState.classList.add('hidden');
                initialState.classList.remove('hidden');
            });
            
            // Retry button
            document.getElementById('retry-btn').addEventListener('click', function() {
                errorState.classList.add('hidden');
                initialState.classList.remove('hidden');
            });
            
            // Initialize camera if available
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                statusIndicator.classList.remove('hidden');
                
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        const video = document.getElementById('camera-feed');
                        video.srcObject = stream;
                        statusIndicator.classList.add('hidden');
                    })
                    .catch(function(error) {
                        console.error('Camera error:', error);
                        statusIndicator.classList.remove('hidden');
                        document.getElementById('status-icon').innerHTML = '<i class="fas fa-exclamation-triangle text-red-500"></i>';
                        document.getElementById('status-text').textContent = 'Camera access denied';
                    });
            } else {
                statusIndicator.classList.remove('hidden');
                document.getElementById('status-icon').innerHTML = '<i class="fas fa-exclamation-triangle text-red-500"></i>';
                document.getElementById('status-text').textContent = 'Camera not supported';
            }
        });
    </script>
</body>
</html>