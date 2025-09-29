<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../public/config/db.php';

// Get student ID from session or request
session_start();
$student_id = $_SESSION['student_id'] ?? $_GET['student_id'] ?? 0;

if (!$student_id) {
    echo json_encode(['error' => 'No student ID provided']);
    exit;
}

// Get student basic info
$student = [];
$stmt = $conn->prepare("SELECT s.*, c.name AS course_name FROM students s 
                        LEFT JOIN courses c ON s.course_id = c.id 
                        WHERE s.id = ? OR s.student_id = ?");
$stmt->bind_param('is', $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $student = $row;
} else {
    echo json_encode(['error' => 'Student not found']);
    exit;
}

// Get attendance statistics
$attendance = [
    'overall' => 0,
    'present' => 0,
    'absent' => 0,
    'late' => 0,
    'weekly' => []
];

// Sample query for attendance (adjust based on your schema)
$result = $conn->query("SELECT status, date FROM attendance WHERE student_id = {$student['id']} ORDER BY date DESC");
if ($result && $result->num_rows > 0) {
    $total = $result->num_rows;
    $present = 0;
    $weekly = [];
    
    // Get dates for the last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $weekly[$date] = ['date' => $date, 'status' => 'absent', 'formatted_date' => date('D', strtotime($date))];
    }
    
    while ($row = $result->fetch_assoc()) {
        if ($row['status'] == 'present') {
            $present++;
            $attendance['present']++;
        } elseif ($row['status'] == 'late') {
            $attendance['late']++;
        } else {
            $attendance['absent']++;
        }
        
        // Add to weekly data if within last 7 days
        $date = substr($row['date'], 0, 10);
        if (isset($weekly[$date])) {
            $weekly[$date]['status'] = $row['status'];
        }
    }
    
    $attendance['overall'] = round(($present / $total) * 100);
    $attendance['weekly'] = array_values($weekly);
}

// Get today's schedule
$today = date('Y-m-d');
$schedule = [];
$result = $conn->query("SELECT c.name AS course_name, t.name AS teacher_name, s.start_time, s.end_time, s.room 
                       FROM schedule s 
                       JOIN courses c ON s.course_id = c.id 
                       JOIN teachers t ON s.teacher_id = t.id 
                       WHERE s.date = '$today' AND s.course_id = {$student['course_id']}
                       ORDER BY s.start_time ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
}

// Get enrolled classes
$classes = [];
$result = $conn->query("SELECT c.name, c.description, t.name AS teacher 
                       FROM courses c 
                       JOIN teachers t ON c.teacher_id = t.id 
                       WHERE c.id = {$student['course_id']}");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Get recent activity
$activity = [];
$result = $conn->query("SELECT 'attendance' AS type, date, status AS details FROM attendance 
                       WHERE student_id = {$student['id']} 
                       ORDER BY date DESC LIMIT 5");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $activity[] = [
            'type' => $row['type'],
            'date' => $row['date'],
            'details' => $row['details'],
            'message' => 'You were marked ' . $row['details'] . ' on ' . date('M d, Y', strtotime($row['date']))
        ];
    }
}

// Get syllabus progress
$syllabus = [
    'progress' => rand(60, 90), // Placeholder - replace with actual calculation
    'topics' => []
];

// Return all data
echo json_encode([
    'student' => $student,
    'attendance' => $attendance,
    'schedule' => $schedule,
    'classes' => $classes,
    'activity' => $activity,
    'syllabus' => $syllabus
]);