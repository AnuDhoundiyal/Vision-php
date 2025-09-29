```php
<?php
$pageTitle = "Mark Attendance";
$current_page = basename($_SERVER['PHP_SELF']);

require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('teacher'); // Ensure only teachers can access

$conn = $db->getConnection();
$teacher_id = $_SESSION['user_id']; // Assuming teacher_id is stored in session as user_id

// Fetch courses assigned to the current teacher
$assigned_courses = [];
$stmt = $conn->prepare("
    SELECT tc.course_id, c.name AS course_name, c.batch, c.division
    FROM teacher_courses tc
    JOIN teachers t ON tc.teacher_id = t.id
    JOIN users u ON t.user_id = u.id
    JOIN courses c ON tc.course_id = c.id
    WHERE u.id = ?
    ORDER BY c.name ASC
");
if ($stmt) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $assigned_courses[] = $row;
    }
    $stmt->close();
}

// Get unique batches and divisions from assigned courses for filters
$batches = [];
$divisions = [];
foreach ($assigned_courses as $course) {
    if (!empty($course['batch']) && !in_array($course['batch'], $batches)) {
        $batches[] = $course['batch'];
    }
    if (!empty($course['division']) && !in_array($course['division'], $divisions)) {
        $divisions[] = $course['division'];
    }
}
sort($batches);
sort($divisions);

// Get current date for attendance
$today_date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans">

<div class="flex h-screen overflow-hidden">
    <?php include_once __DIR__ . '/../components/sidebar.php'; ?>

    <div class="flex-1 overflow-auto lg:ml-64">
        <header class="bg-gray-100 dark:bg-gray-800 shadow-sm z-10 px-6 py-4">
            <h1 class="text-2xl font-bold"><?= $pageTitle ?></h1>
        </header>

        <main class="p-6 space-y-6">

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 flex flex-col md:flex-row gap-4 items-center">
                <select id="courseSelect" class="w-full md:w-1/3 p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">Select Course</option>
                    <?php foreach($assigned_courses as $course): ?>
                        <option value="<?= $course['course_id'] ?>" data-batch="<?= htmlspecialchars($course['batch']) ?>" data-division="<?= htmlspecialchars($course['division']) ?>">
                            <?= htmlspecialchars($course['course_name']) ?> (<?= htmlspecialchars($course['batch']) ?>/<?= htmlspecialchars($course['division']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <select id="batchSelect" class="w-full md:w-1/3 p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">Select Batch</option>
                    <?php foreach($batches as $b): ?>
                        <option value="<?= $b ?>"><?= $b ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="divisionSelect" class="w-full md:w-1/3 p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">Select Division</option>
                    <?php foreach($divisions as $d): ?>
                        <option value="<?= $d ?>"><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Students Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">#</th>
                                <th class="px-6 py-3 text-left font-medium">Name</th>
                                <th class="px-6 py-3 text-left font-medium">Student ID</th>
                                <th class="px-6 py-3 text-left font-medium">Attendance</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr><td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-400">Please select a course to view students.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <button id="submitAttendance" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" disabled>Submit Attendance</button>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Notification Card -->
<div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

<script>
const assignedCourses = <?php echo json_encode($assigned_courses); ?>;
const courseSelect = document.getElementById('courseSelect');
const batchSelect = document.getElementById('batchSelect');
const divisionSelect = document.getElementById('divisionSelect');
const studentsTableBody = document.getElementById('studentsTableBody');
const submitAttendanceBtn = document.getElementById('submitAttendance');
const todayDate = '<?php echo $today_date; ?>';

let currentStudents = []; // Stores students for the currently selected course/batch/division

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        console.warn('Toast container not found. Create a div with id="toast-container"');
        return;
    }

    const toast = document.createElement('div');
    toast.className = `p-4 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-y-full opacity-0
        ${type === 'success' ? 'bg-green-500' :
          type === 'error' ? 'bg-red-500' :
          type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'}`;
    toast.textContent = message;
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-y-full', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-full', 'opacity-0');
        toast.addEventListener('transitionend', function() {
            toast.remove();
        });
    }, 3000);
}

async function fetchStudentsForCourse() {
    const courseId = courseSelect.value;
    const batch = batchSelect.value;
    const division = divisionSelect.value;

    if (!courseId) {
        studentsTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-400">Please select a course to view students.</td></tr>';
        submitAttendanceBtn.disabled = true;
        currentStudents = [];
        return;
    }

    try {
        const response = await fetch(`../../api/teacher/get-students-for-attendance.php?course_id=${courseId}&batch=${batch}&division=${division}`);
        const data = await response.json();

        if (data.success) {
            currentStudents = data.data;
            renderStudentsTable(currentStudents);
            submitAttendanceBtn.disabled = currentStudents.length === 0;
        } else {
            showToast(data.message, 'error');
            studentsTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500 dark:text-red-400">${data.message}</td></tr>`;
            submitAttendanceBtn.disabled = true;
            currentStudents = [];
        }
    } catch (error) {
        console.error('Error fetching students:', error);
        showToast('Failed to load students. Please try again.', 'error');
        studentsTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-red-500 dark:text-red-400">Error loading students.</td></tr>';
        submitAttendanceBtn.disabled = true;
        currentStudents = [];
    }
}

function renderStudentsTable(students) {
    studentsTableBody.innerHTML = '';
    if (students.length === 0) {
        studentsTableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500 dark:text-gray-400">No students found for this course, batch, and division.</td></tr>';
        return;
    }

    students.forEach((s, idx) => {
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-100 dark:hover:bg-gray-700 transition";
        tr.innerHTML = `
            <td class="px-6 py-3">${idx+1}</td>
            <td class="px-6 py-3 font-medium">${s.full_name}</td>
            <td class="px-6 py-3">${s.student_id_number}</td>
            <td class="px-6 py-3 flex gap-2">
                <button class="attendance-btn present px-3 py-1 rounded-lg border border-green-400 text-green-700 hover:bg-green-100 transition" data-status="present">Present</button>
                <button class="attendance-btn absent px-3 py-1 rounded-lg border border-red-400 text-red-700 hover:bg-red-100 transition" data-status="absent">Absent</button>
                <button class="attendance-btn late px-3 py-1 rounded-lg border border-yellow-400 text-yellow-700 hover:bg-yellow-100 transition" data-status="late">Late</button>
            </td>
        `;
        studentsTableBody.appendChild(tr);

        const presentBtn = tr.querySelector('.present');
        const absentBtn = tr.querySelector('.absent');
        const lateBtn = tr.querySelector('.late');

        // Set initial state based on previous attendance if available
        if (s.attendance_status) {
            if (s.attendance_status === 'present') presentBtn.classList.add('bg-green-100');
            else if (s.attendance_status === 'absent') absentBtn.classList.add('bg-red-100');
            else if (s.attendance_status === 'late') lateBtn.classList.add('bg-yellow-100');
        } else {
            // Default to absent if no previous record
            absentBtn.classList.add('bg-red-100');
        }

        const buttons = [presentBtn, absentBtn, lateBtn];
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('bg-green-100', 'bg-red-100', 'bg-yellow-100'));
                if (btn.dataset.status === 'present') btn.classList.add('bg-green-100');
                else if (btn.dataset.status === 'absent') btn.classList.add('bg-red-100');
                else if (btn.dataset.status === 'late') btn.classList.add('bg-yellow-100');
            });
        });
    });
}

courseSelect.addEventListener('change', fetchStudentsForCourse);
batchSelect.addEventListener('change', fetchStudentsForCourse);
divisionSelect.addEventListener('change', fetchStudentsForCourse);

submitAttendanceBtn.addEventListener('click', async () => {
    if (currentStudents.length === 0) {
        showToast('No students to submit attendance for!', 'warning');
        return;
    }

    const attendanceData = [];
    studentsTableBody.querySelectorAll('tr').forEach((row, idx) => {
        const student = currentStudents[idx];
        const selectedStatusBtn = row.querySelector('.attendance-btn.bg-green-100, .attendance-btn.bg-red-100, .attendance-btn.bg-yellow-100');
        const status = selectedStatusBtn ? selectedStatusBtn.dataset.status : 'absent'; // Default to absent

        attendanceData.push({
            user_id: student.user_id,
            course_id: courseSelect.value,
            status: status,
            date: todayDate
        });
    });

    try {
        const response = await fetch('../../api/teacher/submit-attendance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(attendanceData)
        });
        const data = await response.json();

        if (data.success) {
            showToast(data.message, 'success');
            // Optionally re-fetch students to show updated attendance status
            fetchStudentsForCourse();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error submitting attendance:', error);
        showToast('Failed to submit attendance. Please try again.', 'error');
    }
});

// Initial load
fetchStudentsForCourse();
```