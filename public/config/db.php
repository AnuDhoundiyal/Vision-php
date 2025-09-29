<?php
$servername = "localhost";
$username   = "root";
$password   = "";  // Set your actual password here
$dbname     = "system"; 

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

function get_db() {
    global $conn;
    return $conn;
}
?>
