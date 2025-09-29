-- VisionNEX PHP Attendance System - Complete Database Schema
-- Import this file into phpMyAdmin to create all required tables
-- Database: system

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL UNIQUE,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_admin_email` (`email`),
  KEY `idx_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `classes`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `section` varchar(10) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `total_students` int(11) DEFAULT 0,
  `room_number` varchar(20) DEFAULT NULL,
  `schedule_time` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_class_name` (`class_name`),
  KEY `idx_academic_year` (`academic_year`),
  KEY `fk_class_teacher` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `students`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `roll_number` varchar(50) UNIQUE DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `batch` varchar(20) DEFAULT NULL,
  `division` varchar(10) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `status` enum('active','inactive','graduated','suspended') NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_student_email` (`email`),
  KEY `idx_student_roll` (`roll_number`),
  KEY `idx_student_name` (`name`),
  KEY `fk_student_class` (`class_id`),
  CONSTRAINT `fk_student_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `teachers`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `employee_id` varchar(50) UNIQUE DEFAULT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `assigned_classes` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `joining_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','on_leave','retired') NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_teacher_email` (`email`),
  KEY `idx_teacher_employee_id` (`employee_id`),
  KEY `idx_teacher_department` (`department`),
  KEY `idx_teacher_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `attendance`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','teacher') NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late','on_leave','excused') NOT NULL DEFAULT 'absent',
  `recognition_confidence` decimal(5,4) DEFAULT NULL,
  `recognition_method` enum('face_recognition','manual','kiosk','mobile') DEFAULT 'manual',
  `marked_by` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_daily_attendance` (`user_id`, `user_type`, `class_id`, `date`),
  KEY `idx_attendance_date` (`date`),
  KEY `idx_attendance_user` (`user_id`, `user_type`),
  KEY `idx_attendance_class` (`class_id`),
  KEY `idx_attendance_status` (`status`),
  KEY `idx_attendance_method` (`recognition_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `face_encodings`
-- Store face recognition data for faster processing
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `face_encodings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','teacher') NOT NULL,
  `encoding_data` longtext DEFAULT NULL,
  `image_hash` varchar(64) DEFAULT NULL,
  `confidence_threshold` decimal(3,2) DEFAULT 0.85,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_encoding` (`user_id`, `user_type`),
  KEY `idx_encoding_user` (`user_id`, `user_type`),
  KEY `idx_encoding_hash` (`image_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `system_settings`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_editable` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `activity_logs`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('admin','student','teacher') DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activity_user` (`user_id`, `user_type`),
  KEY `idx_activity_action` (`action`),
  KEY `idx_activity_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `notifications`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','student','teacher') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `action_url` varchar(500) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notification_user` (`user_id`, `user_type`),
  KEY `idx_notification_read` (`is_read`),
  KEY `idx_notification_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Insert default system settings
-- --------------------------------------------------------

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `is_editable`) VALUES
('face_recognition_threshold', '0.85', 'string', 'Minimum confidence threshold for face recognition (0.0 to 1.0)', 1),
('auto_capture_delay', '5', 'integer', 'Delay in seconds for auto-capture in kiosk mode', 1),
('max_login_attempts', '5', 'integer', 'Maximum failed login attempts before account lockout', 1),
('lockout_duration', '300', 'integer', 'Account lockout duration in seconds', 1),
('session_timeout', '3600', 'integer', 'Session timeout in seconds', 1),
('institution_name', 'VisionNEX University', 'string', 'Name of the educational institution', 1),
('academic_year', '2024-2025', 'string', 'Current academic year', 1),
('attendance_grace_period', '15', 'integer', 'Grace period in minutes for late attendance', 1),
('enable_face_recognition', '1', 'boolean', 'Enable or disable face recognition system', 1),
('kiosk_auto_refresh', '30', 'integer', 'Kiosk page auto-refresh interval in seconds', 1);

-- --------------------------------------------------------
-- Insert default admin user
-- Username: admin, Password: admin123
-- --------------------------------------------------------

INSERT INTO `admins` (`username`, `email`, `password`, `full_name`, `status`) VALUES
('admin', 'admin@visionnex.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'active');

-- --------------------------------------------------------
-- Add foreign key constraints after all tables are created
-- --------------------------------------------------------

ALTER TABLE `classes` 
ADD CONSTRAINT `fk_class_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------
-- Create indexes for better performance
-- --------------------------------------------------------

CREATE INDEX `idx_attendance_composite` ON `attendance` (`date`, `user_type`, `status`);
CREATE INDEX `idx_students_composite` ON `students` (`class_id`, `status`);
CREATE INDEX `idx_teachers_composite` ON `teachers` (`department`, `status`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;