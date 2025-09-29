<?php
require_once __DIR__ . '/../../public/config/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$today = date('Y-m-d');
$failed_students = [];

$stmt = $conn->prepare("
    INSERT INTO attendance (student_id, course_id, status, date, created_at)
    VALUES (?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE status = VALUES(status)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

foreach ($data as $att) {
    $student_id = intval($att['student_id']);
    $course_id  = intval($att['course_id']);
    $status     = $att['status'] ?? 'absent';

    if (!$stmt->bind_param('iiss', $student_id, $course_id, $status, $today)) {
        $failed_students[] = $att;
        error_log("Bind failed: student_id=$student_id, course_id=$course_id, status=$status");
        continue;
    }

    if (!$stmt->execute()) {
        $failed_students[] = $att;
        error_log("Execute failed: student_id=$student_id, course_id=$course_id, status=$status, Error=".$stmt->error);
    }
}

$stmt->close();

if (empty($failed_students)) {
    echo json_encode(['success' => true, 'message' => 'Attendance submitted successfully!']);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Some entries could not be saved. Check PHP error log.',
        'failed_students' => $failed_students
    ]);
}
