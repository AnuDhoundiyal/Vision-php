<?php
/**
 * VisionNEX PHP Attendance System - Recognition Service Status API
 * Returns the status of the face recognition service
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../public/config/config.php';

try {
    $conn = $db->getConnection();
    
    // Count active users with images
    $studentCount = 0;
    $teacherCount = 0;
    
    $result = $conn->query("
        SELECT COUNT(*) as count FROM students 
        WHERE status = 'active' AND image_path IS NOT NULL AND image_path != ''
    ");
    if ($result) {
        $studentCount = $result->fetch_assoc()['count'];
    }
    
    $result = $conn->query("
        SELECT COUNT(*) as count FROM teachers 
        WHERE status = 'active' AND image_path IS NOT NULL AND image_path != ''
    ");
    if ($result) {
        $teacherCount = $result->fetch_assoc()['count'];
    }
    
    $totalUsers = $studentCount + $teacherCount;
    
    // Check if GD extension is loaded
    $gdLoaded = extension_loaded('gd');
    $imagickLoaded = extension_loaded('imagick');
    
    $response = [
        'success' => true,
        'message' => 'Recognition service online',
        'data' => [
            'status' => 'online',
            'service' => 'VisionNEX PHP Face Recognition',
            'users_loaded' => $totalUsers,
            'students_loaded' => $studentCount,
            'teachers_loaded' => $teacherCount,
            'gd_extension' => $gdLoaded,
            'imagick_extension' => $imagickLoaded,
            'image_processing' => $gdLoaded || $imagickLoaded ? 'available' : 'unavailable',
            'timestamp' => date('c')
        ]
    ];
    
    if (!$gdLoaded && !$imagickLoaded) {
        $response['success'] = false;
        $response['message'] = 'Image processing extensions not available';
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Service error: ' . $e->getMessage(),
        'data' => null
    ];
}

echo json_encode($response);
?>