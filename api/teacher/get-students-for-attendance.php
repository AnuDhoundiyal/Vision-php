```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('teacher'); // Ensure only teachers can access this API

header('Content-Type: application/json');

$conn = $db->getConnection();

$course_id = filter_var($_GET['course_id'] ?? '', FILTER_VALIDATE_INT);
$batch = sanitize_input($_GET['batch'] ?? '');
$division = sanitize_input($_GET['division'] ?? '');
$today_date = date('Y-m-d');

$response = [
    'success' => false,
    'message' => 'Failed to fetch students.',
    'data' => []
];

if (!$course_id) {
    json_response(false, "Course ID is required.", null, 400);
}

try {
    $where_clauses = ["s.course_id = ?"];
    $params = [$course_id];
    $types = "i";

    if (!empty($batch)) {
        $where_clauses[] = "s.batch = ?";
        $params[] = $batch;
        $types .= "s";
    }
    if (!empty($division)) {
        $where_clauses[] = "s.division = ?";
        $params[] = $division;
        $types .= "s";
    }

    $where_sql = "WHERE " . implode(" AND ", $where_clauses);

    $query = "
        SELECT
            s.id AS student_table_id, s.student_id_number, s.batch, s.division,
            u.id AS user_id, u.full_name, u.email, u.profile_image,
            a.status AS attendance_status
        FROM students s
        JOIN users u ON s.user_id = u.id
        LEFT JOIN attendance a ON u.id = a.user_id AND a.user_type = 'student' AND a.course_id = ? AND a.date = ?
        $where_sql
        ORDER BY u.full_name ASC
    ";

    // Add course_id and today_date for the LEFT JOIN condition
    array_splice($params, 0, 0, [$today_date]); // Insert today_date at the beginning
    array_splice($params, 0, 0, [$course_id]); // Insert course_id at the beginning
    $types = "iis" . $types; // Update types string accordingly

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare student fetch query: " . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();

    json_response(true, "Students fetched successfully.", $students);

} catch (Exception $e) {
    error_log("API Error (get-students-for-attendance.php): " . $e->getMessage());
    json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
}
```