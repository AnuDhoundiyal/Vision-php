<?php
require_once "C:/xampp/htdocs/php_project/public/config/db.php";

// Tables to create
$tables = [

    // Divisions table
    "divisions" => "CREATE TABLE IF NOT EXISTS divisions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        teacher_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
    ) ENGINE=InnoDB",

    // Subjects table (if not fully covered by courses)
    "subjects" => "CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        teacher_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
    ) ENGINE=InnoDB",

    // Classes table
    "classes" => "CREATE TABLE IF NOT EXISTS classes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_id INT NOT NULL,
        division_id INT NOT NULL,
        class_date DATE NOT NULL,
        start_time TIME,
        end_time TIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
        FOREIGN KEY (division_id) REFERENCES divisions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB"
];

// Loop and create tables
foreach ($tables as $name => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "✅ Table '$name' created successfully.<br>";
    } else {
        echo "❌ Error creating table '$name': " . $conn->error . "<br>";
    }
}

$conn->close();
?>
