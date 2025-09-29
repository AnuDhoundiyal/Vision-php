<?php
require_once __DIR__ . '/../../../public/config/db.php';


// Departments
$conn->query("
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    batch VARCHAR(20) DEFAULT NULL,
    division VARCHAR(5) DEFAULT NULL
)
");

// Courses
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

// Students
$conn->query("
CREATE TABLE IF NOT EXISTS students (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Teachers (if needed)
$conn->query("
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    subject VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

echo "✅ All tables created successfully!";
