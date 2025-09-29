<?php
// File: create_schedule_table.php
require_once __DIR__ . '/../../../public/config/db.php';  // adjust path if needed

$sql = "
CREATE TABLE IF NOT EXISTS schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    batch VARCHAR(20),
    division VARCHAR(5),
    teacher_id INT,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Run query
if ($conn->query($sql) === TRUE) {
    echo "✅ Table `schedule` created or already exists!";
} else {
    echo "❌ Error creating `schedule`: " . $conn->error;
}

$conn->close();
?>
