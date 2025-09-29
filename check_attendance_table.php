<?php
require_once __DIR__ . '/public/config/db.php';

// Check if the attendance table exists
$result = $conn->query("SHOW TABLES LIKE 'attendance'");
if ($result->num_rows == 0) {
    echo "Attendance table does not exist. Creating it now...\n";
    
    // Create the attendance table
    $sql = "CREATE TABLE attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        course_id INT NOT NULL,
        status ENUM('present', 'absent', 'late') NOT NULL DEFAULT 'absent',
        date DATE NOT NULL DEFAULT CURRENT_DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_attendance (student_id, course_id, date)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Attendance table created successfully\n";
    } else {
        echo "Error creating attendance table: " . $conn->error . "\n";
    }
} else {
    echo "Attendance table exists. Checking structure...\n";
    
    // Describe the attendance table
    $result = $conn->query("DESCRIBE attendance");
    echo "Table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
    }
}

// Check if there's any data in the attendance table
$result = $conn->query("SELECT COUNT(*) as count FROM attendance");
$row = $result->fetch_assoc();
echo "\nTotal attendance records: " . $row['count'] . "\n";

// Test the mark_attendance functionality
echo "\nTesting mark_attendance functionality...\n";

// Get a random student ID
$result = $conn->query("SELECT id, course_id FROM students LIMIT 1");
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $student_id = $student['id'];
    $course_id = $student['course_id'];
    $status = 'present';
    $date = date('Y-m-d');
    
    // Insert a test attendance record
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, course_id, status, date, created_at) 
                          VALUES (?, ?, ?, ?, NOW()) 
                          ON DUPLICATE KEY UPDATE status = VALUES(status)");
    $stmt->bind_param('iiss', $student_id, $course_id, $status, $date);
    
    if ($stmt->execute()) {
        echo "Test attendance record inserted successfully for student ID: $student_id\n";
    } else {
        echo "Error inserting test record: " . $stmt->error . "\n";
    }
    
    $stmt->close();
} else {
    echo "No students found in the database\n";
}

$conn->close();
echo "\nCheck complete!\n";