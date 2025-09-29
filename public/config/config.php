```php
<?php
// config/config.php
// Relative to project root: adjust if needed
$config = [
    'UPLOAD_DIR'      => __DIR__ . '/../uploads',  // server path (absolute)
    'UPLOAD_DIR_REL'  => 'uploads',                // public relative path used in DB
    'MAX_FILE_SIZE'   => 5 * 1024 * 1024,          // 5 MB
    'ALLOWED_MIMES'   => ['image/jpeg','image/png','image/webp'],
    'FACE_RECOGNITION_SERVICE_URL' => 'http://localhost:5000/recognize', // URL to your Python Flask service
    'FACE_RECOGNITION_THRESHOLD' => 0.85 // Minimum confidence for recognition
];

// ensure upload dir exists and is writable
if (!is_dir($config['UPLOAD_DIR'])) {
    if (!mkdir($config['UPLOAD_DIR'], 0755, true)) {
        error_log("Failed to create upload directory: " . $config['UPLOAD_DIR']);
        // Handle error appropriately, e.g., die("Upload directory not writable.");
    }
}

// --- Database Connection ---
// The database connection is now handled by config/database.php
// The global $conn variable is available from there.
require_once __DIR__ . '/../../config/database.php';

// Include common functions
require_once __DIR__ . '/../../includes/functions.php';

// Start session securely
secure_session_start();
?>
```