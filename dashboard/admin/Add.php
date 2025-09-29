<?php
// session_start();

include __DIR__ . '/api/setup_all.php';
  // if file is in admin/api


// Fetch departments and courses
$departments = [];
$result = $conn->query("SELECT * FROM departments ORDER BY id DESC");
while($row = $result->fetch_assoc()) $departments[] = $row;

$courses = [];
$result = $conn->query("
    SELECT courses.*, departments.name AS dept_name 
    FROM courses 
    LEFT JOIN departments ON courses.department_id = departments.id 
    ORDER BY courses.id DESC
");
while($row = $result->fetch_assoc()) $courses[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Departments & Courses - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<div class="flex h-screen">

    <!-- Sidebar -->
    <?php include_once '../components/sidebar.php'; ?>

    <div class="flex-1 p-6 overflow-auto ml-64">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white">Departments & Courses</h1>

        <!-- Notification -->
        <div id="notif" class="fixed top-5 right-5 p-4 rounded shadow flex items-center justify-between hidden w-96 bg-green-600 text-white">
            <span id="notifMsg"></span>
            <button onclick="$('#notif').hide()" class="ml-4 font-bold text-xl">✖</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add Department -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-t-4 border-blue-600 p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add Department</h2>
                <form id="deptForm" class="space-y-3">
                    <input type="text" name="department_name" placeholder="Department Name" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                    <input type="text" name="department_batch" placeholder="Batch (e.g., 2025-2026)" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <input type="text" name="department_division" placeholder="Division (e.g., A,B)" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full">Add Department</button>
                </form>
            </div>

            <!-- Add Course -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-t-4 border-green-600 p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add Course</h2>
                <form id="courseForm" class="space-y-3">
                    <input type="text" name="course_name" placeholder="Course Name" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                    <input type="text" name="course_batch" placeholder="Batch (e.g., 2025-2026)" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <input type="text" name="course_division" placeholder="Division (e.g., A,B)" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <select name="department_id" id="courseDeptSelect" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                        <option value="">Select Department</option>
                        <?php foreach($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 w-full">Add Course</button>
                </form>
            </div>
        </div>

        <!-- Lists -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div id="deptListCard" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Departments List</h2>
                <ul id="deptList">
                    <?php foreach($departments as $dept): ?>
                        <li class="flex justify-between items-center mb-2 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <?= htmlspecialchars($dept['name']) ?> (Batch: <?= htmlspecialchars($dept['batch']) ?>, Division: <?= htmlspecialchars($dept['division']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div id="courseListCard" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Courses List</h2>
                <ul id="courseList">
                    <?php foreach($courses as $course): ?>
                        <li class="flex justify-between items-center mb-2 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <?= htmlspecialchars($course['name']) ?> (Batch: <?= htmlspecialchars($course['batch']) ?>, Division: <?= htmlspecialchars($course['division']) ?>, Dept: <?= htmlspecialchars($course['dept_name']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

    // Show notification
    function showNotif(msg){
        $('#notifMsg').text(msg);
        $('#notif').fadeIn();
        setTimeout(()=>$('#notif').fadeOut(), 3000);
    }

    // Add Department AJAX
    $('#deptForm').submit(function(e){
        e.preventDefault();
        $.post('ajax_add.php', $(this).serialize() + '&type=department', function(res){
            if(res.status == 'success'){
                showNotif(res.msg);
                $('#deptForm')[0].reset();

                // Update department list
                $('#deptList').prepend(`<li class="flex justify-between items-center mb-2 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">${res.data.name} (Batch: ${res.data.batch}, Division: ${res.data.division})</li>`);

                // Add new department to Course dropdown
                $('#courseDeptSelect').append(`<option value="${res.data.id}">${res.data.name}</option>`);
            }
        }, 'json');
    });

    // Add Course AJAX
    $('#courseForm').submit(function(e){
        e.preventDefault();
        $.post('ajax_add.php', $(this).serialize() + '&type=course', function(res){
            if(res.status == 'success'){
                showNotif(res.msg);
                $('#courseForm')[0].reset();
                $('#courseList').prepend(`<li class="flex justify-between items-center mb-2 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">${res.data.name} (Batch: ${res.data.batch}, Division: ${res.data.division}, Dept: ${res.data.dept_name})</li>`);
            }
        }, 'json');
    });

});
</script>

</body>
</html>
