```php
<?php
$pageTitle = "Students Management";
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access

// Fetch courses for filters and forms
$courses = [];
$res = $conn->query("SELECT id, name, batch, division FROM courses ORDER BY name ASC");
while($row = $res->fetch_assoc()) $courses[] = $row;

// Fetch distinct Batches & Divisions from students for filters
$batches = $conn->query("SELECT DISTINCT batch FROM students WHERE batch IS NOT NULL ORDER BY batch ASC")->fetch_all(MYSQLI_ASSOC);
$divisions = $conn->query("SELECT DISTINCT division FROM students WHERE division IS NOT NULL ORDER BY division ASC")->fetch_all(MYSQLI_ASSOC);

// Get current user's profile image for sidebar
$user_profile_image = $_SESSION['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<style>
    /* Custom styles for DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5em 1em;
        margin-left: 2px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f8f8f8;
        color: #333;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background-color: #3B82F6;
        color: white;
        border-color: #3B82F6;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #e0e0e0;
    }
    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 0.5em;
        margin-left: 0.5em;
        margin-right: 0.5em;
    }
</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

<div class="flex h-screen overflow-hidden">

  <!-- Sidebar -->
  <?php include_once __DIR__ . '/../components/sidebar.php'; ?>

  <div class="flex-1 overflow-auto lg:ml-64 p-6">

    <h1 class="text-2xl font-bold mb-4"><?= $pageTitle ?></h1>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="filter_course" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
                <select name="course" id="filter_course" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Courses</option>
                    <?php foreach($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filter_batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                <select name="batch" id="filter_batch" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Batches</option>
                    <?php foreach($batches as $batch): ?>
                        <option value="<?= htmlspecialchars($batch['batch']) ?>"><?= htmlspecialchars($batch['batch']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filter_division" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Division</label>
                <select name="division" id="filter_division" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Divisions</option>
                    <?php foreach($divisions as $division): ?>
                        <option value="<?= htmlspecialchars($division['division']) ?>"><?= htmlspecialchars($division['division']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="button" id="applyFilterBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filter</button>
                <button type="button" id="resetFilterBtn" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Reset</button>
            </div>
        </form>
    </div>

    <!-- Student Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Student List</h2>
            <button onclick="openAddEditModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">+ Add Student</button>
        </div>
        <table id="studentsTable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Profile</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Student ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Course / Batch / Division</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>

  </div>
</div>

<!-- Add/Edit Student Modal -->
<div id="addEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-800 dark:text-white">Add Student</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="studentForm" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <input type="hidden" name="id" id="student_id">
            <input type="hidden" name="action" id="form_action" value="create">

            <!-- Profile Image -->
            <div class="md:col-span-2 flex flex-col items-center">
                <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
                    <img id="profilePreview" src="https://ui-avatars.com/api/?name=Student&background=0D8ABC&color=fff" class="w-full h-full object-cover">
                </div>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" class="mt-4 p-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Max 5MB, JPG, PNG, WEBP</p>
            </div>

            <!-- Basic Info -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" id="full_name" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                <p id="password_hint" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Min 6 characters. Required for new students.</p>
            </div>
            <div>
                <label for="student_id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student ID Number</label>
                <input type="text" name="student_id_number" id="student_id_number" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                <input type="tel" name="phone" id="phone" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                <textarea name="address" id="address" rows="2" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <!-- Academic Info -->
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course <span class="text-red-500">*</span></label>
                <select name="course_id" id="course_id" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="">Select Course</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name'] . ' (' . $c['batch'] . '/' . $c['division'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="batch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch</label>
                <input type="text" name="batch" id="batch" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label for="division" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Division</label>
                <input type="text" name="division" id="division" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="w-full p-2.5 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="graduated">Graduated</option>
                </select>
            </div>

            <div class="md:col-span-2 flex justify-end gap-4 mt-6">
                <button type="button" onclick="closeModal()" class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Delete Student</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to delete <span id="deleteStudentName" class="font-semibold"></span>? This action cannot be undone.</p>
        <div class="flex justify-center gap-4">
            <button type="button" onclick="closeDeleteModal()" class="px-5 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">Cancel</button>
            <button type="button" id="confirmDeleteBtn" class="px-5 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
$(document).ready(function() {
    const UPLOAD_DIR_REL = "<?= $config['UPLOAD_DIR_REL'] ?>"; // From config.php

    // Initialize DataTable
    let studentsTable = $('#studentsTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "../../api/admin/students.php",
            "type": "POST",
            "data": function (d) {
                d.action = 'read';
                d.course_id = $('#filter_course').val();
                d.batch = $('#filter_batch').val();
                d.division = $('#filter_division').val();
            }
        },
        "columns": [
            { 
                "data": "profile_image",
                "render": function(data, type, row) {
                    const imageUrl = data ? `${UPLOAD_DIR_REL}/${data}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(row.full_name)}`;
                    return `<img src="${imageUrl}" alt="Profile" class="w-10 h-10 rounded-full object-cover">`;
                },
                "orderable": false
            },
            { "data": "full_name" },
            { "data": "student_id_number" },
            { "data": "email" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    return `${row.course_name || 'N/A'} / ${row.batch || 'N/A'} / ${row.division || 'N/A'}`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <button onclick="openAddEditModal(${row.id})" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-edit"></i> Edit</button>
                        <button onclick="openDeleteModal(${row.id}, '${row.full_name}')" class="text-red-600 hover:text-red-900"><i class="fas fa-trash-alt"></i> Delete</button>
                    `;
                },
                "orderable": false
            }
        ],
        "responsive": true,
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    // Filter actions
    $('#applyFilterBtn').on('click', function() {
        studentsTable.ajax.reload();
    });

    $('#resetFilterBtn').on('click', function() {
        $('#filter_course').val('');
        $('#filter_batch').val('');
        $('#filter_division').val('');
        studentsTable.ajax.reload();
    });

    // Profile image preview
    $('#profile_image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#profilePreview').attr('src', ev.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Student Form Submission
    $('#studentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const action = $('#form_action').val();
        const url = '../../api/admin/students.php';

        // Client-side validation
        if (!validateStudentForm()) {
            showToast('Please fill all required fields and ensure password meets criteria.', 'error');
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    closeModal();
                    studentsTable.ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error, xhr.responseText);
                showToast('An error occurred: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'error');
            }
        });
    });

    // Password validation for add/edit form
    $('#password').on('input', function() {
        const password = $(this).val();
        if (password.length < 6 && $('#form_action').val() === 'create') {
            $('#password_hint').html('Password must be at least 6 characters. <span class="text-red-500">Weak</span>');
        } else if (password.length >= 6) {
            $('#password_hint').html('Password must be at least 6 characters. <span class="text-green-500">Strong enough</span>');
        } else {
            $('#password_hint').text('Min 6 characters. Required for new students.');
        }
    });
});

// Global functions for modals and toasts
function openAddEditModal(id = null) {
    $('#studentForm')[0].reset(); // Clear form
    $('#student_id').val('');
    $('#form_action').val('create');
    $('#modalTitle').text('Add New Student');
    $('#profilePreview').attr('src', 'https://ui-avatars.com/api/?name=Student&background=0D8ABC&color=fff');
    $('#password').attr('required', true).val(''); // Password required for new user
    $('#password_hint').text('Min 6 characters. Required for new students.');

    if (id) {
        $('#form_action').val('update');
        $('#modalTitle').text('Edit Student');
        $('#student_id').val(id);
        $('#password').attr('required', false).val(''); // Password optional for edit
        $('#password_hint').text('Leave blank to keep current password.');

        // Fetch student data for editing
        $.ajax({
            url: '../../api/admin/students.php',
            type: 'POST',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const student = response.data;
                    $('#full_name').val(student.full_name);
                    $('#email').val(student.email);
                    $('#student_id_number').val(student.student_id_number);
                    $('#phone').val(student.phone);
                    $('#address').val(student.address);
                    $('#course_id').val(student.course_id);
                    $('#batch').val(student.batch);
                    $('#division').val(student.division);
                    $('#status').val(student.status);
                    if (student.profile_image) {
                        $('#profilePreview').attr('src', `${UPLOAD_DIR_REL}/${student.profile_image}`);
                    } else {
                        $('#profilePreview').attr('src', `https://ui-avatars.com/api/?name=${encodeURIComponent(student.full_name)}`);
                    }
                } else {
                    showToast('Failed to load student data: ' + response.message, 'error');
                    closeModal();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error, xhr.responseText);
                showToast('Error fetching student data.', 'error');
                closeModal();
            }
        });
    }
    $('#addEditModal').removeClass('hidden').addClass('flex');
}

function closeModal() {
    $('#addEditModal').addClass('hidden').removeClass('flex');
    $('#studentForm')[0].reset();
    $('#password_hint').text('Min 6 characters. Required for new students.');
}

let studentToDeleteId = null;
function openDeleteModal(id, name) {
    studentToDeleteId = id;
    $('#deleteStudentName').text(name);
    $('#deleteModal').removeClass('hidden').addClass('flex');
}

function closeDeleteModal() {
    studentToDeleteId = null;
    $('#deleteModal').addClass('hidden').removeClass('flex');
}

$('#confirmDeleteBtn').on('click', function() {
    if (studentToDeleteId) {
        $.ajax({
            url: '../../api/admin/students.php',
            type: 'POST',
            data: { action: 'delete', id: studentToDeleteId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#studentsTable').DataTable().ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
                closeDeleteModal();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error, xhr.responseText);
                showToast('Error deleting student.', 'error');
                closeDeleteModal();
            }
        });
    }
});

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

    // Animate in
    setTimeout(() => {
        toast.removeClass('translate-y-full opacity-0').addClass('translate-y-0 opacity-100');
    }, 10);

    // Animate out and remove
    setTimeout(() => {
        toast.removeClass('translate-y-0 opacity-100').addClass('translate-y-full opacity-0');
        toast.on('transitionend', function() {
            $(this).remove();
        });
    }, 3000);
}

function validateStudentForm() {
    let isValid = true;
    const fullName = $('#full_name').val();
    const email = $('#email').val();
    const password = $('#password').val();
    const courseId = $('#course_id').val();
    const action = $('#form_action').val();

    if (!fullName || !email || !courseId) {
        isValid = false;
    }

    if (action === 'create' && password.length < 6) {
        isValid = false;
    }
    // For update, password is optional, so only validate if provided
    if (action === 'update' && password && password.length < 6) {
        isValid = false;
    }

    // Basic email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        isValid = false;
    }

    return isValid;
}
</script>
</body>
</html>
```