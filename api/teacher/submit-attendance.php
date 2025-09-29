```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('teacher'); // Ensure only teachers can access this API

header('Content-Type: application/json');

$conn = $db->getConnection();
$data = json_decode(file_get_contents('php://input'), true);

$response = [
    'success' => false,
    'message' => 'No data received or invalid format.',
    'failed_entries' => []
];

if (!is_array($data) || empty($data)) {
    json_response(false, "No attendance data provided.", null, 400);
}

$conn->begin_transaction();
try {
    $failed_entries = [];
    $stmt = $conn->prepare("
        INSERT INTO attendance (user_id, user_type, course_id, date, time_in, status)
        VALUES (?, 'student', ?, ?, NOW(), ?)
        ON DUPLICATE KEY UPDATE time_in = NOW(), status = VALUES(status), updated_at = CURRENT_TIMESTAMP
    ");

    if (!$stmt) {
        throw new Exception("Failed to prepare attendance statement: " . $conn->error);
    }

    foreach ($data as $entry) {
        $user_id = filter_var($entry['user_id'] ?? '', FILTER_VALIDATE_INT);
        $course_id = filter_var($entry['course_id'] ?? '', FILTER_VALIDATE_INT);
        $date = sanitize_input($entry['date'] ?? '');
        $status = sanitize_input($entry['status'] ?? 'absent');

        if (!$user_id || !$course_id || empty($date) || empty($status)) {
            $failed_entries[] = $entry;
            continue;
        }

        $stmt->bind_param("iiss", $user_id, $course_id, $date, $status);
        if (!$stmt->execute()) {
            $failed_entries[] = $entry;
            error_log("Attendance submission failed for user_id: {$user_id}, course_id: {$course_id}, Error: " . $stmt->error);
        }
    }
    $stmt->close();

    if (empty($failed_entries)) {
        $conn->commit();
        log_activity($_SESSION['user_id'], 'Attendance Submitted', "Teacher submitted attendance for " . count($data) . " students.");
        json_response(true, "Attendance submitted successfully!");
    } else {
        $conn->rollback();
        log_activity($_SESSION['user_id'], 'Attendance Submission Failed', "Teacher failed to submit attendance for some entries.");
        json_response(false, "Some attendance entries failed to save. Please check logs.", ['failed_entries' => $failed_entries], 500);
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log("API Error (submit-attendance.php): " . $e->getMessage());
    json_response(false, "An unexpected error occurred: " . $e->getMessage(), null, $e->getCode() ?: 500);
}
```