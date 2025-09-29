<?php
require_once 'C:/xampp/htdocs/php_project/public/config/db.php';

$sql = "CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    status VARCHAR(20) NOT NULL,
    date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (student_id, course_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "Attendance table created successfully!";
} else {
    echo "Error creating table: " . $conn->error;
}
