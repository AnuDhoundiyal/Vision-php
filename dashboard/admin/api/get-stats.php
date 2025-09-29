```php
<?php
require_once __DIR__ . '/../../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access this API

header('Content-Type: application/json; charset=utf-8');

$conn = $db->getConnection();

$students = 0;
if ($res = $conn->query("SELECT COUNT(s.id) AS total FROM students s JOIN users u ON s.user_id = u.id WHERE u.role = 'student'")) {
    $students = (int)$res->fetch_assoc()['total'];
}

$teachers = 0;
if ($res = $conn->query("SELECT COUNT(t.id) AS total FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.role = 'teacher'")) {
    $teachers = (int)$res->fetch_assoc()['total'];
}

$courses = 0;
if ($res = $conn->query("SELECT COUNT(*) AS total FROM courses")) {
    $courses = (int)$res->fetch_assoc()['total'];
}

// Today's attendance percentage (example for students)
$today = date('Y-m-d');
$totalStudentsToday = 0;
if ($res = $conn->query("SELECT COUNT(s.id) AS total FROM students s JOIN users u ON s.user_id = u.id WHERE u.role = 'student' AND s.status = 'active'")) {
    $totalStudentsToday = (int)$res->fetch_assoc()['total'];
}

$presentStudentsToday = 0;
if ($res = $conn->query("SELECT COUNT(DISTINCT user_id) AS present FROM attendance WHERE user_type = 'student' AND date = '$today' AND status = 'present'")) {
    $presentStudentsToday = (int)$res->fetch_assoc()['present'];
}

$attendancePercentage = ($totalStudentsToday > 0) ? round(($presentStudentsToday / $totalStudentsToday) * 100, 2) : 0;


echo json_encode([
    'students'   => $students,
    'teachers'   => $teachers,
    'courses'    => $courses,
    'attendance' => $attendancePercentage
]);
exit;
```