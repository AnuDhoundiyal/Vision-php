<?php
header('Content-Type: application/json');

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

$response = [
    'success' => false,
    'message' => 'No classes found',
    'data' => []
];

try {
    // Get classes from database
    $query = "SELECT id, subject_name, batch, division FROM classes ORDER BY subject_name";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
        
        $response = [
            'success' => true,
            'message' => count($classes) . ' classes found',
            'data' => $classes
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error retrieving classes',
        'data' => ['error' => $e->getMessage()]
    ];
}

echo json_encode($response);