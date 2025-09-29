<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisionNex Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar-transition { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); transition: transform 0.2s ease; }
        .progress-bar { background: linear-gradient(90deg, #3B82F6 0%, #8B5CF6 100%); }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="ml-64">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 id="pageTitle" class="text-2xl font-semibold text-gray-800">Dashboard</h2>
                    <p class="text-gray-600">Welcome back, <span id="studentName">Student</span></p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center space-x-2 bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <img id="studentAvatar" src="https://images.pexels.com/photos/1040881/pexels-photo-1040881.jpeg?auto=compress&cs=tinysrgb&w=150" alt="Student" class="w-8 h-8 rounded-full">
                            <span class="text-gray-700">Student Portal</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Section -->
        <div id="dashboard-section" class="section-content p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100">Overall Attendance</p>
                            <h3 id="overallAttendance" class="text-3xl font-bold">0%</h3>
                        </div>
                        <i class="fas fa-percentage text-4xl text-blue-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">Classes Today</p>
                            <h3 id="classesToday" class="text-3xl font-bold">0</h3>
                        </div>
                        <i class="fas fa-calendar-day text-4xl text-green-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">Enrolled Classes</p>
                            <h3 id="enrolledClasses" class="text-3xl font-bold">0</h3>
                        </div>
                        <i class="fas fa-door-open text-4xl text-purple-200"></i>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white card-hover shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100">Syllabus Progress</p>
                            <h3 id="syllabusProgress" class="text-3xl font-bold">0%</h3>
                        </div>
                        <i class="fas fa-book-open text-4xl text-orange-200"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule and Attendance Chart -->
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

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div id="recentActivity" class="space-y-4">
                    <!-- Recent activities will be loaded dynamically -->
                </div>
            </div>
        </div>

        <!-- Attendance Section -->
        <div id="attendance-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">My Attendance Records</h3>
                    <p class="text-gray-600">View your attendance history and statistics</p>
                </div>
                
                <!-- Attendance Filters -->
                <div class="p-6 border-b bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <select id="subjectFilter" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Subjects</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" id="startDateFilter" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" id="endDateFilter" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button onclick="filterAttendance()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <button onclick="resetAttendanceFilter()" class="ml-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-times mr-2"></i>Reset
                        </button>
                    </div>
                </div>
                
                <!-- Attendance Summary -->
                <div class="p-6 border-b">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div id="presentDays" class="text-3xl font-bold text-green-600">0</div>
                            <div class="text-sm text-gray-600">Present</div>
                        </div>
                        <div class="text-center">
                            <div id="absentDays" class="text-3xl font-bold text-red-600">0</div>
                            <div class="text-sm text-gray-600">Absent</div>
                        </div>
                        <div class="text-center">
                            <div id="lateDays" class="text-3xl font-bold text-yellow-600">0</div>
                            <div class="text-sm text-gray-600">Late</div>
                        </div>
                        <div class="text-center">
                            <div id="attendancePercentage" class="text-3xl font-bold text-blue-600">0%</div>
                            <div class="text-sm text-gray-600">Attendance Rate</div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance Records Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Attendance records will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Classes Section -->
        <div id="classes-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">My Classes</h3>
                    <p class="text-gray-600">View your enrolled classes and schedules</p>
                </div>
                <div id="classesContent" class="p-6">
                    <!-- Classes will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Syllabus Section -->
        <div id="syllabus-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">Syllabus Progress</h3>
                    <p class="text-gray-600">Track what has been covered in your classes</p>
                </div>
                <div id="syllabusContent" class="p-6">
                    <!-- Syllabus progress will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Performance Section -->
        <div id="performance-section" class="section-content p-6 hidden">
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-800">Performance Analytics</h3>
                    <p class="text-gray-600">View your academic performance metrics</p>
                </div>
                <div id="performanceContent" class="p-6">
                    <!-- Performance analytics will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="student.js"></script>
</body>
</html>