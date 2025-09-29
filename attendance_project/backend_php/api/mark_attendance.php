<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../public/config/db.php';

// Get database connection
$conn = get_db();
if (!$conn) {
    echo json_encode(['success'=>false, 'message'=>'Database connection failed']);
    exit;
}

// Get input
$data = json_decode(file_get_contents('php://input'), true);
$student_id = isset($data['student_id']) ? intval($data['student_id']) : 0;
$course_id  = isset($data['course_id']) ? intval($data['course_id']) : 0;
$status     = $data['status'] ?? 'present';

if (!$student_id || !$course_id) {
    echo json_encode(['success'=>false, 'message'=>'Missing student_id or course_id']);
    exit;
}

// Insert attendance
$stmt = $conn->prepare("INSERT INTO attendance (student_id, course_id, status) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $student_id, $course_id, $status);

if ($stmt->execute()) {
    echo json_encode(['success'=>true, 'message'=>'Attendance marked']);
} else {
    echo json_encode(['success'=>false, 'message'=>'Database error: '.$conn->error]);
}

$stmt->close();
$conn->close();
?>
