<?php
require_once __DIR__ . '/public/config/db.php';

// Drop old students table if exists
$conn->query("DROP TABLE IF EXISTS students");

// Create students table
$sql = "CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    student_id VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    course_id INT,
    batch VARCHAR(50),
    division VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(course_id) REFERENCES courses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if($conn->query($sql)) {
    echo "Students table created successfully!";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
