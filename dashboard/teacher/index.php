<?php
$pageTitle = "Teacher Dashboard";
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include_once '../components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto lg:ml-64">
        <!-- Header -->
        <header class="bg-gray-100 dark:bg-gray-800 shadow-sm z-10">
            <div class="px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold"><?= $pageTitle ?></h1>
            </div>
        </header>

        <!-- Main -->
        <main class="p-6 space-y-6">

            <!-- Quick Actions -->
             <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Quick Actions</h2>
    

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <a href="mark_attendance.php" class="p-4 bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 rounded-lg shadow transition flex flex-col items-start">
            <i class="fas fa-clipboard-check text-2xl text-blue-600 dark:text-blue-400 mb-2"></i>
            <h4 class="font-semibold text-gray-800 dark:text-gray-100">Mark Attendance</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">Take attendance for your classes</p>
        </a>
        <a href="schedule.php" class="p-4 bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800 rounded-lg shadow transition flex flex-col items-start">
            <i class="fas fa-calendar text-2xl text-green-600 dark:text-green-400 mb-2"></i>
            <h4 class="font-semibold text-gray-800 dark:text-gray-100">View Schedule</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">Check your timetable</p>
        </a>
        <a href="profile.php" class="p-4 bg-purple-50 dark:bg-purple-900 hover:bg-purple-100 dark:hover:bg-purple-800 rounded-lg shadow transition flex flex-col items-start">
            <i class="fas fa-user text-2xl text-purple-600 dark:text-purple-400 mb-2"></i>
            <h4 class="font-semibold text-gray-800 dark:text-gray-100">My Profile</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">Update your information</p>
        </a>
    </div>
    </div>

    
            <!-- Charts Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Classes per Subject (Bar Chart) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Classes per Subject</h2>
                    <canvas id="classesChart" class="w-full h-64"></canvas>
                </div>

                <!-- Students per Course (Pie Chart) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Students per Course</h2>
                    <canvas id="studentsChart" class="w-full h-64"></canvas>
                </div>

            </div>

            <!-- Activity Section (Hardcoded) -->
            <!-- Recent Activities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h3 class="text-lg font-medium text-gray-800 dark:text-gray-100 mb-4">Recent Activities</h3>
        <ul class="space-y-4">
            <li class="flex items-start p-3 border-l-4 border-green-500 bg-green-50 dark:bg-green-900 rounded-r-lg">
                <i class="fas fa-user-plus text-green-600 dark:text-green-400 mr-3 mt-1"></i>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">New student registered</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rahul Sharma joined BCA Division A</p>
                </div>
            </li>
            <li class="flex items-start p-3 border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900 rounded-r-lg">
                <i class="fas fa-book text-blue-600 dark:text-blue-400 mr-3 mt-1"></i>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Course updated</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Data Structures syllabus updated</p>
                </div>
            </li>
        </ul>
    </div>

        </main>
    </div>
</div>

<!-- Charts Script -->
<script>
const classesCtx = document.getElementById('classesChart').getContext('2d');
const classesChart = new Chart(classesCtx, {
    type: 'bar',
    data: {
        labels: [], // Populate dynamically from DB
        datasets: [{
            label: 'Classes Taken',
            data: [], // Populate dynamically from DB
            backgroundColor: 'rgba(99, 102, 241, 0.6)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});

const studentsCtx = document.getElementById('studentsChart').getContext('2d');
const studentsChart = new Chart(studentsCtx, {
    type: 'pie',
    data: {
        labels: [], // Populate dynamically from DB
        datasets: [{
            label: 'Students',
            data: [], // Populate dynamically from DB
            backgroundColor: [
                'rgba(147, 197, 253, 0.7)',
                'rgba(251, 191, 36, 0.7)',
                'rgba(248, 113, 113, 0.7)',
                'rgba(134, 239, 172, 0.7)'
            ],
            borderWidth: 1
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

</body>
</html>
