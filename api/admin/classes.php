<?php
/**
 * VisionNEX PHP Attendance System - Classes Management API
 * Handles CRUD operations for classes
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../public/config/config.php';
check_auth('admin');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$conn = $db->getConnection();

switch ($action) {
    case 'list':
        try {
            $query = "
                SELECT c.*, t.name as teacher_name,
                       (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
                FROM classes c
                LEFT JOIN teachers t ON c.teacher_id = t.id
                WHERE c.status = 'active'
                ORDER BY c.class_name, c.section
            ";
            
            $result = $conn->query($query);
            $classes = [];
            
            while ($row = $result->fetch_assoc()) {
                $classes[] = $row;
            }
            
            json_response(true, 'Classes fetched successfully', $classes);
            
        } catch (Exception $e) {
            json_response(false, 'Error fetching classes: ' . $e->getMessage(), null, 500);
        }
        break;
        
    case 'create':
        try {
            $className = sanitize_input($_POST['class_name'] ?? '');
            $section = sanitize_input($_POST['section'] ?? '');
            $academicYear = sanitize_input($_POST['academic_year'] ?? '');
            $department = sanitize_input($_POST['department'] ?? '');
            $teacherId = filter_var($_POST['teacher_id'] ?? '', FILTER_VALIDATE_INT);
            $roomNumber = sanitize_input($_POST['room_number'] ?? '');
            $scheduleTime = sanitize_input($_POST['schedule_time'] ?? '');
            
            if (empty($className) || empty($academicYear)) {
                throw new Exception('Class name and academic year are required', 400);
            }
            
            $stmt = $conn->prepare("
                INSERT INTO classes (class_name, section, academic_year, department, teacher_id, room_number, schedule_time)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssiss", $className, $section, $academicYear, $department, $teacherId, $roomNumber, $scheduleTime);
            
            if ($stmt->execute()) {
                $classId = $db->getLastInsertId();
                log_activity($_SESSION['user_id'], 'Class Created', "Created class: {$className} - {$section}");
                json_response(true, 'Class created successfully', ['id' => $classId]);
            } else {
                throw new Exception('Failed to create class: ' . $stmt->error);
            }
            $stmt->close();
            
        } catch (Exception $e) {
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;
        
    case 'update':
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            $className = sanitize_input($_POST['class_name'] ?? '');
            $section = sanitize_input($_POST['section'] ?? '');
            $academicYear = sanitize_input($_POST['academic_year'] ?? '');
            $department = sanitize_input($_POST['department'] ?? '');
            $teacherId = filter_var($_POST['teacher_id'] ?? '', FILTER_VALIDATE_INT);
            $roomNumber = sanitize_input($_POST['room_number'] ?? '');
            $scheduleTime = sanitize_input($_POST['schedule_time'] ?? '');
            
            if (!$id || empty($className) || empty($academicYear)) {
                throw new Exception('Class ID, name, and academic year are required', 400);
            }
            
            $stmt = $conn->prepare("
                UPDATE classes 
                SET class_name = ?, section = ?, academic_year = ?, department = ?, 
                    teacher_id = ?, room_number = ?, schedule_time = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("ssssissi", $className, $section, $academicYear, $department, $teacherId, $roomNumber, $scheduleTime, $id);
            
            if ($stmt->execute()) {
                log_activity($_SESSION['user_id'], 'Class Updated', "Updated class ID: {$id}");
                json_response(true, 'Class updated successfully');
            } else {
                throw new Exception('Failed to update class: ' . $stmt->error);
            }
            $stmt->close();
            
        } catch (Exception $e) {
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;
        
    case 'delete':
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            
            if (!$id) {
                throw new Exception('Class ID is required', 400);
            }
            
            // Soft delete - set status to inactive
            $stmt = $conn->prepare("UPDATE classes SET status = 'inactive', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                log_activity($_SESSION['user_id'], 'Class Deleted', "Deleted class ID: {$id}");
                json_response(true, 'Class deleted successfully');
            } else {
                throw new Exception('Failed to delete class: ' . $stmt->error);
            }
            $stmt->close();
            
        } catch (Exception $e) {
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;
        
    default:
        json_response(false, 'Invalid action specified', null, 400);
        break;
}
?>