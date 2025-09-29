<?php
require_once 'C:\xampp\htdocs\php_project\public\config\db.php';

/// Fetch departments
$departments = [];
$res = $conn->query("SELECT * FROM departments ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $departments[$row['id']] = $row;

// Fetch courses
$courses = [];
$res = $conn->query("SELECT * FROM courses ORDER BY name ASC");
while ($row = $res->fetch_assoc()) $courses[$row['id']] = $row;

// Fetch teachers
$teachers = [];
$res = $conn->query("SELECT t.*, d.name AS department_name
                     FROM teachers t
                     LEFT JOIN departments d ON t.id = d.id  -- No course_id, just join departments if needed
                     ORDER BY t.name ASC");
while ($row = $res->fetch_assoc()) $teachers[] = $row;

// Fetch Teachers with department only (no course_id)
$teachers = [];
$res = $conn->query("
    SELECT t.*, d.name AS department_name
    FROM teachers t
    LEFT JOIN departments d ON t.id = d.id  -- if you want, or just show 'No Department'
    ORDER BY t.name ASC
");
while ($row = $res->fetch_assoc()) $teachers[] = $row;


// Prepare chart data
// Teachers per department
$deptCounts = [];
foreach ($teachers as $t) {
    $dept = $t['department_name'] ?: 'No Department';
    if (!isset($deptCounts[$dept])) $deptCounts[$dept] = 0;
    $deptCounts[$dept]++;
}
$deptLabels = json_encode(array_keys($deptCounts));
$deptData = json_encode(array_values($deptCounts));

// Courses per department
$courseCounts = [];
foreach ($courses as $c) {
    $dept = $departments[$c['department_id']]['name'] ?? 'No Department';
    if (!isset($courseCounts[$dept])) $courseCounts[$dept] = 0;
    $courseCounts[$dept]++;
}
$courseLabels = json_encode(array_keys($courseCounts));
$courseData = json_encode(array_values($courseCounts));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teachers Management - VisionNex ERA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .modal-bg {
            backdrop-filter: blur(4px);
        }

        .card-purple {
            background-color: rgb(243, 232, 255);
        }

        .card-blue {
            background-color: rgb(232, 243, 255);
        }

        .card-pink {
            background-color: rgb(255, 232, 243);
        }

        .card-white {
            background-color: rgb(255, 255, 255);
        }

        /* Remove hover effect on card */
        .hover-card {
            transition: none;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include_once '../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto lg:ml-64 p-6 space-y-6">

            <h1 class="text-2xl font-bold mb-4">Teachers Management</h1>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover-card">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filters</h2>
                <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                        <select name="department" id="departmentFilter" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
                        <select name="course" id="courseFilter" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2 mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filter</button>
                        <button type="button" id="resetFilter" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Reset</button>
                    </div>
                </form>
            </div>

       <!-- Charts -->
<div class="grid md:grid-cols-2 gap-6">
  <div class="rounded-lg shadow p-6 bg-white dark:bg-gray-800 flex flex-col">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Courses per Department</h3>
    <div class="flex-1">
      <canvas id="courseBarChart" class="w-full h-80"></canvas>
    </div>
  </div>

  <div class="rounded-lg shadow p-6 bg-white dark:bg-gray-800 flex flex-col">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Teachers per Department</h3>
    <div class="flex-1">
      <canvas id="teacherPieChart" class="w-full h-80"></canvas>
    </div>
  </div>
</div>


            <!-- Teacher List -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover-card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Teacher List</h2>
                    <a href="add_teacher.php" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Add Teacher</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="teacherTable">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Profile</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Course / Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <img class="w-10 h-10 rounded-full object-cover" src="<?= $teacher['profile_image'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($teacher['name']); ?>" alt="Profile">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?= htmlspecialchars($teacher['name']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-300">
                                            <?= htmlspecialchars($teacher['course_name'] ?? 'No Course'); ?> / <?= htmlspecialchars($teacher['department_name'] ?? 'No Dept'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                            <button onclick="openEditModal(<?= $teacher['id']; ?>)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <button onclick="openDeleteModal(<?= $teacher['id']; ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-gray-500 dark:text-gray-400 py-4">No teachers found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center modal-bg z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Edit Teacher</h2>
            <form id="editForm" method="POST" action="edit_teacher.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="block mb-1 text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" id="edit_name" class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="mb-3">
                    <label class="block mb-1 text-gray-700 dark:text-gray-300">Profile Image</label>
                    <input type="file" name="profile_image" id="edit_image" class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center modal-bg z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-80">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Delete Teacher</h2>
            <p class="text-gray-600 dark:text-gray-300">Are you sure you want to delete this teacher?</p>
            <form id="deleteForm" method="POST" action="delete_teacher.php" class="mt-4">
                <input type="hidden" name="id" id="delete_id">
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Charts
       // Courses per Department - Bar Chart
const ctxBar = document.getElementById('courseBarChart').getContext('2d');
const ctxPie = document.getElementById('teacherPieChart').getContext('2d');

new Chart(ctxBar, {
  type: 'bar',
  data: {
    labels: <?= $courseLabels ?>,
    datasets: [{
      label: 'Courses per Department',
      data: <?= $courseData ?>,
      backgroundColor: [
        'rgba(155, 89, 182, 0.8)',
        'rgba(52, 152, 219, 0.8)',
        'rgba(231, 76, 60, 0.8)',
        'rgba(46, 204, 113, 0.8)',
        'rgba(241, 196, 15, 0.8)'
      ],
      hoverBackgroundColor: [
        'rgba(155, 89, 182, 1)',
        'rgba(52, 152, 219, 1)',
        'rgba(231, 76, 60, 1)',
        'rgba(46, 204, 113, 1)',
        'rgba(241, 196, 15, 1)'
      ],
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { 
      legend: { display: false },
      tooltip: { enabled: true }
    },
    scales: { 
      y: { 
        beginAtZero: true, 
        ticks: { stepSize: 1 },
        grid: { display: true, color: 'rgba(200, 200, 200, 0.2)' }
      },
      x: {
        grid: { display: false }
      }
    }
  }
});

new Chart(ctxPie, {
  type: 'pie',
  data: {
    labels: <?= $deptLabels ?>,
    datasets: [{
      data: <?= $deptData ?>,
      backgroundColor: [
        'rgba(239, 193, 11, 0.75)',
        'rgba(200, 107, 237, 0.95)',
        'rgb(114, 155, 242)',
        'rgba(243, 124, 111, 0.8)',
        'rgba(98, 236, 156, 0.8)'
      ],
      hoverOffset: 15,
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { 
      legend: { 
        position: 'right',
        labels: {
          padding: 15,
          usePointStyle: true,
          pointStyle: 'circle'
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            const label = context.label || '';
            const value = context.raw || 0;
            return `${label}: ${value} teachers`;
          }
        }
      }
    },
    layout: {
      padding: 10
    }
  }
});

        // Modals
        function openEditModal(id) {
            const teacher = <?= json_encode($teachers) ?>.find(t => t.id == id);
            document.getElementById('edit_id').value = teacher.id;
            document.getElementById('edit_name').value = teacher.name;
            document.getElementById('edit_email').value = teacher.email;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openDeleteModal(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Filter AJAX
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const dept = document.getElementById('departmentFilter').value;
            const course = document.getElementById('courseFilter').value;
            const rows = document.querySelectorAll('#teacherTable tbody tr');
            rows.forEach(row => {
                const text = row.cells[2].textContent.toLowerCase();
                const deptMatch = !dept || text.includes(document.querySelector(`#departmentFilter option[value="${dept}"]`).text.toLowerCase());
                const courseMatch = !course || text.includes(document.querySelector(`#courseFilter option[value="${course}"]`).text.toLowerCase());
                row.style.display = (deptMatch && courseMatch) ? 'table-row' : 'none';
            });
        });

        document.getElementById('resetFilter').addEventListener('click', function() {
            document.getElementById('departmentFilter').value = '';
            document.getElementById('courseFilter').value = '';
            document.querySelectorAll('#teacherTable tbody tr').forEach(r => r.style.display = 'table-row');
        });
    </script>

</body>

</html>