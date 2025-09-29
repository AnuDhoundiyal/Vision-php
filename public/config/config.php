<?php
// config/config.php
// Relative to project root: adjust if needed
$config = [
    'UPLOAD_DIR'      => __DIR__ . '/../uploads',  // server path
    'UPLOAD_DIR_REL'  => 'uploads',                // public relative path used in DB
    'MAX_FILE_SIZE'   => 2 * 1024 * 1024,          // 2 MB
    'ALLOWED_MIMES'   => ['image/jpeg','image/png','image/webp'],
];

// ensure upload dir exists and is writable
if (!is_dir($config['UPLOAD_DIR'])) {
    mkdir($config['UPLOAD_DIR'], 0755, true);
}

// --- Database Connection ---
$host = "localhost";
$user = "root";   // XAMPP default user
$pass = "";       // XAMPP default password is empty
$db   = "system";   // same as db.php


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
