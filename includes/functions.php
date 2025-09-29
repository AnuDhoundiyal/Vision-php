```php
<?php
/**
 * VisionNEX PHP Attendance System - Common Functions
 * Shared utilities and helper functions
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Secure session management
 */
function secure_session_start() {
    if (session_status() == PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')); // Only secure on HTTPS
        ini_set('session.use_only_cookies', 1);
        session_start();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Check if user is logged in and has required role
 *
 * @param string|array|null $required_role Single role string, array of roles, or null for any logged-in user.
 * @param string $redirect_unauth Path to redirect if not authorized.
 * @param string $redirect_unlogged Path to redirect if not logged in.
 * @return bool True if authorized, false otherwise (after redirection).
 */
function check_auth($required_role = null, $redirect_unauth = '/unauthorized.php', $redirect_unlogged = '/login.php') {
    secure_session_start();
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: ' . $redirect_unlogged);
        exit;
    }
    
    if ($required_role !== null) {
        if (is_string($required_role)) {
            $required_role = [$required_role];
        }
        if (!in_array($_SESSION['role'], $required_role)) {
            header('Location: ' . $redirect_unauth);
            exit;
        }
    }
    
    return true;
}

/**
 * Sanitize input data
 *
 * @param string $data The input string to sanitize.
 * @return string The sanitized string.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate secure random password
 *
 * @param int $length The desired length of the password.
 * @return string The generated password.
 */
function generate_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
    $password = '';
    $char_length = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $char_length)];
    }
    return $password;
}

/**
 * Send JSON response and terminate script.
 *
 * @param bool $success Indicates if the operation was successful.
 * @param string $message A message describing the outcome.
 * @param array|object|null $data Optional data to include in the response.
 * @param int $status_code HTTP status code (e.g., 200, 400, 500).
 */
function json_response($success, $message, $data = null, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Upload file with validation.
 *
 * @param array $file The $_FILES array entry for the uploaded file.
 * @param string $upload_dir The target directory for the upload (absolute path).
 * @param array $allowed_types Allowed file extensions (e.g., ['jpg', 'jpeg', 'png']).
 * @param int $max_size Maximum file size in bytes (default 5MB).
 * @return array An associative array with 'success', 'message', 'filename', 'path', 'relative_path'.
 */
function upload_file($file, $upload_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'webp'], $max_size = 5 * 1024 * 1024) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error: ' . $file['error']];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Max ' . ($max_size / (1024 * 1024)) . 'MB'];
    }
    
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }
    }
    
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = rtrim($upload_dir, '/') . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Determine relative path from project root for database storage
        $project_root = realpath(__DIR__ . '/..'); // Adjust if project root is different
        $relative_path = str_replace($project_root, '', $target_path);
        $relative_path = ltrim(str_replace('\\', '/', $relative_path), '/'); // Normalize slashes and remove leading slash if any
        
        return ['success' => true, 'message' => 'File uploaded successfully.', 'filename' => $filename, 'path' => $target_path, 'relative_path' => $relative_path];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file.'];
}

/**
 * PHP-based face comparison logic using GD library
 * This is a simplified implementation for demonstration purposes
 * For production use, consider integrating with dedicated ML services
 *
 * @param string $captured_image_path Path to the image captured from the camera.
 * @param string $stored_image_path Path to the stored profile image.
 * @return float A confidence score (0.0 to 1.0).
 */
function compare_faces($captured_image_path, $stored_image_path) {
    // PHP-based face comparison using GD library
    // Note: This is a basic implementation. For production systems,
    // consider using dedicated face recognition services for better accuracy
    
    if (!file_exists($captured_image_path) || !file_exists($stored_image_path)) {
        return 0.0;
    }
    
    try {
        // Load images using GD
        $captured = imagecreatefromstring(file_get_contents($captured_image_path));
        $stored = imagecreatefromstring(file_get_contents($stored_image_path));
        
        if (!$captured || !$stored) {
            return 0.0;
        }
        
        // Resize images to standard size for comparison
        $size = 100;
        $captured_resized = imagecreatetruecolor($size, $size);
        $stored_resized = imagecreatetruecolor($size, $size);
        
        imagecopyresampled($captured_resized, $captured, 0, 0, 0, 0, $size, $size, imagesx($captured), imagesy($captured));
        imagecopyresampled($stored_resized, $stored, 0, 0, 0, 0, $size, $size, imagesx($stored), imagesy($stored));
        
        // Simple pixel-by-pixel comparison (basic similarity)
        $total_pixels = $size * $size;
        $matching_pixels = 0;
        
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $captured_color = imagecolorat($captured_resized, $x, $y);
                $stored_color = imagecolorat($stored_resized, $x, $y);
                
                // Extract RGB values
                $captured_rgb = [
                    ($captured_color >> 16) & 0xFF,
                    ($captured_color >> 8) & 0xFF,
                    $captured_color & 0xFF
                ];
                $stored_rgb = [
                    ($stored_color >> 16) & 0xFF,
                    ($stored_color >> 8) & 0xFF,
                    $stored_color & 0xFF
                ];
                
                // Calculate color difference
                $diff = abs($captured_rgb[0] - $stored_rgb[0]) + 
                       abs($captured_rgb[1] - $stored_rgb[1]) + 
                       abs($captured_rgb[2] - $stored_rgb[2]);
                
                // If colors are similar (threshold: 50), count as matching
                if ($diff < 50) {
                    $matching_pixels++;
                }
            }
        }
        
        // Clean up memory
        imagedestroy($captured);
        imagedestroy($stored);
        imagedestroy($captured_resized);
        imagedestroy($stored_resized);
        
        // Calculate confidence as percentage of matching pixels
        $confidence = $matching_pixels / $total_pixels;
        
        // Add some randomization to simulate real-world variance
        $confidence += (rand(-10, 10) / 100);
        $confidence = max(0.0, min(1.0, $confidence)); // Clamp between 0 and 1
        
        return $confidence;
        
    } catch (Exception $e) {
        error_log("Face comparison error: " . $e->getMessage());
        return 0.0;
    }
}


/**
 * Log system activity
 *
 * @param int|null $user_id The ID of the user performing the action.
 * @param string $action A brief description of the action.
 * @param string|null $details More detailed information about the action.
 */
function log_activity($user_id, $action, $details = null) {
    global $db; // Access the global Database instance
    $conn = $db->getConnection();

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $user_id, $action, $details, $ip_address, $user_agent);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Failed to prepare activity log statement: " . $conn->error);
    }
}

/**
 * Get user by ID
 *
 * @param int $user_id The ID of the user.
 * @return array|null User data or null if not found.
 */
function get_user($user_id) {
    global $db;
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT id, full_name, email, role, profile_image FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    return null;
}

/**
 * Format date for display
 *
 * @param string $date The date string.
 * @param string $format The desired date format.
 * @return string The formatted date.
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Calculate attendance percentage
 *
 * @param int $user_id The ID of the user.
 * @param string $user_type The type of user ('student' or 'teacher').
 * @param string|null $start_date Optional start date for the period.
 * @param string|null $end_date Optional end date for the period.
 * @param int|null $course_id Optional course ID to filter by.
 * @return float The attendance percentage.
 */
function calculate_attendance_percentage($user_id, $user_type, $start_date = null, $end_date = null, $course_id = null) {
    global $db;
    $conn = $db->getConnection();
    
    $where_clause = "WHERE user_id = ? AND user_type = ?";
    $params = [$user_id, $user_type];
    $types = "is";
    
    if ($start_date) {
        $where_clause .= " AND date >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if ($end_date) {
        $where_clause .= " AND date <= ?";
        $params[] = $end_date;
        $types .= "s";
    }

    if ($course_id) {
        $where_clause .= " AND course_id = ?";
        $params[] = $course_id;
        $types .= "i";
    }
    
    // Get total attendance records
    $stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM attendance $where_clause");
    if (!$stmt_total) { error_log("Error preparing total attendance query: " . $conn->error); return 0; }
    $stmt_total->bind_param($types, ...$params);
    $stmt_total->execute();
    $total = $stmt_total->get_result()->fetch_assoc()['total'];
    $stmt_total->close();
    
    // Get present attendance records
    $stmt_present = $conn->prepare("SELECT COUNT(*) as present FROM attendance $where_clause AND status = 'present'");
    if (!$stmt_present) { error_log("Error preparing present attendance query: " . $conn->error); return 0; }
    $stmt_present->bind_param($types, ...$params);
    $stmt_present->execute();
    $present = $stmt_present->get_result()->fetch_assoc()['present'];
    $stmt_present->close();
    
    return $total > 0 ? round(($present / $total) * 100, 2) : 0;
}

/**
 * Display a toast notification.
 *
 * @param string $message The message to display.
 * @param string $type The type of notification (success, error, warning, info).
 */
function show_toast($message, $type = 'info') {
    // This function is typically implemented in JavaScript on the frontend.
    // For PHP, we can set a session variable that the frontend JS reads.
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['toast'] = ['message' => $message, 'type' => $type];
}

/**
 * Check and display toast notification from session.
 */
function display_toast_from_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                // Assuming a showToast function exists in global JS or can be defined here
                function showToast(message, type) {
                    const toastContainer = document.getElementById('toast-container');
                    if (!toastContainer) {
                        console.warn('Toast container not found. Create a div with id=\"toast-container\"');
                        return;
                    }
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-4 right-4 p-4 rounded-lg shadow-lg text-white ' + 
                                      (type === 'success' ? 'bg-green-500' : 
                                       type === 'error' ? 'bg-red-500' : 
                                       type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500');
                    toast.textContent = message;
                    toastContainer.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }
                showToast('" . addslashes($toast['message']) . "', '" . addslashes($toast['type']) . "');
            });
        </script>";
        unset($_SESSION['toast']);
    }
}
```