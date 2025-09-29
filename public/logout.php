<?php
/**
 * VisionNEX PHP Attendance System - Logout Handler
 * Securely destroys user session and redirects to login page
 */

require_once "config/config.php";

// Log the logout activity if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['role'] ?? 'unknown';
    log_activity($user_id, 'User Logout', "User logged out from {$user_type} dashboard");
}

// Destroy session
session_unset();
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
header("Location: login.php");
exit;
?>