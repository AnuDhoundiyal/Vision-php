<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


error_reporting(0); // turn off warnings/notices
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Path configuration
$students_dir = __DIR__ . '/../../uploads/students/';
$teachers_dir = __DIR__ . '/../../admin/uploads/teachers/';


// Database connection
try {
    require_once __DIR__ . '/../../../public/config/db.php';
    
    // Check if connection is valid
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('Database connection not established');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'data' => ['error' => $e->getMessage()]
    ]);
    exit;
}

// Ensure 'action' parameter exists
$action = $_GET['action'] ?? '';

$response = [
    'success' => false,
    'message' => 'Invalid request',
    'data' => null
];

// Action: recognize
if ($action === 'recognize') {
    if (!isset($_FILES['image'])) {
        $response['message'] = 'No image uploaded';
        echo json_encode($response);
        exit;
    }

    $uploadedPath = $_FILES['image']['tmp_name'];
    $class_id = $_POST['class_id'] ?? null;
    
    // Get students from database
    $students = [];
    $query = "SELECT id, name, roll_number FROM students";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    
    $recognized = false;
    $matchedStudent = null;
    
    if (count($students) > 0) {
        // Simulate recognition with a random student (for demo purposes)
        $randomIndex = array_rand($students);
        $student = $students[$randomIndex];
        
        // 80% chance of successful recognition for demo
        if (rand(1, 100) <= 80) {
            $recognized = true;
            
            // Mark attendance in database
            $student_id = $student['id'];
            $today = date('Y-m-d');
            $time = date('H:i:s');
            
            // Check if attendance already exists for today
            $checkQuery = "SELECT * FROM attendance WHERE student_id = ? AND date = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("is", $student_id, $today);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows == 0) {
                // Insert new attendance record
                $insertQuery = "INSERT INTO attendance (student_id, class_id, date, time, status) VALUES (?, ?, ?, ?, 'present')";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("iiss", $student_id, $class_id, $today, $time);
                $attendanceMarked = $insertStmt->execute();
                $insertStmt->close();
            } else {
                // Already marked attendance today
                $attendanceMarked = true;
            }
            
            $matchedStudent = [
                'id' => $student['id'],
                'name' => $student['name'],
                'roll_number' => $student['roll_number'],
                'confidence' => rand(85, 98) / 100, // Simulated confidence between 85-98%
                'attendance_marked' => $attendanceMarked
            ];
        }
    }

    if ($recognized) {
        $response = [
            'success' => true,
            'message' => 'Face recognized',
            'data' => $matchedStudent
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Face not recognized'
        ];
    }

    echo json_encode($response);
    exit;
}

// Action: faces (API status check)
if ($action === 'faces') {
    // Count students in database
    $query = "SELECT COUNT(*) as count FROM students";
    $result = $conn->query($query);
    $studentCount = 0;
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $studentCount = $row['count'];
    }
    
    $response = [
        'success' => true,
        'message' => 'API online',
        'data' => [
            'students_loaded' => $studentCount,
            'version' => '1.0.0',
            'status' => 'ready'
        ]
    ];
    echo json_encode($response);
    exit;
}

// Default response
echo json_encode($response);
