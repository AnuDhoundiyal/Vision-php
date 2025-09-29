<?php
/**
 * VisionNex ERA - Common Functions
 * Shared utilities and helper functions
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Secure session management
 */
function secure_session_start() {
    if (session_status() == PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Check if user is logged in and has required role
 */
function check_auth($required_role = null) {
    secure_session_start();
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: /login.php');
        exit;
    }
    
    if ($required_role && $_SESSION['role'] !== $required_role) {
        header('Location: /unauthorized.php');
        exit;
    }
    
    return true;
}

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate secure random password
 */
function generate_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Send JSON response
 */
function json_response($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Upload file with validation
 */
function upload_file($file, $upload_dir, $allowed_types = ['jpg', 'jpeg', 'png']) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return ['success' => false, 'message' => 'File too large'];
    }
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename, 'path' => $target_path];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
}

/**
 * Simple face recognition using image comparison
 * This is a basic implementation - in production, use proper face recognition libraries
 */
function compare_faces($image1_path, $image2_path) {
    if (!file_exists($image1_path) || !file_exists($image2_path)) {
        return 0;
    }
    
    // Basic image comparison using histogram
    $img1 = imagecreatefromstring(file_get_contents($image1_path));
    $img2 = imagecreatefromstring(file_get_contents($image2_path));
    
    if (!$img1 || !$img2) {
        return 0;
    }
    
    // Resize images for comparison
    $img1_resized = imagescale($img1, 100, 100);
    $img2_resized = imagescale($img2, 100, 100);
    
    $similarity = 0;
    $total_pixels = 100 * 100;
    
    for ($x = 0; $x < 100; $x++) {
        for ($y = 0; $y < 100; $y++) {
            $rgb1 = imagecolorat($img1_resized, $x, $y);
            $rgb2 = imagecolorat($img2_resized, $x, $y);
            
            $r1 = ($rgb1 >> 16) & 0xFF;
            $g1 = ($rgb1 >> 8) & 0xFF;
            $b1 = $rgb1 & 0xFF;
            
            $r2 = ($rgb2 >> 16) & 0xFF;
            $g2 = ($rgb2 >> 8) & 0xFF;
            $b2 = $rgb2 & 0xFF;
            
            $diff = abs($r1 - $r2) + abs($g1 - $g2) + abs($b1 - $b2);
            $similarity += (765 - $diff) / 765; // 765 = max possible difference
        }
    }
    
    imagedestroy($img1);
    imagedestroy($img2);
    imagedestroy($img1_resized);
    imagedestroy($img2_resized);
    
    return $similarity / $total_pixels;
}

/**
 * Log system activity
 */
function log_activity($user_id, $action, $details = '') {
    $db = Database::getInstance();
    $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
}

/**
 * Get user by ID
 */
function get_user($user_id) {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Format date for display
 */
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Calculate attendance percentage
 */
function calculate_attendance_percentage($user_id, $start_date = null, $end_date = null) {
    $db = Database::getInstance();
    
    $where_clause = "WHERE user_id = ?";
    $params = [$user_id];
    $types = "i";
    
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
    
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM attendance $where_clause");
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as present FROM attendance $where_clause AND status = 'present'");
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $present = $stmt->get_result()->fetch_assoc()['present'];
    
    return $total > 0 ? round(($present / $total) * 100, 2) : 0;
}
?>