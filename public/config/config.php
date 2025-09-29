```php
<?php
/**
 * VisionNEX PHP Attendance System - Main Configuration
 * Central configuration file for the entire system
 */

// File upload configuration
$config = [
    'UPLOAD_DIR'      => __DIR__ . '/../../uploads',  // Absolute path to uploads directory
    'UPLOAD_DIR_REL'  => 'uploads',                    // Relative path for web access
    'MAX_FILE_SIZE'   => 5 * 1024 * 1024,             // 5 MB maximum file size
    'ALLOWED_MIMES'   => ['image/jpeg','image/png','image/webp'],
    'FACE_RECOGNITION_THRESHOLD' => 0.85,             // Minimum confidence for face recognition
    'AUTO_CAPTURE_DELAY' => 5,                        // Seconds between auto-captures in kiosk
    'SESSION_TIMEOUT' => 3600,                        // Session timeout in seconds (1 hour)
    'MAX_LOGIN_ATTEMPTS' => 5,                        // Maximum failed login attempts
    'LOCKOUT_DURATION' => 300                         // Account lockout duration in seconds (5 minutes)
];

// Ensure upload directories exist and are writable
if (!is_dir($config['UPLOAD_DIR'])) {
    if (!mkdir($config['UPLOAD_DIR'], 0755, true)) {
        error_log("Failed to create upload directory: " . $config['UPLOAD_DIR']);
        die("Upload directory not writable. Please check permissions.");
    }
}

// Create subdirectories for different user types
$subdirs = ['students', 'teachers', 'admins'];
foreach ($subdirs as $subdir) {
    $path = $config['UPLOAD_DIR'] . '/' . $subdir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// Include database connection and common functions
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Start session securely
secure_session_start();
?>
```