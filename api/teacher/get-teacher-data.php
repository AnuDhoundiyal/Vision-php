```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('teacher'); // Ensure only teachers can access this API

header('Content-Type: application/json');

$conn = $db->getConnection();
$teacher_user_id = $_SESSION['user_id'];

$response = [
    'success' => false,
    'message' => 'Failed to fetch teacher data.',
    'data' => []
];

try {
    // 1. Get Teacher Basic Info
    $teacher = [];
    $stmt = $conn->prepare("
        SELECT 
            t.id AS teacher_id, t.employee_id, t.phone, t.address, t.position, t.joining_date, t.status,
            u.full_name, u.email, u.profile_image,
            d.name AS department_name
        FROM teachers t
        JOIN users u ON t.user_id = u.id
        LEFT JOIN departments d ON t.department_id = d.id
        WHERE u.id = ?
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare teacher info query: " . $conn->error);
    }
    $stmt->bind_param("i", $teacher_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $teacher = $row;
    } else {
        throw new Exception("Teacher not found for user ID: " . $teacher_user_id);
    }
    $stmt->close();

    // 2. Get Assigned Classes
    $assigned_classes = [];
    $stmt = $conn->prepare("
        SELECT 
            tc.course_id,
            c.name AS course_name, c.batch, c.division,
            d.name AS department_name
        FROM teacher_courses tc
        JOIN courses c ON tc.course_id = c.id
        LEFT JOIN departments d ON c.department_id = d.id
        WHERE tc.teacher_id = ?
        ORDER BY c.name
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare assigned classes query: " . $conn->error);
    }
    $stmt->bind_param("i", $teacher['teacher_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Get student count for each class
        $student_count = 0;
        $stmtStudents = $conn->prepare("SELECT COUNT(id) FROM students WHERE course_id = ? AND batch = ? AND division = ?");
        if ($stmtStudents) {
            $stmtStudents->bind_param("iss", $row['course_id'], $row['batch'], $row['division']);
            $stmtStudents->execute();
            $student_count = $stmtStudents->get_result()->fetch_row()[0];
            $stmtStudents->close();
        }
        $row['student_count'] = $student_count;
        $assigned_classes[] = $row;
    }
    $stmt->close();

    // 3. Get Today's Schedule
    $today_schedule = [];
    $today_day_of_week = date('l'); // e.g., Monday
    $stmt = $conn->prepare("
        SELECT 
            s.id, s.start_time, s.end_time, s.room,
            c.name AS course_name, c.batch, c.division
        FROM schedule s
        JOIN courses c ON s.course_id = c.id
        WHERE s.teacher_id = ? AND s.day_of_week = ?
        ORDER BY s.start_time
    ");
    if (!$stmt) {
        throw new Exception("Failed to prepare today's schedule query: " . $conn->error);
    }
    $stmt->bind_param("is", $teacher['teacher_id'], $today_day_of_week);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $today_schedule[] = $row;
    }
    $stmt->close();

    // 4. Get Overall Attendance Statistics for assigned classes
    $total_students_across_classes = 0;
    $total_attendance_records = 0;
    $total_present_records = 0;

    foreach ($assigned_classes as $class) {
        $total_students_across_classes += $class['student_count'];

        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present
            FROM attendance
            WHERE course_id = ? AND user_type = 'student'
        ");
        if ($stmt) {
            $stmt->bind_param("i", $class['course_id']);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $total_attendance_records += $result['total'];
            $total_present_records += $result['present'];
            $stmt->close();
        }
    }
    $average_attendance_rate = ($total_attendance_records > 0) ? round(($total_present_records / $total_attendance_records) * 100, 2) : 0;

    // 5. Weekly Attendance Overview (for all assigned classes combined)
    $weekly_attendance_data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $day_label = date('D', strtotime($date));
        
        $present_count = 0;
        $absent_count = 0;
        $late_count = 0;

        $stmt = $conn->prepare("
            SELECT status, COUNT(*) as count
            FROM attendance
            WHERE user_type = 'student' AND date = ? AND course_id IN (SELECT course_id FROM teacher_courses WHERE teacher_id = ?)
            GROUP BY status
        ");
        if ($stmt) {
            $stmt->bind_param("si", $date, $teacher['teacher_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                if ($row['status'] == 'present') $present_count = $row['count'];
                if ($row['status'] == 'absent') $absent_count = $row['count'];
                if ($row['status'] == 'late') $late_count = $row['count'];
            }
            $stmt->close();
        }
        $weekly_attendance_data[] = [
            'date' => $date,
            'day_label' => $day_label,
            'present' => $present_count,
            'absent' => $absent_count,
            'late' => $late_count
        ];
    }


    $response = [
        'success' => true,
        'message' => 'Teacher data fetched successfully.',
        'data' => [
            'teacher_info' => $teacher,
            'assigned_classes' => $assigned_classes,
            'today_schedule' => $today_schedule,
            'stats' => [
                'total_classes' => count($assigned_classes),
                'total_students_assigned' => $total_students_across_classes,
                'today_classes_count' => count($today_schedule),
                'average_attendance_rate' => $average_attendance_rate
            ],
            'weekly_attendance_data' => $weekly_attendance_data
        ]
    ];

} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
    error_log("Teacher Dashboard API Error: " . $e->getMessage());
}

echo json_encode($response);
```