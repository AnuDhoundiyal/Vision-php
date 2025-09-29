<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'system';

// Connect
$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if not exist
$conn->query("
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    batch VARCHAR(20) DEFAULT NULL,
    division VARCHAR(5) DEFAULT NULL
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    batch VARCHAR(20) DEFAULT NULL,
    division VARCHAR(5) DEFAULT NULL,
    department_id INT DEFAULT NULL,
    FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE CASCADE
)
");
?>
