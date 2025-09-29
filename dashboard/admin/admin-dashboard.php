```php
<?php
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access

// Get current user's profile image for sidebar
$user_profile_image = $_SESSION['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?> - VisionNex ERA</title>

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#4f46e5',
        secondary: '#7c3aed',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#3b82f6',
        lightGray: '#f3f4f6',
        darkGray: '#1f2937'
      },
      fontFamily: { sans: ['Inter','sans-serif'] }
    }
  }
}
</script>

<!-- Font Awesome & Chart.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.card-hover:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.12); transition: 0.3s; }
.chart-white-bg { background-color: #ffffff !important; }
</style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
<?php include_once __DIR__ . '/../components/sidebar.php'; ?>

<div class="md:ml-64 transition-all duration-300">
  <div class="container px-4 py-6 space-y-10">

    <!-- Page Title + Date -->
    <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <h1 class="text-3xl font-bold tracking-tight"><?= $pageTitle ?></h1>
      <div class="text-lg font-medium text-gray-600 dark:text-gray-300">
        <?= date("l, d F Y") ?>
      </div>
    </header>

    <!-- Quick Stats -->
    <section id="quick-stats">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-5 border-l-4 border-indigo-400">
          <h3 class="text-sm text-gray-500">Total Students</h3>
          <div class="mt-2 text-2xl font-semibold" id="total-students">--</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-5 border-l-4 border-blue-400">
          <h3 class="text-sm text-gray-500">Total Teachers</h3>
          <div class="mt-2 text-2xl font-semibold" id="total-teachers">--</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-5 border-l-4 border-purple-400">
          <h3 class="text-sm text-gray-500">Active Courses</h3>
          <div class="mt-2 text-2xl font-semibold" id="active-courses">--</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-5 border-l-4 border-amber-400">
          <h3 class="text-sm text-gray-500">Today's Attendance</h3>
          <div class="mt-2 text-2xl font-semibold" id="todays-attendance">--%</div>
        </div>
      </div>
    </section>

    <!-- Charts -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-6">
        <h3 class="mb-4 font-semibold text-lg">Enrollment Metrics</h3>
        <canvas id="enrollment-chart" class="h-64"></canvas>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-6">
        <h3 class="mb-4 font-semibold text-lg">Attendance Statistics</h3>
        <canvas id="attendance-chart" class="h-64 chart-white-bg"></canvas>
      </div>
    </section>

    <!-- Recent Activities -->
    <section>
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md card-hover p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium">Recent Activities</h3>
          <a href="#" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View All</a>
        </div>
        <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
          <?php
          // Example: multiple recent activities with different colors
          $activities = [
            ['title'=>'New student registered','desc'=>'Rahul Sharma joined BCA Division A','time'=>'10 minutes ago','color'=>'green'],
            ['title'=>'Teacher added','desc'=>'Mr. Joshi joined BCA faculty','time'=>'30 minutes ago','color'=>'blue'],
            ['title'=>'Course updated','desc'=>'Python course schedule updated','time'=>'1 hour ago','color'=>'purple'],
            ['title'=>'Attendance marked','desc'=>'Today\'s attendance updated','time'=>'2 hours ago','color'=>'amber']
          ];
          $colorMap = ['green'=>'green','blue'=>'blue','purple'=>'purple','amber'=>'amber'];
          foreach($activities as $act): ?>
          <div class="flex items-start p-3 border-l-4 border-<?= $colorMap[$act['color']] ?>-400 bg-<?= $colorMap[$act['color']] ?>-50 dark:bg-<?= $colorMap[$act['color']] ?>-900/20 rounded-r-lg">
            <div class="flex-shrink-0 mr-3">
              <div class="w-10 h-10 rounded-full bg-<?= $colorMap[$act['color']] ?>-100 dark:bg-<?= $colorMap[$act['color']] ?>-900/30 flex items-center justify-center">
                <i class="fas fa-circle text-<?= $colorMap[$act['color']] ?>-600 dark:text-<?= $colorMap[$act['color']] ?>-400"></i>
              </div>
            </div>
            <div>
              <p class="text-sm font-medium"><?= $act['title'] ?></p>
              <p class="text-xs text-gray-500 dark:text-gray-400"><?= $act['desc'] ?></p>
              <p class="text-xs text-gray-400 dark:text-gray-500"><?= $act['time'] ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  </div>
</div>
<div id="toast-container"></div>
<?php display_toast_from_session(); ?>
<script>
$(function(){
  // Quick Stats
  $.get("./api/get-stats.php", function(data){
    $("#total-students").text(data.students);
    $("#total-teachers").text(data.teachers);
    $("#active-courses").text(data.courses);
    $("#todays-attendance").text(data.attendance + "%");
  });

  // Chart pastel palette
  const pastel = ['#f9c5d1','#c5e1f9','#f9e0c5','#d9c5f9','#c5f9e0','#fde68a','#e0c5f9'];

  const enrollmentCtx = document.getElementById('enrollment-chart').getContext('2d');
  const attendanceCtx = document.getElementById('attendance-chart').getContext('2d');

  let enrollmentChart = new Chart(enrollmentCtx, {
    type: 'line',
    data: { labels:[], datasets:[] },
    options: {
      responsive:true,
      scales: { y:{ beginAtZero:true } },
      plugins:{ legend:{ display:true } },
      elements: { line:{ borderWidth:2, tension:0.4 }, point:{ radius:3 } }
    }
  });

  let attendanceChart = new Chart(attendanceCtx, {
    type: 'bar',
    data: { labels:[], datasets:[] },
    options: {
      responsive:true,
      scales: {
        x: { grid: { color:'#f3f4f6' } },
        y: { beginAtZero:true, grid: { color:'#f3f4f6' } }
      },
      plugins:{ legend:{ display:true } },
      elements:{ bar:{ borderRadius:6, barPercentage:0.5 } }
    }
  });

  // Load chart data
  $.get("./api/get-charts.php", function(data){
    if(data.enrollment.datasets){
      data.enrollment.datasets.forEach(ds => {
        ds.borderColor = pastel; // This will apply the whole array as one color, needs adjustment for multiple lines
        ds.backgroundColor = pastel; // Same here
      });
      enrollmentChart.data = data.enrollment;
      enrollmentChart.update();
    }
    if(data.attendance.datasets){
      data.attendance.datasets.forEach((ds,i)=>{
        ds.backgroundColor = pastel[i % pastel.length];
      });
      attendanceChart.data = data.attendance;
      attendanceChart.update();
    }
  });
});
</script>
</body>
</html>
```