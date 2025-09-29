<?php
/**
 * VisionNEX PHP Attendance System - Database Connection Test
 * Use this file to verify your database connection is working properly
 */

require_once "config/database.php";

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test - VisionNEX</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb; }
        .info { color: #17a2b8; background: #d1ecf1; padding: 15px; border-radius: 5px; border: 1px solid #bee5eb; }
        h1 { color: #333; text-align: center; }
        .test-item { margin: 15px 0; padding: 10px; border-left: 4px solid #007bff; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>VisionNEX Database Connection Test</h1>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<div class='success'>✅ <strong>Database Connection Successful!</strong></div>";
    
    // Test basic query
    $result = $conn->query("SELECT VERSION() as mysql_version");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<div class='info'>📊 <strong>MySQL Version:</strong> " . $row['mysql_version'] . "</div>";
    }
    
    // Check if tables exist
    $tables = ['admins', 'students', 'teachers', 'classes', 'attendance', 'system_settings'];
    $existing_tables = [];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $existing_tables[] = $table;
        } else {
            $missing_tables[] = $table;
        }
    }
    
    echo "<div class='test-item'><strong>Table Status Check:</strong></div>";
    
    if (!empty($existing_tables)) {
        echo "<div class='success'>✅ <strong>Existing Tables:</strong> " . implode(', ', $existing_tables) . "</div>";
    }
    
    if (!empty($missing_tables)) {
        echo "<div class='error'>❌ <strong>Missing Tables:</strong> " . implode(', ', $missing_tables) . "<br>";
        echo "<em>Please import database/schema.sql into phpMyAdmin to create missing tables.</em></div>";
    }
    
    // Check admin user
    $admin_check = $conn->query("SELECT COUNT(*) as admin_count FROM admins WHERE status = 'active'");
    if ($admin_check) {
        $admin_row = $admin_check->fetch_assoc();
        if ($admin_row['admin_count'] > 0) {
            echo "<div class='success'>✅ <strong>Admin User:</strong> Found " . $admin_row['admin_count'] . " active admin(s)</div>";
        } else {
            echo "<div class='error'>❌ <strong>Admin User:</strong> No active admin users found. Please check the database import.</div>";
        }
    }
    
    // Check upload directories
    $upload_dirs = ['uploads', 'uploads/students', 'uploads/teachers', 'uploads/admins'];
    echo "<div class='test-item'><strong>Upload Directory Check:</strong></div>";
    
    foreach ($upload_dirs as $dir) {
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                echo "<div class='success'>✅ <strong>$dir:</strong> Exists and writable</div>";
            } else {
                echo "<div class='error'>❌ <strong>$dir:</strong> Exists but not writable. Please set permissions to 755.</div>";
            }
        } else {
            echo "<div class='error'>❌ <strong>$dir:</strong> Directory does not exist. Please create it.</div>";
        }
    }
    
    // PHP Extensions check
    echo "<div class='test-item'><strong>PHP Extensions Check:</strong></div>";
    $required_extensions = ['gd', 'mysqli', 'json', 'fileinfo'];
    
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<div class='success'>✅ <strong>$ext:</strong> Loaded</div>";
        } else {
            echo "<div class='error'>❌ <strong>$ext:</strong> Not loaded. Please enable this extension in php.ini</div>";
        }
    }
    
    echo "<div class='info'>🎉 <strong>Connection test completed!</strong> If all checks pass, your system is ready to use.</div>";
    echo "<div class='test-item'><strong>Next Steps:</strong><br>";
    echo "1. Navigate to <a href='public/login.php'>public/login.php</a> to access the system<br>";
    echo "2. Use default admin credentials: admin@visionnex.com / admin123<br>";
    echo "3. Change the default password immediately after first login</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ <strong>Database Connection Failed!</strong><br>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Please check:</strong><br>";
    echo "• MySQL service is running<br>";
    echo "• Database credentials in config/database.php are correct<br>";
    echo "• Database 'system' exists<br>";
    echo "• User has proper permissions</div>";
}

echo "</div></body></html>";
?>