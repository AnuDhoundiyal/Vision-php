<?php
header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'].'/php_project/public/config/db.php';

$response = ['status'=>'error','message'=>'Something went wrong'];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception("Invalid request method");

    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $course_id  = $_POST['course_id'] ?? null;
    $batch      = trim($_POST['batch'] ?? '');
    $division   = trim($_POST['division'] ?? '');

    if (!$name || !$email) throw new Exception("Name and Email are required");
    if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== 0) throw new Exception("Profile image is required");

    // Upload folder
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/php_project/uploads/students/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time().'_'.basename($_FILES['profile_image']['name']);
    $targetFile = $uploadDir . $filename;
    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) throw new Exception("Failed to upload image");

    $profile_image = "uploads/students/".$filename;

    // Auto-generate student_id
    if (empty($student_id) && $course_id) {
        $row = $conn->query("SELECT name FROM courses WHERE id=$course_id")->fetch_assoc();
        if(!$row) throw new Exception("Invalid course ID");
        $short = strtoupper(substr($row['name'], 0, 3));
        $count = $conn->query("SELECT COUNT(*) AS total FROM students WHERE course_id=$course_id")->fetch_assoc()['total'] + 1;
        $student_id = $short . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    $stmt = $conn->prepare("INSERT INTO students (name,email,student_id,phone,address,profile_image,course_id,batch,division) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssiss", $name,$email,$student_id,$phone,$address,$profile_image,$course_id,$batch,$division);

    if ($stmt->execute()) {
        $response = ['status'=>'success','message'=>'Student added successfully'];
    } else {
        $response = ['status'=>'error','message'=>$stmt->error];
    }

} catch(Exception $e) {
    $response = ['status'=>'error','message'=>$e->getMessage()];
}

echo json_encode($response);
