<?php
$pageTitle = "Mark Attendance";
$current_page = basename($_SERVER['PHP_SELF']);

require_once __DIR__ . '/../../public/config/db.php';

// Fetch courses
$courses = [];
$res = $conn->query("SELECT * FROM courses ORDER BY name ASC");
while($row = $res->fetch_assoc()) $courses[] = $row;

// Fetch students
$students = [];
$res = $conn->query("SELECT s.id, s.name, s.student_id, s.course_id, c.name AS course_name, s.batch, s.division 
                     FROM students s 
                     LEFT JOIN courses c ON s.course_id = c.id 
                     ORDER BY s.name ASC");
while($row = $res->fetch_assoc()) $students[] = $row;

// Batches and divisions
$batches = [];
$divisions = [];
$res = $conn->query("SELECT DISTINCT batch FROM students ORDER BY batch ASC");
while($row = $res->fetch_assoc()) $batches[] = $row['batch'];
$res = $conn->query("SELECT DISTINCT division FROM students ORDER BY division ASC");
while($row = $res->fetch_assoc()) $divisions[] = $row['division'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans">

<div class="flex h-screen overflow-hidden">
    <?php include_once '../components/sidebar.php'; ?>

    <div class="flex-1 overflow-auto lg:ml-64">
        <header class="bg-gray-100 dark:bg-gray-800 shadow-sm z-10 px-6 py-4">
            <h1 class="text-2xl font-bold"><?= $pageTitle ?></h1>
        </header>

        <main class="p-6 space-y-6">

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 flex flex-col md:flex-row gap-4 items-center">
                <select id="courseSelect" class="w-full md:w-1/3 p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <option value="">Select Course</option>
                    <?php foreach($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
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
                        <tbody id="studentsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <button id="submitAttendance" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Submit Attendance</button>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Notification Card -->
<div id="notificationCard" class="fixed top-5 right-5 hidden p-4 rounded-lg shadow-md text-white font-medium transition"></div>

<script>
const students = <?php echo json_encode($students); ?>;
const courseSelect = document.getElementById('courseSelect');
const batchSelect = document.getElementById('batchSelect');
const divisionSelect = document.getElementById('divisionSelect');
const studentsTableBody = document.getElementById('studentsTableBody');
const notificationCard = document.getElementById('notificationCard');

let filteredStudents = [];

function showNotification(message, success = true) {
    notificationCard.textContent = message;
    notificationCard.style.backgroundColor = success ? '#34D399' : '#F87171'; // green/red
    notificationCard.classList.remove('hidden');
    setTimeout(() => notificationCard.classList.add('hidden'), 3000);
}

function populateStudents() {
    const courseId = courseSelect.value;
    const batch = batchSelect.value;
    const division = divisionSelect.value;

    filteredStudents = students.filter(s => (!courseId || s.course_id == courseId)
                                        && (!batch || s.batch == batch)
                                        && (!division || s.division == division));

    studentsTableBody.innerHTML = '';
    filteredStudents.forEach((s, idx) => {
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-100 dark:hover:bg-gray-700 transition";
        tr.innerHTML = `
            <td class="px-6 py-3">${idx+1}</td>
            <td class="px-6 py-3 font-medium">${s.name}</td>
            <td class="px-6 py-3">${s.student_id}</td>
            <td class="px-6 py-3 flex gap-2">
                <button class="attendance-btn present px-3 py-1 rounded-lg border border-green-400 text-green-700 hover:bg-green-100 transition">Present</button>
                <button class="attendance-btn absent px-3 py-1 rounded-lg border border-red-400 text-red-700 hover:bg-red-100 transition">Absent</button>
            </td>
        `;
        studentsTableBody.appendChild(tr);

        const presentBtn = tr.querySelector('.present');
        const absentBtn = tr.querySelector('.absent');

        presentBtn.addEventListener('click', () => {
            presentBtn.classList.add('bg-green-100');
            absentBtn.classList.remove('bg-red-100');
            presentBtn.dataset.status = 'present';
            absentBtn.dataset.status = '';
        });
        absentBtn.addEventListener('click', () => {
            absentBtn.classList.add('bg-red-100');
            presentBtn.classList.remove('bg-green-100');
            absentBtn.dataset.status = 'absent';
            presentBtn.dataset.status = '';
        });
    });
}

courseSelect.addEventListener('change', populateStudents);
batchSelect.addEventListener('change', populateStudents);
divisionSelect.addEventListener('change', populateStudents);

document.getElementById('submitAttendance').addEventListener('click', () => {
    if(filteredStudents.length === 0){
        showNotification('No students to submit!', false);
        return;
    }

    const attendanceData = [];
    studentsTableBody.querySelectorAll('tr').forEach((row, idx) => {
        const presentBtn = row.querySelector('.present');
        const absentBtn = row.querySelector('.absent');
        const status = presentBtn.dataset.status || absentBtn.dataset.status || 'absent';
        const studentId = filteredStudents[idx].id;
        const courseId = filteredStudents[idx].course_id;
        attendanceData.push({ student_id: studentId, course_id: courseId, status });
    });

    fetch('api/submit_attendance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(attendanceData)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            showNotification(data.message, true);
        } else {
            showNotification(data.message, false);
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('Error submitting attendance', false);
    });
});

</script>
</body>
</html>
