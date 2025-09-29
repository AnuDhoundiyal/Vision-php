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

        // Default sort column mapping
        $orderColumn = $columns[$orderColumnIndex]['data'] ?? 'full_name';
        $columnMap = [
            'full_name' => 'u.full_name',
            'employee_id' => 't.employee_id',
            'email' => 'u.email',
            'department_name' => 'd.name',
            'position' => 't.position',
            'status' => 't.status'
        ];
        $orderBy = $columnMap[$orderColumn] ?? 'u.full_name';

        $filterDepartmentId = $_POST['department_id'] ?? '';
        $filterCourseId = $_POST['course_id'] ?? '';

        $where = [];
        $params = [];
        $types = "";

        if (!empty($searchValue)) {
            $where[] = "(u.full_name LIKE ? OR u.email LIKE ? OR t.employee_id LIKE ? OR d.name LIKE ? OR t.position LIKE ?)";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $params[] = "%" . $searchValue . "%";
            $types .= "sssss";
        }
        if (!empty($filterDepartmentId)) {
            $where[] = "t.department_id = ?";
            $params[] = $filterDepartmentId;
            $types .= "i";
        }
        if (!empty($filterCourseId)) {
            $where[] = "tc.course_id = ?";
            $params[] = $filterCourseId;
            $types .= "i";
        }

        $whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

        // Join teacher_courses if filtering by course
        $joinTeacherCourses = !empty($filterCourseId) ? "LEFT JOIN teacher_courses tc ON t.id = tc.teacher_id" : "";

        // Total records without filtering
        $totalRecordsQuery = "SELECT COUNT(t.id) FROM teachers t JOIN users u ON t.user_id = u.id LEFT JOIN departments d ON t.department_id = d.id";
        $totalRecordsResult = $conn->query($totalRecordsQuery);
        $totalRecords = $totalRecordsResult->fetch_row()[0];

        // Total records with filtering
        $filteredRecordsQuery = "SELECT COUNT(DISTINCT t.id) FROM teachers t JOIN users u ON t.user_id = u.id LEFT JOIN departments d ON t.department_id = d.id $joinTeacherCourses $whereSql";
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
        $query = "SELECT 
                    t.id, t.employee_id, t.phone, t.address, t.department_id, t.position, t.joining_date, t.status,
                    u.id AS user_id, u.full_name, u.email, u.profile_image,
                    d.name AS department_name
                  FROM teachers t
                  JOIN users u ON t.user_id = u.id
                  LEFT JOIN departments d ON t.department_id = d.id
                  $joinTeacherCourses
                  $whereSql
                  GROUP BY t.id
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
                // Fetch assigned courses for each teacher
                $teacher_id = $row['id'];
                $assignedCourses = [];
                $stmtCourses = $conn->prepare("SELECT course_id FROM teacher_courses WHERE teacher_id = ?");
                if ($stmtCourses) {
                    $stmtCourses->bind_param("i", $teacher_id);
                    $stmtCourses->execute();
                    $resultCourses = $stmtCourses->get_result();
                    while ($courseRow = $resultCourses->fetch_assoc()) {
                        $assignedCourses[] = $courseRow['course_id'];
                    }
                    $stmtCourses->close();
                }
                $row['assigned_courses'] = $assignedCourses;
                $data[] = $row;
            }
            $stmt->close();

            json_response(true, "Teachers fetched successfully", [
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($filteredRecords),
                "data" => $data
            ]);
        } else {
            json_response(false, "Failed to prepare teacher fetch query: " . $conn->error, null, 500);
        }
        break;

    case 'get':
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) {
            json_response(false, "Invalid teacher ID provided.", null, 400);
        }

        $query = "SELECT 
                    t.id, t.employee_id, t.phone, t.address, t.department_id, t.position, t.joining_date, t.status,
                    u.id AS user_id, u.full_name, u.email, u.profile_image
                  FROM teachers t
                  JOIN users u ON t.user_id = u.id
                  WHERE t.id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $teacher = $result->fetch_assoc();
            $stmt->close();

            if ($teacher) {
                // Fetch assigned courses
                $assignedCourses = [];
                $stmtCourses = $conn->prepare("SELECT course_id FROM teacher_courses WHERE teacher_id = ?");
                if ($stmtCourses) {
                    $stmtCourses->bind_param("i", $id);
                    $stmtCourses->execute();
                    $resultCourses = $stmtCourses->get_result();
                    while ($courseRow = $resultCourses->fetch_assoc()) {
                        $assignedCourses[] = $courseRow['course_id'];
                    }
                    $stmtCourses->close();
                }
                $teacher['assigned_courses'] = $assignedCourses;
                json_response(true, "Teacher fetched successfully", $teacher);
            } else {
                json_response(false, "Teacher not found.", null, 404);
            }
        } else {
            json_response(false, "Failed to prepare get teacher query: " . $conn->error, null, 500);
        }
        break;

    case 'create':
        $conn->begin_transaction();
        try {
            $fullName = sanitize_input($_POST['full_name'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            $password = $_POST['password'] ?? ''; // Will be hashed
            $employeeId = sanitize_input($_POST['employee_id'] ?? '');
            $phone = sanitize_input($_POST['phone'] ?? '');
            $address = sanitize_input($_POST['address'] ?? '');
            $departmentId = filter_var($_POST['department_id'] ?? '', FILTER_VALIDATE_INT);
            $position = sanitize_input($_POST['position'] ?? '');
            $joiningDate = sanitize_input($_POST['joining_date'] ?? '');
            $status = sanitize_input($_POST['status'] ?? 'active');
            $assignedCourses = $_POST['assigned_courses'] ?? []; // Array of course IDs

            // Validate inputs
            if (empty($fullName) || empty($email) || empty($password) || strlen($password) < 6) {
                throw new Exception("Full name, email, and a password of at least 6 characters are required.", 400);
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
                $uploadResult = upload_file($_FILES['profile_image'], $config['UPLOAD_DIR'] . '/teachers');
                if (!$uploadResult['success']) {
                    throw new Exception("Image upload failed: " . $uploadResult['message'], 400);
                }
                $profileImageRelPath = 'teachers/' . $uploadResult['filename']; // Store path relative to 'uploads'
            }

            // 1. Create user entry
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmtUser = $conn->prepare("INSERT INTO users (full_name, email, password, role, profile_image) VALUES (?, ?, ?, 'teacher', ?)");
            if (!$stmtUser) { throw new Exception("Failed to prepare user insert: " . $conn->error, 500); }
            $stmtUser->bind_param("ssss", $fullName, $email, $hashedPassword, $profileImageRelPath);
            if (!$stmtUser->execute()) { throw new Exception("Failed to create user: " . $stmtUser->error, 500); }
            $userId = $db->getLastInsertId();
            $stmtUser->close();

            // 2. Create teacher entry
            $stmtTeacher = $conn->prepare("INSERT INTO teachers (user_id, employee_id, phone, address, department_id, position, joining_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmtTeacher) { throw new Exception("Failed to prepare teacher insert: " . $conn->error, 500); }
            $stmtTeacher->bind_param("isssisss", $userId, $employeeId, $phone, $address, $departmentId, $position, $joiningDate, $status);
            if (!$stmtTeacher->execute()) { throw new Exception("Failed to create teacher: " . $stmtTeacher->error, 500); }
            $teacherId = $db->getLastInsertId();
            $stmtTeacher->close();

            // 3. Assign courses
            if (!empty($assignedCourses)) {
                $stmtAssignCourse = $conn->prepare("INSERT INTO teacher_courses (teacher_id, course_id) VALUES (?, ?)");
                if (!$stmtAssignCourse) { throw new Exception("Failed to prepare course assignment: " . $conn->error, 500); }
                foreach ($assignedCourses as $courseId) {
                    $stmtAssignCourse->bind_param("ii", $teacherId, $courseId);
                    $stmtAssignCourse->execute();
                }
                $stmtAssignCourse->close();
            }

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Teacher Created', "Admin created teacher: {$fullName} (ID: {$teacherId})");
            json_response(true, "Teacher created successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Teacher Creation Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'update':
        $conn->begin_transaction();
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT); // Teacher ID
            $fullName = sanitize_input($_POST['full_name'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');
            $password = $_POST['password'] ?? ''; // Optional, only update if provided
            $employeeId = sanitize_input($_POST['employee_id'] ?? '');
            $phone = sanitize_input($_POST['phone'] ?? '');
            $address = sanitize_input($_POST['address'] ?? '');
            $departmentId = filter_var($_POST['department_id'] ?? '', FILTER_VALIDATE_INT);
            $position = sanitize_input($_POST['position'] ?? '');
            $joiningDate = sanitize_input($_POST['joining_date'] ?? '');
            $status = sanitize_input($_POST['status'] ?? 'active');
            $assignedCourses = $_POST['assigned_courses'] ?? []; // Array of course IDs

            if (!$id || empty($fullName) || empty($email)) {
                throw new Exception("Teacher ID, full name, and email are required.", 400);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.", 400);
            }
            if (!empty($password) && strlen($password) < 6) {
                throw new Exception("Password must be at least 6 characters.", 400);
            }

            // Get current user_id and profile_image path
            $stmtGetTeacher = $conn->prepare("SELECT user_id FROM teachers WHERE id = ?");
            $stmtGetTeacher->bind_param("i", $id);
            $stmtGetTeacher->execute();
            $resultGetTeacher = $stmtGetTeacher->get_result();
            $teacherData = $resultGetTeacher->fetch_assoc();
            $stmtGetTeacher->close();
            if (!$teacherData) { throw new Exception("Teacher not found.", 404); }
            $userId = $teacherData['user_id'];

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
                $uploadResult = upload_file($_FILES['profile_image'], $config['UPLOAD_DIR'] . '/teachers');
                if (!$uploadResult['success']) {
                    throw new Exception("Image upload failed: " . $uploadResult['message'], 400);
                }
                $profileImageRelPath = 'teachers/' . $uploadResult['filename'];
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

            // 2. Update teacher entry
            $stmtTeacher = $conn->prepare("UPDATE teachers SET employee_id = ?, phone = ?, address = ?, department_id = ?, position = ?, joining_date = ?, status = ? WHERE id = ?");
            if (!$stmtTeacher) { throw new Exception("Failed to prepare teacher update: " . $conn->error, 500); }
            $stmtTeacher->bind_param("sssisssi", $employeeId, $phone, $address, $departmentId, $position, $joiningDate, $status, $id);
            if (!$stmtTeacher->execute()) { throw new Exception("Failed to update teacher: " . $stmtTeacher->error, 500); }
            $stmtTeacher->close();

            // 3. Update assigned courses
            $conn->query("DELETE FROM teacher_courses WHERE teacher_id = " . intval($id)); // Clear existing assignments
            if (!empty($assignedCourses)) {
                $stmtAssignCourse = $conn->prepare("INSERT INTO teacher_courses (teacher_id, course_id) VALUES (?, ?)");
                if (!$stmtAssignCourse) { throw new Exception("Failed to prepare course assignment: " . $conn->error, 500); }
                foreach ($assignedCourses as $courseId) {
                    $stmtAssignCourse->bind_param("ii", $id, $courseId);
                    $stmtAssignCourse->execute();
                }
                $stmtAssignCourse->close();
            }

            $conn->commit();
            log_activity($_SESSION['user_id'], 'Teacher Updated', "Admin updated teacher: {$fullName} (ID: {$id})");
            json_response(true, "Teacher updated successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Teacher Update Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    case 'delete':
        $conn->begin_transaction();
        try {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT); // Teacher ID
            if (!$id) {
                throw new Exception("Invalid teacher ID provided.", 400);
            }

            // Get user_id and profile_image path before deleting
            $stmtGetTeacher = $conn->prepare("SELECT user_id FROM teachers WHERE id = ?");
            $stmtGetTeacher->bind_param("i", $id);
            $stmtGetTeacher->execute();
            $resultGetTeacher = $stmtGetTeacher->get_result();
            $teacherData = $resultGetTeacher->fetch_assoc();
            $stmtGetTeacher->close();
            if (!$teacherData) { throw new Exception("Teacher not found.", 404); }
            $userId = $teacherData['user_id'];

            $stmtGetUser = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmtGetUser->bind_param("i", $userId);
            $stmtGetUser->execute();
            $resultGetUser = $stmtGetUser->get_result();
            $userData = $resultGetUser->fetch_assoc();
            $stmtGetUser->close();
            $profileImageToDelete = $userData['profile_image'];

            // Deleting from 'users' table will cascade delete from 'teachers' and 'teacher_courses' due to FOREIGN KEY ON DELETE CASCADE
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
            log_activity($_SESSION['user_id'], 'Teacher Deleted', "Admin deleted teacher (User ID: {$userId}, Teacher ID: {$id})");
            json_response(true, "Teacher deleted successfully!");

        } catch (Exception $e) {
            $conn->rollback();
            log_activity($_SESSION['user_id'], 'Teacher Deletion Failed', "Error: {$e->getMessage()}");
            json_response(false, $e->getMessage(), null, $e->getCode() ?: 500);
        }
        break;

    default:
        json_response(false, "Invalid action provided.", null, 400);
        break;
}
```