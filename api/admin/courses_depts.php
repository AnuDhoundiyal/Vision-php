```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access this API

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$conn = $db->getConnection();

switch ($action) {
    case 'add_department':
        $conn->begin_transaction();
        try {
            $name = sanitize_input($_POST['department_name'] ?? '');
            $batch = sanitize_input($_POST['department_batch'] ?? '');
            $division = sanitize_input($_POST['department_division'] ?? '');

            if (empty($name)) {
                throw new Exception("Department name is required.", 400);
            }

            $stmt = $conn->prepare("INSERT INTO departments (name, batch, division) VALUES (?, ?, ?)");
            if (!$stmt) { throw new Exception("Failed to prepare department insert: " . $conn->error, 500); }
            $stmt->bind_param("sss", $name, $batch, $division);
            if (!$stmt->execute()) { throw new Exception("Failed to add department: " . $stmt->error, 500); }
            $id = $db->getLastInsertId();
            $stmt->close();

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Department Added', "Admin added department: {$name} (ID: {$id})");
            json_response(true, "Department '{$name}' added successfully!", ['id' => $id, 'name' => $name, 'batch' => $batch, 'division' => $division]);

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Department Add Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'add_course':
        $conn->begin_transaction();
        try {
            $name = sanitize_input($_POST['course_name'] ?? '');
            $batch = sanitize_input($_POST['course_batch'] ?? '');
            $division = sanitize_input($_POST['course_division'] ?? '');
            $departmentId = filter_var($_POST['department_id'] ?? '', FILTER_VALIDATE_INT);

            if (empty($name) || !$departmentId) {
                throw new Exception("Course name and department are required.", 400);
            }

            $stmt = $conn->prepare("INSERT INTO courses (name, batch, division, department_id) VALUES (?, ?, ?, ?)");
            if (!$stmt) { throw new Exception("Failed to prepare course insert: " . $conn->error, 500); }
            $stmt->bind_param("sssi", $name, $batch, $division, $departmentId);
            if (!$stmt->execute()) { throw new Exception("Failed to add course: " . $stmt->error, 500); }
            $id = $db->getLastInsertId();
            $stmt->close();

            // Get department name for response
            $deptName = null;
            $stmtDept = $conn->prepare("SELECT name FROM departments WHERE id = ?");
            if ($stmtDept) {
                $stmtDept->bind_param("i", $departmentId);
                $stmtDept->execute();
                $resultDept = $stmtDept->get_result();
                if ($row = $resultDept->fetch_assoc()) {
                    $deptName = $row['name'];
                }
                $stmtDept->close();
            }

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Course Added', "Admin added course: {$name} (ID: {$id})");
            json_response(true, "Course '{$name}' added successfully!", ['id' => $id, 'name' => $name, 'batch' => $batch, 'division' => $division, 'dept_name' => $deptName]);

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Course Add Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'delete_department':
        $conn->begin_transaction();
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception("Invalid department ID provided.", 400);
            }

            $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
            if (!$stmt) { throw new Exception("Failed to prepare department delete: " . $conn->error, 500); }
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) { throw new Exception("Failed to delete department: " . $stmt->error, 500); }
            $stmt->close();

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Department Deleted', "Admin deleted department (ID: {$id})");
            json_response(true, "Department deleted successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Department Delete Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'delete_course':
        $conn->begin_transaction();
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception("Invalid course ID provided.", 400);
            }

            $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
            if (!$stmt) { throw new Exception("Failed to prepare course delete: " . $conn->error, 500); }
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) { throw new Exception("Failed to delete course: " . $stmt->error, 500); }
            $stmt->close();

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Course Deleted', "Admin deleted course (ID: {$id})");
            json_response(true, "Course deleted successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Course Delete Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    default:
        json_response(false, "Invalid action provided.", null, 400);
        break;
}
```