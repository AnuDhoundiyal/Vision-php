<?php
require_once __DIR__ . '/../../../public/config/db.php';
header('Content-Type: application/json; charset=utf-8');

$students = 0;
if ($res = $conn->query("SELECT COUNT(*) AS total FROM students")) {
    $students = (int)$res->fetch_assoc()['total'];
}

$teachers = 0;
if ($res = $conn->query("SELECT COUNT(*) AS total FROM teachers")) {
    $teachers = (int)$res->fetch_assoc()['total'];
}

$courses = 0;
if ($res = $conn->query("SELECT COUNT(*) AS total FROM courses")) {
    $courses = (int)$res->fetch_assoc()['total'];
}

// No attendance schema yet
$attendance = 0;

echo json_encode([
    'students'   => $students,
    'teachers'   => $teachers,
    'courses'    => $courses,
    'attendance' => $attendance
]);
exit;
