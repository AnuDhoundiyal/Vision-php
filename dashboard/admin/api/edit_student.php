<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../public/config/db.php';

$id = intval($_POST['id'] ?? 0);
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$profile_image = $_FILES['profile_image'] ?? null;

if(!$id || !$name || !$email){
    echo json_encode(['status'=>'error','message'=>'All fields are required']);
    exit;
}

// Handle profile image upload
if($profile_image && $profile_image['tmp_name']){
    $ext = pathinfo($profile_image['name'], PATHINFO_EXTENSION);
    $filename = time().'_'.basename($profile_image['name']);
    $target = __DIR__.'/../uploads/students/'.$filename;
    if(move_uploaded_file($profile_image['tmp_name'],$target)){
        $img_sql = ", profile_image='". $conn->real_escape_string($filename) ."'";
    } else {
        $img_sql = '';
    }
} else {
    $img_sql = '';
}

$sql = "UPDATE students SET name='".$conn->real_escape_string($name)."', email='".$conn->real_escape_string($email)."' $img_sql WHERE id=$id";
if($conn->query($sql)){
    echo json_encode(['status'=>'success','message'=>'Student updated successfully']);
} else {
    echo json_encode(['status'=>'error','message'=>'DB Error: '.$conn->error]);
}
