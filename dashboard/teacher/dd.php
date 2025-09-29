<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisionNex Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar-transition { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); transition: transform 0.2s ease; }
        .camera-container { position: relative; overflow: hidden; border-radius: 12px; }
        .camera-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-40 sidebar-transition">
        <div class="p-6 border-b">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">VisionNex</h1>
                    <p class="text-sm text-gray-500">Teacher Panel</p>
                </div>
            </div>
        </div>
        
        <nav class="mt-6">
            <a href="#dashboard" onclick="showSection('dashboard')" class="nav-item flex items-center px-6 py-3 text-green-600 bg-green-50 border-r-2 border-green-600">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="#attendance" onclick="showSection('attendance')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-clipboard-check mr-3"></i> Take Attendance
            </a>
            <a href="#classes" onclick="showSection('classes')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-door-open mr-3"></i> My Classes
            </a>
            <a href="#topics" onclick="showSection('topics')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-book mr-3"></i> Topics & Syllabus
            </a>
            <a href="#reports" onclick="showSection('reports')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-line mr-3"></i> Reports
            </a>
        </nav>
        
        <div class="absolute bottom-4 left-6 right-6">
            <button onclick="logout()" class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 id="pageTitle" class="text-2xl font-semibold text-gray-800">Dashboard</h2>
                    <p class="text-gray-600">Welcome back, <span id="teacherName">Teacher</span></p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <img id="teacherAvatar" src="https://images.pexels.com/photos/1040881/pexels-photo-1040881.jpeg?auto=compress&cs=tinysrgb&w=150" alt="Teacher" class="w-8 h-8 rounded-full">
                            <span class="text-gray-700">Teacher Panel</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Section -->
        <div id="dashboard-section" class="section-content p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">My Classes</p>
                            <h3 id="totalClasses" class="text-3xl font-bold">0</h3>
                        </div>
                        <i class="fas fa-door-open text-4xl text-green-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100">Total Students</p>
                            <h3 id="totalStudents" class="text-3xl font-bold">0</h3>
                        </div>
                        <i class="fas fa-user-graduate text-4xl text-blue-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">Today's Classes</p>
                            <h3 id="todayClasses" class="text-3xl font-bold">0</h3>
                        </div>
                        <i class="fas fa-calendar-day text-4xl text-purple-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100">Attendance Rate</p>
                            <h3 id="attendanceRate" class="text-3xl font-bold">0%</h3>
                        </div>
                        <i class="fas fa-percentage text-4xl text-orange-200"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule and Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Today's Schedule -->
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Today's Schedule</h3>
                    <div id="todaySchedule" class="space-y-4">
                        <!-- Schedule items will be loaded dynamically -->
                    </div>
                </div>
                
                <!-- Attendance Chart -->
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Weekly Attendance</h3>
                    <canvas id="attendanceChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <button onclick="showSection('attendance')" class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow text-left">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Take Attendance</h4>
                            <p class="text-gray-600 text-sm">Mark student attendance</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="showSection('topics')" class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow text-left">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Log Topics</h4>
                            <p class="text-gray-600 text-sm">Track syllabus progress</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="showSection('reports')" class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow text-left">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">View Reports</h4>
                            <p class="text-gray-600 text-sm">Student performance</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Attendance Section -->
        <div id="attendance-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">Take Attendance</h3>
                    <p class="text-gray-600">Choose a class and method to mark attendance</p>
                </div>
                
                <!-- Class Selection -->
                <div class="p-6 border-b bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                            <select id="attendanceClassSelect" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Choose a class...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" id="attendanceDate" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                    
                    <!-- Attendance Method Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attendance Method</label>
                        <div class="flex space-x-4">
                            <button id="faceRecognitionBtn" onclick="selectAttendanceMethod('face')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-camera mr-2"></i>Face Recognition
                            </button>
                            <button id="manualBtn" onclick="selectAttendanceMethod('manual')" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-clipboard mr-2"></i>Manual Entry
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Face Recognition Interface -->
                <div id="faceRecognitionInterface" class="p-6 hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Camera Feed</h4>
                            <div class="camera-container bg-gray-900 aspect-video">
                                <video id="cameraFeed" autoplay muted class="w-full h-full object-cover"></video>
                                <div id="cameraOverlay" class="camera-overlay hidden">
                                    <div class="text-white text-center">
                                        <i class="fas fa-camera-retro text-4xl mb-2"></i>
                                        <p>Position face in frame and press capture</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-center mt-4 space-x-4">
                                <button id="startCameraBtn" onclick="startCamera()" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    <i class="fas fa-video mr-2"></i>Start Camera
                                </button>
                                <button id="captureBtn" onclick="captureImage()" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 hidden">
                                    <i class="fas fa-camera mr-2"></i>Capture & Recognize
                                </button>
                                <button id="stopCameraBtn" onclick="stopCamera()" class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 hidden">
                                    <i class="fas fa-stop mr-2"></i>Stop Camera
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Recognition Results</h4>
                            <div id="recognitionResults" class="space-y-4">
                                <div class="text-center text-gray-500 py-8">
                                    <i class="fas fa-user-check text-4xl mb-2"></i>
                                    <p>No recognition attempts yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Manual Attendance Interface -->
                <div id="manualAttendanceInterface" class="p-6">
                    <div id="studentAttendanceList" class="space-y-2">
                        <!-- Student list will be populated here -->
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button onclick="saveManualAttendance()" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                            <i class="fas fa-save mr-2"></i>Save Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Section -->
        <div id="classes-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">My Classes</h3>
                    <p class="text-gray-600">Manage your assigned classes</p>
                </div>
                <div id="classesList" class="p-6">
                    <!-- Classes will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Topics Section -->
        <div id="topics-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Topics & Syllabus</h3>
                            <p class="text-gray-600">Track what you've covered in class</p>
                        </div>
                        <button onclick="showAddTopicModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-plus mr-2"></i>Log Topic
                        </button>
                    </div>
                </div>
                <div id="topicsList" class="p-6">
                    <!-- Topics will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div id="reports-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">Student Reports</h3>
                    <p class="text-gray-600">View attendance and performance analytics</p>
                </div>
                <div id="reportsContent" class="p-6">
                    <!-- Reports will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Topic Modal -->
    <div id="addTopicModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Log Covered Topic</h3>
                <button onclick="hideAddTopicModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="addTopicForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                    <select name="class_id" id="topicClassSelect" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a class...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Topic Title</label>
                    <input type="text" name="topic_title" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Covered</label>
                        <input type="date" name="date_covered" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="60" min="1" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resources/Notes</label>
                    <textarea name="notes" rows="3" placeholder="Any resources, notes, or additional information..." class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="hideAddTopicModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Log Topic
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="teacher.js"></script>
</body>
</html>