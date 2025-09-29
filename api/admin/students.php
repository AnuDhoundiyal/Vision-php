```php
<?php
require_once __DIR__ . '/../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access this API

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$conn = $db->getConnection();

switch ($action) {
    case 'read':
        // DataTables server-side processing
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $searchValue = $_POST['search']['value'] ?? '';
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir = $_POST['order'][0]['dir'] ?? 'asc';
        $columns = $_POST['columns'] ?? [];

        $orderColumn = $columns[$orderColumnIndex]['data'] ?? 'full_name'; // Default sort column

        // Map DataTables column names to actual DB column names
        $columnMap = [
            'full_name' => 'u.full_name',
            'student_id_number' => 's.student_id_number',
            'email' => 'u.email',
            // 'course_name' is a join, handle carefully or sort by u.full_name
        ];
        $orderBy = $columnMap[$orderColumn] ?? 'u.full_name';


        $filterCourseId = $_POST['course_id'] ?? '';
        $filterBatch = $_POST['batch'] ?? '';
        $filterDivision = $_POST['division'] ?? '';

        $where = [];
        $params = [];
        $types = "";

        if (!empty($searchValue)) {
            $where[] = "(u.full_name LIKE ? OR u.email LIKE ? OR s.student_id_number LIKE ? OR c.name LIKE ?)";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $types .= "ssss";
        }
        if (!empty($filterCourseId)) {
            $where[] = "s.course_id = ?";
            $params[] = $filterCourseId;
            $types .= "i";
        }
        if (!empty($filterBatch)) {
            $where[] = "s.batch = ?";
            $params[] = $filterBatch;
            $types .= "s";
        }
        if (!empty($filterDivision)) {
            $where[] = "s.division = ?";
            $params[] = $filterDivision;
            $types .= "s";
        }

        $whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

        // Total records without filtering
        $totalRecordsQuery = "SELECT COUNT(s.id) FROM students s JOIN users u ON s.user_id = u.id LEFT JOIN courses c ON s.course_id = c.id";
        $totalRecordsResult = $conn->query($totalRecordsQuery);
        $totalRecords = $totalRecordsResult->fetch_row()[0];

        // Total records with filtering
        $filteredRecordsQuery = "SELECT COUNT(s.id) FROM students s JOIN users u ON s.user_id = u.id LEFT JOIN courses c ON s.course_id = c.id $whereSql";
        $stmtFiltered = $conn->prepare($filteredRecordsQuery);
        if ($stmtFiltered) {
            if (!empty($types)) {
                $stmtFiltered->bind_param($types, ...$params);
            }
            $stmtFiltered->execute();
            $filteredRecords = $stmtFiltered->get_result()->fetch_row()[0];
            $stmtFiltered->close();
        } else {
            json_response(false, "Failed to prepare filtered count query: " . $conn->error, null, 500);
        }

        // Fetch data
        $query = "SELECT s.id, s.student_id_number, s.phone, s.address, s.batch, s.division, s.status,
                         u.id AS user_id, u.full_name, u.email, u.profile_image,
                         c.name AS course_name
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  LEFT JOIN courses c ON s.course_id = c.id
                  $whereSql
                  ORDER BY $orderBy $orderDir
                  LIMIT ?, ?";
        
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $params[] = $start;
            $params[] = $length;
            $types .= "ii"; // Add types for LIMIT parameters

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();

            json_response(true, "Students fetched successfully", [
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($filteredRecords),
                "data" => $data
            ]);
        } else {
            json_response(false, "Failed to prepare student fetch query: " . $conn->error, null, 500);
        }
        break;

    case 'get':
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) {
            json_response(false, "Invalid student ID provided.", null, 400);
        }

        $query = "SELECT s.id, s.student_id_number, s.phone, s.address, s.course_id, s.batch, s.division, s.status,
                         u.id AS user_id, u.full_name, u.email, u.profile_image
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  WHERE s.id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $student = $result->fetch_assoc();
            $stmt->close();

            if ($student) {
                json_response(true, "Student fetched successfully", $student);
            } else {
                json_response(false, "Student not found.", null, 404);
            }
        } else {
            json_response(false, "Failed to prepare get student query: " . $conn->error, null, 500);
        }
        break;

    case 'create':
        $conn->begin_transaction();
        try {
            $fullName = sanitize_input($_POST['full_name'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            $password = $_POST['password'] ?? ''; // Will be hashed
            $studentIdNumber = sanitize_input($_POST['student_id_number'] ?? '');
            $phone = sanitize_input($_POST['phone'] ?? '');
            $address = sanitize_input($_POST['address'] ?? '');
            $courseId = filter_var($_POST['course_id'] ?? '', FILTER_VALIDATE_INT);
            $batch = sanitize_input($_POST['batch'] ?? '');
            $division = sanitize_input($_POST['division'] ?? '');
            $status = sanitize_input($_POST['status'] ?? 'active');

            // Validate inputs
            if (empty($fullName) || empty($email) || empty($password) || !$courseId || strlen($password) < 6) {
                throw new Exception("Full name, email, course, and a password of at least 6 characters are required.", 400);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.", 400);
            }

            // Check if email already exists
            $stmtCheckEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmtCheckEmail->bind_param("s", $email);
            $stmtCheckEmail->execute();
            $stmtCheckEmail->store_result();
            if ($stmtCheckEmail->num_rows > 0) {
                throw new Exception("Email already registered.", 409);
            }
            $stmtCheckEmail->close();

            // Handle profile image upload
            $profileImageRelPath = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = upload_file($_FILES['profile_image'], $config['UPLOAD_DIR'] . '/students');
                if (!$uploadResult['success']) {
                    throw new Exception("Image upload failed: " . $uploadResult['message'], 400);
                }
                $profileImageRelPath = 'students/' . $uploadResult['filename']; // Store path relative to 'uploads'
            }

            // 1. Create user entry
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmtUser = $conn->prepare("INSERT INTO users (full_name, email, password, role, profile_image) VALUES (?, ?, ?, 'student', ?)");
            if (!$stmtUser) { throw new Exception("Failed to prepare user insert: " . $conn->error, 500); }
            $stmtUser->bind_param("ssss", $fullName, $email, $hashedPassword, $profileImageRelPath);
            if (!$stmtUser->execute()) { throw new Exception("Failed to create user: " . $stmtUser->error, 500); }
            $userId = $db->getLastInsertId();
            $stmtUser->close();

            // 2. Create student entry
            $stmtStudent = $conn->prepare("INSERT INTO students (user_id, student_id_number, phone, address, course_id, batch, division, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmtStudent) { throw new Exception("Failed to prepare student insert: " . $conn->error, 500); }
            $stmtStudent->bind_param("isssiiss", $userId, $studentIdNumber, $phone, $address, $courseId, $batch, $division, $status);
            if (!$stmtStudent->execute()) { throw new Exception("Failed to create student: " . $stmtStudent->error, 500); }
            $stmtStudent->close();

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Student Created', "Admin created student: {$fullName} (ID: {$userId})");
            json_response(true, "Student created successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Student Creation Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'update':
        $conn->begin_transaction();
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            $fullName = sanitize_input($_POST['full_name'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            $password = $_POST['password'] ?? ''; // Optional, only update if provided
            $studentIdNumber = sanitize_input($_POST['student_id_number'] ?? '');
            $phone = sanitize_input($_POST['phone'] ?? '');
            $address = sanitize_input($_POST['address'] ?? '');
            $courseId = filter_var($_POST['course_id'] ?? '', FILTER_VALIDATE_INT);
            $batch = sanitize_input($_POST['batch'] ?? '');
            $division = sanitize_input($_POST['division'] ?? '');
            $status = sanitize_input($_POST['status'] ?? 'active');

            if (!$id || empty($fullName) || empty($email) || !$courseId) {
                throw new Exception("Student ID, full name, email, and course are required.", 400);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.", 400);
            }
            if (!empty($password) && strlen($password) < 6) {
                throw new Exception("Password must be at least 6 characters.", 400);
            }

            // Get current user_id and profile_image path
            $stmtGetStudent = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
            $stmtGetStudent->bind_param("i", $id);
            $stmtGetStudent->execute();
            $resultGetStudent = $stmtGetStudent->get_result();
            $studentData = $resultGetStudent->fetch_assoc();
            $stmtGetStudent->close();
            if (!$studentData) { throw new Exception("Student not found.", 404); }
            $userId = $studentData['user_id'];

            $stmtGetUser = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmtGetUser->bind_param("i", $userId);
            $stmtGetUser->execute();
            $resultGetUser = $stmtGetUser->get_result();
            $userData = $resultGetUser->fetch_assoc();
            $stmtGetUser->close();
            $oldProfileImage = $userData['profile_image'];

            // Check if email already exists for another user
            $stmtCheckEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmtCheckEmail->bind_param("si", $email, $userId);
            $stmtCheckEmail->execute();
            $stmtCheckEmail->store_result();
            if ($stmtCheckEmail->num_rows > 0) {
                throw new Exception("Email already registered by another user.", 409);
            }
            $stmtCheckEmail->close();

            // Handle profile image upload
            $profileImageRelPath = $oldProfileImage; // Keep old image by default
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = upload_file($_FILES['profile_image'], $config['UPLOAD_DIR'] . '/students');
                if (!$uploadResult['success']) {
                    throw new Exception("Image upload failed: " . $uploadResult['message'], 400);
                }
                $profileImageRelPath = 'students/' . $uploadResult['filename'];
                // Optionally delete old image file
                if ($oldProfileImage && file_exists($config['UPLOAD_DIR'] . '/' . $oldProfileImage)) {
                    unlink($config['UPLOAD_DIR'] . '/' . $oldProfileImage);
                }
            }

            // 1. Update user entry
            $userUpdateFields = "full_name = ?, email = ?, profile_image = ?";
            $userUpdateParams = [$fullName, $email, $profileImageRelPath];
            $userUpdateTypes = "sss";

            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $userUpdateFields .= ", password = ?";
                $userUpdateParams[] = $hashedPassword;
                $userUpdateTypes .= "s";
            }
            $userUpdateParams[] = $userId;
            $userUpdateTypes .= "i";

            $stmtUser = $conn->prepare("UPDATE users SET $userUpdateFields WHERE id = ?");
            if (!$stmtUser) { throw new Exception("Failed to prepare user update: " . $conn->error, 500); }
            $stmtUser->bind_param($userUpdateTypes, ...$userUpdateParams);
            if (!$stmtUser->execute()) { throw new Exception("Failed to update user: " . $stmtUser->error, 500); }
            $stmtUser->close();

            // 2. Update student entry
            $stmtStudent = $conn->prepare("UPDATE students SET student_id_number = ?, phone = ?, address = ?, course_id = ?, batch = ?, division = ?, status = ? WHERE id = ?");
            if (!$stmtStudent) { throw new Exception("Failed to prepare student update: " . $conn->error, 500); }
            $stmtStudent->bind_param("sssiissi", $studentIdNumber, $phone, $address, $courseId, $batch, $division, $status, $id);
            if (!$stmtStudent->execute()) { throw new Exception("Failed to update student: " . $stmtStudent->error, 500); }
            $stmtStudent->close();

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Student Updated', "Admin updated student: {$fullName} (ID: {$id})");
            json_response(true, "Student updated successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Student Update Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'delete':
        $conn->begin_transaction();
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception("Invalid student ID provided.", 400);
            }

            // Get user_id and profile_image path before deleting
            $stmtGetStudent = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
            $stmtGetStudent->bind_param("i", $id);
            $stmtGetStudent->execute();
            $resultGetStudent = $stmtGetStudent->get_result();
            $studentData = $resultGetStudent->fetch_assoc();
            $stmtGetStudent->close();
            if (!$studentData) { throw new Exception("Student not found.", 404); }
            $userId = $studentData['user_id'];

            $stmtGetUser = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmtGetUser->bind_param("i", $userId);
            $stmtGetUser->execute();
            $resultGetUser = $stmtGetUser->get_result();
            $userData = $resultGetUser->fetch_assoc();
            $stmtGetUser->close();
            $profileImageToDelete = $userData['profile_image'];

            // Deleting from 'users' table will cascade delete from 'students' due to FOREIGN KEY ON DELETE CASCADE
            $stmtDeleteUser = $conn->prepare("DELETE FROM users WHERE id = ?");
            if (!$stmtDeleteUser) { throw new Exception("Failed to prepare user delete: " . $conn->error, 500); }
            $stmtDeleteUser->bind_param("i", $userId);
            if (!$stmtDeleteUser->execute()) { throw new Exception("Failed to delete user: " . $stmtDeleteUser->error, 500); }
            $stmtDeleteUser->close();

            // Delete profile image file if it exists
            if ($profileImageToDelete && file_exists($config['UPLOAD_DIR'] . '/' . $profileImageToDelete)) {
                unlink($config['UPLOAD_DIR'] . '/' . $profileImageToDelete);
            }

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Student Deleted', "Admin deleted student (User ID: {$userId}, Student ID: {$id})");
            json_response(true, "Student deleted successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Student Deletion Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    default:
        json_response(false, "Invalid action provided.", null, 400);
        break;
}
```