```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access

$conn = $db->getConnection();

// Fetch departments and courses
$departments = [];
$result = $conn->query("SELECT id, name, batch, division FROM departments ORDER BY name ASC");
while($row = $result->fetch_assoc()) $departments[] = $row;

$courses = [];
$result = $conn->query("
    SELECT courses.id, courses.name, courses.batch, courses.division, departments.name AS dept_name 
    FROM courses 
    LEFT JOIN departments ON courses.department_id = departments.id 
    ORDER BY courses.name ASC
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include_once __DIR__ . '/../components/sidebar.php'; ?>

    <div class="flex-1 p-6 overflow-auto lg:ml-64">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white">Departments & Courses</h1>

        <!-- Notification -->
        <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add Department -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-t-4 border-blue-600 p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add Department</h2>
                <form id="deptForm" class="space-y-3">
                    <div>
                        <label for="department_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department Name <span class="text-red-500">*</span></label>
                        <input type="text" name="department_name" id="department_name" placeholder="e.g., Computer Science" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div>
                        <label for="department_batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch (Optional)</label>
                        <input type="text" name="department_batch" id="department_batch" placeholder="e.g., 2025-2026" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="department_division" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Division (Optional)</label>
                        <input type="text" name="department_division" id="department_division" placeholder="e.g., A, B" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full"><i class="fas fa-plus mr-2"></i>Add Department</button>
                </form>
            </div>

            <!-- Add Course -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border-t-4 border-green-600 p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add Course</h2>
                <form id="courseForm" class="space-y-3">
                    <div>
                        <label for="course_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course Name <span class="text-red-500">*</span></label>
                        <input type="text" name="course_name" id="course_name" placeholder="e.g., Data Structures" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                    </div>
                    <div>
                        <label for="course_batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch (Optional)</label>
                        <input type="text" name="course_batch" id="course_batch" placeholder="e.g., 2025-2026" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="course_division" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Division (Optional)</label>
                        <input type="text" name="course_division" id="course_division" placeholder="e.g., A, B" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label for="courseDeptSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="courseDeptSelect" class="w-full p-2.5 border rounded-md dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select Department</option>
                            <?php foreach($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 w-full"><i class="fas fa-plus mr-2"></i>Add Course</button>
                </form>
            </div>
        </div>

        <!-- Lists -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div id="deptListCard" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Departments List</h2>
                <ul id="deptList" class="space-y-2">
                    <?php if (empty($departments)): ?>
                        <li class="text-gray-500 dark:text-gray-400">No departments added yet.</li>
                    <?php else: ?>
                        <?php foreach($departments as $dept): ?>
                            <li class="flex justify-between items-center p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <span><?= htmlspecialchars($dept['name']) ?> (Batch: <?= htmlspecialchars($dept['batch'] ?: 'N/A') ?>, Division: <?= htmlspecialchars($dept['division'] ?: 'N/A') ?>)</span>
                                <button onclick="deleteDepartment(<?= $dept['id'] ?>, '<?= htmlspecialchars($dept['name']) ?>')" class="text-red-500 hover:text-red-700 ml-4"><i class="fas fa-trash-alt"></i></button>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div id="courseListCard" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Courses List</h2>
                <ul id="courseList" class="space-y-2">
                    <?php if (empty($courses)): ?>
                        <li class="text-gray-500 dark:text-gray-400">No courses added yet.</li>
                    <?php else: ?>
                        <?php foreach($courses as $course): ?>
                            <li class="flex justify-between items-center p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <span><?= htmlspecialchars($course['name']) ?> (Batch: <?= htmlspecialchars($course['batch'] ?: 'N/A') ?>, Division: <?= htmlspecialchars($course['division'] ?: 'N/A') ?>, Dept: <?= htmlspecialchars($course['dept_name'] ?: 'N/A') ?>)</span>
                                <button onclick="deleteCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['name']) ?>')" class="text-red-500 hover:text-red-700 ml-4"><i class="fas fa-trash-alt"></i></button>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4" id="deleteModalTitle">Confirm Deletion</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6" id="deleteModalMessage"></p>
        <div class="flex justify-center gap-4">
            <button type="button" onclick="closeDeleteConfirmModal()" class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button type="button" id="confirmDeleteActionBtn" class="px-5 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

    // Function to show toast notifications
    function showToast(message, type = 'info') {
        const toastContainer = $('#toast-container');
        const toast = $(`
            <div class="p-4 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-y-full opacity-0
                ${type === 'success' ? 'bg-green-500' :
                  type === 'error' ? 'bg-red-500' :
                  type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'}">
                ${message}
            </div>
        `);
        toastContainer.append(toast);

        setTimeout(() => {
            toast.removeClass('translate-y-full opacity-0').addClass('translate-y-0 opacity-100');
        }, 10);

        setTimeout(() => {
            toast.removeClass('translate-y-0 opacity-100').addClass('translate-y-full opacity-0');
            toast.on('transitionend', function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Add Department AJAX
    $('#deptForm').submit(function(e){
        e.preventDefault();
        if (!$(this).find('input[name="department_name"]').val()) {
            showToast('Department name is required.', 'error');
            return;
        }
        $.post('../../api/admin/courses_depts.php', $(this).serialize() + '&action=add_department', function(res){
            if(res.success){
                showToast(res.message, 'success');
                $('#deptForm')[0].reset();
                // Update department list
                $('#deptList').prepend(`
                    <li class="flex justify-between items-center p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <span>${res.data.name} (Batch: ${res.data.batch || 'N/A'}, Division: ${res.data.division || 'N/A'})</span>
                        <button onclick="deleteDepartment(${res.data.id}, '${res.data.name}')" class="text-red-500 hover:text-red-700 ml-4"><i class="fas fa-trash-alt"></i></button>
                    </li>
                `);
                // Add new department to Course dropdown
                $('#courseDeptSelect').append(`<option value="${res.data.id}">${res.data.name}</option>`);
            } else {
                showToast(res.message, 'error');
            }
        }, 'json').fail(function(xhr) {
            showToast('Server error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText), 'error');
        });
    });

    // Add Course AJAX
    $('#courseForm').submit(function(e){
        e.preventDefault();
        if (!$(this).find('input[name="course_name"]').val() || !$(this).find('select[name="department_id"]').val()) {
            showToast('Course name and department are required.', 'error');
            return;
        }
        $.post('../../api/admin/courses_depts.php', $(this).serialize() + '&action=add_course', function(res){
            if(res.success){
                showToast(res.message, 'success');
                $('#courseForm')[0].reset();
                $('#courseList').prepend(`
                    <li class="flex justify-between items-center p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <span>${res.data.name} (Batch: ${res.data.batch || 'N/A'}, Division: ${res.data.division || 'N/A'}, Dept: ${res.data.dept_name || 'N/A'})</span>
                        <button onclick="deleteCourse(${res.data.id}, '${res.data.name}')" class="text-red-500 hover:text-red-700 ml-4"><i class="fas fa-trash-alt"></i></button>
                    </li>
                `);
            } else {
                showToast(res.message, 'error');
            }
        }, 'json').fail(function(xhr) {
            showToast('Server error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText), 'error');
        });
    });

    // Global variables for delete modal
    let itemToDelete = { id: null, type: null, name: null };

    window.deleteDepartment = function(id, name) {
        itemToDelete = { id: id, type: 'department', name: name };
        $('#deleteModalTitle').text('Delete Department');
        $('#deleteModalMessage').html(`Are you sure you want to delete department <strong>${name}</strong>? This will also delete all associated courses.`);
        $('#deleteConfirmModal').removeClass('hidden').addClass('flex');
    };

    window.deleteCourse = function(id, name) {
        itemToDelete = { id: id, type: 'course', name: name };
        $('#deleteModalTitle').text('Delete Course');
        $('#deleteModalMessage').html(`Are you sure you want to delete course <strong>${name}</strong>?`);
        $('#deleteConfirmModal').removeClass('hidden').addClass('flex');
    };

    window.closeDeleteConfirmModal = function() {
        $('#deleteConfirmModal').addClass('hidden').removeClass('flex');
        itemToDelete = { id: null, type: null, name: null };
    };

    $('#confirmDeleteActionBtn').on('click', function() {
        if (itemToDelete.id && itemToDelete.type) {
            $.post('../../api/admin/courses_depts.php', { action: `delete_${itemToDelete.type}`, id: itemToDelete.id }, function(res) {
                if (res.success) {
                    showToast(res.message, 'success');
                    // Reload lists or remove item from DOM
                    if (itemToDelete.type === 'department') {
                        location.reload(); // Simple reload for now to update all dependent lists
                    } else if (itemToDelete.type === 'course') {
                        location.reload(); // Simple reload for now
                    }
                } else {
                    showToast(res.message, 'error');
                }
                closeDeleteConfirmModal();
            }, 'json').fail(function(xhr) {
                showToast('Server error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText), 'error');
                closeDeleteConfirmModal();
            });
        }
    });
});
</script>
</body>
</html>
```