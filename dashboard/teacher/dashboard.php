```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('teacher'); // Ensure only teachers can access

$pageTitle = "Teacher Dashboard";
$user_profile_image = $_SESSION['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - VisionNex ERA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar-transition { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); transition: transform 0.2s ease; }
        .progress-bar-fill { background: linear-gradient(90deg, #3B82F6 0%, #8B5CF6 100%); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="ml-64 transition-all duration-300">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 id="pageTitle" class="text-2xl font-semibold text-gray-800 dark:text-white">Dashboard</h2>
                    <p class="text-gray-600 dark:text-gray-400">Welcome back, <span id="teacherName" class="font-medium"><?= htmlspecialchars($_SESSION['username'] ?? 'Teacher') ?></span></p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <img id="teacherAvatar" src="<?= $user_profile_image ? $config['UPLOAD_DIR_REL'] . '/' . $user_profile_image : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username'] ?? 'Teacher') ?>" alt="Teacher" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-gray-700 dark:text-gray-300">Teacher Panel</span>
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
                            <p class="text-orange-100">Avg. Attendance</p>
                            <h3 id="attendanceRate" class="text-3xl font-bold">0%</h3>
                        </div>
                        <i class="fas fa-percentage text-4xl text-orange-200"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule and Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Today's Schedule -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Today's Schedule</h3>
                    <div id="todaySchedule" class="space-y-4">
                        <div class="text-center text-gray-500 dark:text-gray-400 py-4">Loading schedule...</div>
                    </div>
                </div>
                
                <!-- Attendance Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Weekly Attendance Overview</h3>
                    <canvas id="attendanceChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="mark_attendance.php" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow text-left">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Mark Attendance</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Take attendance for your classes</p>
                        </div>
                    </div>
                </a>
                
                <a href="my_classes.php" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow text-left">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-door-open text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">My Classes</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">View and manage assigned classes</p>
                        </div>
                    </div>
                </a>
                
                <a href="reports.php" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow text-left">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">View Reports</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Student attendance and performance</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- My Classes Section -->
        <div id="classes-section" class="section-content p-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">My Classes</h3>
                    <p class="text-gray-600 dark:text-gray-400">Overview of your assigned classes</p>
                </div>
                <div id="classesContent" class="p-6">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">Loading classes...</div>
                </div>
            </div>
        </div>

        <!-- Attendance Section -->
        <div id="attendance-section" class="section-content p-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Attendance Management</h3>
                    <p class="text-gray-600 dark:text-gray-400">Manage attendance for your classes</p>
                </div>
                <div id="attendanceContent" class="p-6">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">Loading attendance data...</div>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div id="reports-section" class="section-content p-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Reports</h3>
                    <p class="text-gray-600 dark:text-gray-400">Generate and view attendance reports</p>
                </div>
                <div id="reportsContent" class="p-6">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">Loading reports...</div>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div id="profile-section" class="section-content p-6 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white">My Profile</h3>
                    <p class="text-gray-600 dark:text-gray-400">Manage your personal and professional information</p>
                </div>
                <div id="profileContent" class="p-6">
                    <div class="text-center text-gray-500 dark:text-gray-400 py-4">Loading profile data...</div>
                </div>
            </div>
        </div>
    </div>
    <div id="toast-container"></div>
    <?php display_toast_from_session(); ?>
    <script src="teacher.js"></script>
</body>
</html>
```