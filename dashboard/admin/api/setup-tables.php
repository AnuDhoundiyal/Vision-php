<?php
require_once __DIR__ . '/../../../public/config/db.php';

$tables = [];

/* --- Courses table (already exists in your case) --- */
$tables['courses'] = "
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    batch VARCHAR(20),
    division VARCHAR(5),
    department_id INT
)";

/* --- Departments table --- */
$tables['departments'] = "
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";

/* --- Students table --- */
$tables['students'] = "
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    student_id VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    course_id INT,
    batch VARCHAR(20),
    division VARCHAR(5),
    profile_image VARCHAR(255),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
)";

/* --- Teachers table --- */
$tables['teachers'] = "
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    department_id INT,
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
)";

/* --- Schedule table (timetable) --- */
$tables['schedule'] = "
CREATE TABLE IF NOT EXISTS schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    batch VARCHAR(20),
    division VARCHAR(5),
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(50),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
)";

/* --- Execution --- */
foreach ($tables as $name => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "✅ Table `$name` is ready.<br>";
    } else {
        echo "❌ Error creating `$name`: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
