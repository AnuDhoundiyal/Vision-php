<?php
$pageTitle = "Students Management";
require_once __DIR__ . '/../../public/config/db.php';

// Filters
$filterCourse = $_GET['course'] ?? '';
$filterBatch = $_GET['batch'] ?? '';
$filterDivision = $_GET['division'] ?? '';

// Fetch Courses
$courses = $conn->query("SELECT id, name FROM courses ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch distinct Batches & Divisions
$batches = $conn->query("SELECT DISTINCT batch FROM students ORDER BY batch ASC")->fetch_all(MYSQLI_ASSOC);
$divisions = $conn->query("SELECT DISTINCT division FROM students ORDER BY division ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch students dynamically based on filters
$sql = "SELECT s.*, c.name AS course FROM students s LEFT JOIN courses c ON s.course_id = c.id WHERE 1=1";
if($filterCourse) $sql .= " AND s.course_id = " . intval($filterCourse);
if($filterBatch) $sql .= " AND s.batch = '" . $conn->real_escape_string($filterBatch) . "'";
if($filterDivision) $sql .= " AND s.division = '" . $conn->real_escape_string($filterDivision) . "'";
$sql .= " ORDER BY s.id DESC";
$result = $conn->query($sql);

$students = [];
while($row = $result->fetch_assoc()){
    $students[] = $row;
}

// Success message
$successMsg = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

<div class="flex h-screen overflow-hidden">

  <!-- Sidebar -->
  <?php include_once '../components/sidebar.php'; ?>

  <div class="flex-1 overflow-auto lg:ml-64 p-6">

    <h1 class="text-2xl font-bold mb-4"><?= $pageTitle ?></h1>

    <!-- Success Message -->
    <?php if($successMsg): ?>
        <div class="mb-4 p-4 rounded bg-green-100 text-green-800 text-center">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <form id="filterForm" method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select name="course" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Courses</option>
                <?php foreach($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= ($filterCourse==$course['id'])?'selected':'' ?>><?= htmlspecialchars($course['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="batch" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Batches</option>
                <?php foreach($batches as $batch): ?>
                    <option value="<?= htmlspecialchars($batch['batch']) ?>" <?= ($filterBatch==$batch['batch'])?'selected':'' ?>><?= htmlspecialchars($batch['batch']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="division" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Divisions</option>
                <?php foreach($divisions as $division): ?>
                    <option value="<?= htmlspecialchars($division['division']) ?>" <?= ($filterDivision==$division['division'])?'selected':'' ?>><?= htmlspecialchars($division['division']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filter</button>
                <button type="button" id="resetBtn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Reset</button>
            </div>
        </form>
    </div>

    <!-- Student Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Student List</h2>
            <button onclick="openAddModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Add Student</button>
        </div>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Profile</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Course / Batch / Division</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">ID / Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if(!empty($students)): ?>
                    <?php foreach($students as $student): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img class="w-10 h-10 rounded-full object-cover"
                                src="<?= !empty($student['profile_image']) ? '../../'.$student['profile_image'] : 'https://ui-avatars.com/api/?name='.urlencode($student['name']) ?>" alt="Profile">
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($student['name']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($student['course'].' / '.$student['batch'].' / '.$student['division']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($student['student_id'] ?? $student['email']) ?></td>
                        <td class="px-6 py-4 flex gap-2">
                            <button onclick="openEditModal(<?= $student['id'] ?>)" class="text-blue-600 hover:text-blue-900">Edit</button>
                            <button onclick="openDeleteModal(<?= $student['id'] ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

  </div>
</div>

<!-- Centered Modals -->
<div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div id="modalContent" class="bg-white dark:bg-gray-800 p-6 rounded w-96">
        <h2 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-white mb-4"></h2>
        <form id="modalForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="modal_id">
            <div id="modalBody"></div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded" id="modalSubmit">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
// Students data for JS
const students = <?= json_encode($students) ?>;

// Filter Reset
document.getElementById('resetBtn').addEventListener('click', function(){
    document.querySelector('select[name="course"]').value = '';
    document.querySelector('select[name="batch"]').value = '';
    document.querySelector('select[name="division"]').value = '';
    document.getElementById('filterForm').submit();
});

// Modals
function openEditModal(id){
    const student = students.find(s=>s.id==id);
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = "Edit Student";
    document.getElementById('modal_id').value = student.id;
    document.getElementById('modalBody').innerHTML = `
        <div class="mb-3"><label class="block mb-1">Name</label><input type="text" name="name" value="${student.name}" class="w-full p-2 rounded border"></div>
        <div class="mb-3"><label class="block mb-1">Email</label><input type="email" name="email" value="${student.email}" class="w-full p-2 rounded border"></div>
        <div class="mb-3"><label class="block mb-1">Profile Image</label><input type="file" name="profile_image" class="w-full p-2 rounded border"></div>
    `;
    document.getElementById('modalForm').onsubmit = function(e){
        e.preventDefault();
        alert('Edit function here to update DB'); // Replace with AJAX/PHP call
        closeModal();
    }
}

function openDeleteModal(id){
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = "Delete Student";
    document.getElementById('modalBody').innerHTML = "<p>Are you sure you want to delete this student?</p>";
    document.getElementById('modalSubmit').innerText = "Delete";
    document.getElementById('modalForm').onsubmit = function(e){
        e.preventDefault();
        alert('Delete function here to remove from DB'); // Replace with AJAX/PHP call
        closeModal();
    }
}

function closeModal(){
    document.getElementById('modalOverlay').classList.add('hidden');
}
</script>
</body>
</html>
