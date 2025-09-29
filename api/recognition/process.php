<?php
/**
 * VisionNEX PHP Attendance System - Face Recognition Processing API
 * Processes captured images and performs face recognition
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../public/config/config.php';

$response = [
    'success' => false,
    'message' => 'Invalid request',
    'data' => null
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Only POST requests allowed';
    echo json_encode($response);
    exit;
}

if (!isset($_FILES['image'])) {
    $response['message'] = 'No image uploaded';
    echo json_encode($response);
    exit;
}

try {
    $uploadedFile = $_FILES['image'];
    $confidenceThreshold = floatval($_POST['confidence_threshold'] ?? 0.85);
    $classId = intval($_POST['class_id'] ?? 0);
    
    // Validate uploaded file
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $uploadedFile['error']);
    }
    
    // Create temporary file for processing
    $tempDir = sys_get_temp_dir();
    $tempFile = $tempDir . '/capture_' . uniqid() . '.jpg';
    
    if (!move_uploaded_file($uploadedFile['tmp_name'], $tempFile)) {
        throw new Exception('Failed to save uploaded image');
    }
    
    // Get all active users with images for comparison
    $users = [];
    
    // Get students
    $stmt = $conn->prepare("
        SELECT id, name, roll_number as id_number, image_path, 'student' as user_type, class_id
        FROM students 
        WHERE status = 'active' AND image_path IS NOT NULL AND image_path != ''
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    
    // Get teachers
    $stmt = $conn->prepare("
        SELECT id, name, employee_id as id_number, image_path, 'teacher' as user_type, NULL as class_id
        FROM teachers 
        WHERE status = 'active' AND image_path IS NOT NULL AND image_path != ''
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    
    if (empty($users)) {
        throw new Exception('No users with profile images found in database');
    }
    
    // Perform face recognition comparison
    $bestMatch = null;
    $bestConfidence = 0.0;
    
    foreach ($users as $user) {
        $storedImagePath = $config['UPLOAD_DIR'] . '/' . $user['image_path'];
        
        if (!file_exists($storedImagePath)) {
            continue; // Skip if image file doesn't exist
        }
        
        // Compare faces using the function from includes/functions.php
        $confidence = compare_faces($tempFile, $storedImagePath);
        
        if ($confidence > $bestConfidence && $confidence >= $confidenceThreshold) {
            $bestConfidence = $confidence;
            $bestMatch = $user;
        }
    }
    
    // Clean up temporary file
    unlink($tempFile);
    
    if ($bestMatch) {
        // Mark attendance
        $attendanceMarked = markAttendance($bestMatch['id'], $bestMatch['user_type'], $classId ?: $bestMatch['class_id']);
        
        $response = [
            'success' => true,
            'message' => 'Face recognized successfully',
            'data' => [
                'id' => $bestMatch['id'],
                'name' => $bestMatch['name'],
                'id_number' => $bestMatch['id_number'],
                'user_type' => $bestMatch['user_type'],
                'confidence' => $bestConfidence,
                'attendance_marked' => $attendanceMarked
            ]
        ];
        
        // Log successful recognition
        log_activity(null, 'Face Recognition Success', json_encode([
            'user_id' => $bestMatch['id'],
            'user_type' => $bestMatch['user_type'],
            'confidence' => $bestConfidence,
            'attendance_marked' => $attendanceMarked
        ]));
        
    } else {
        $response = [
            'success' => false,
            'message' => 'Face not recognized or confidence below threshold'
        ];
        
        // Log failed recognition
        log_activity(null, 'Face Recognition Failed', json_encode([
            'threshold' => $confidenceThreshold,
            'users_checked' => count($users)
        ]));
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Recognition error: ' . $e->getMessage()
    ];
    
    error_log("Face recognition error: " . $e->getMessage());
    
    // Clean up temp file if it exists
    if (isset($tempFile) && file_exists($tempFile)) {
        unlink($tempFile);
    }
}

echo json_encode($response);

/**
 * Mark attendance for recognized user
 */
function markAttendance($userId, $userType, $classId = null) {
    global $conn;
    
    try {
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Check if attendance already exists for today
        $stmt = $conn->prepare("
            SELECT id, status FROM attendance 
            WHERE user_id = ? AND user_type = ? AND date = ? AND class_id = ?
        ");
        $stmt->bind_param("issi", $userId, $userType, $today, $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        $stmt->close();
        
        if ($existing) {
            // Update existing attendance to 'present' if it was 'absent'
            if ($existing['status'] === 'absent') {
                $stmt = $conn->prepare("
                    UPDATE attendance 
                    SET status = 'present', time_in = ?, recognition_method = 'face_recognition', updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->bind_param("si", $currentTime, $existing['id']);
                $success = $stmt->execute();
                $stmt->close();
                return $success;
            }
            return true; // Already marked present
        } else {
            // Insert new attendance record
            $stmt = $conn->prepare("
                INSERT INTO attendance (user_id, user_type, class_id, date, time_in, status, recognition_method, ip_address, created_at)
                VALUES (?, ?, ?, ?, ?, 'present', 'face_recognition', ?, NOW())
            ");
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt->bind_param("isisss", $userId, $userType, $classId, $today, $currentTime, $ipAddress);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        
    } catch (Exception $e) {
        error_log("Attendance marking error: " . $e->getMessage());
        return false;
    }
}
?>