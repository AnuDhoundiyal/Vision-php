<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../public/config/db.php';

$id = intval($_POST['id'] ?? 0);
if(!$id){
    echo json_encode(['status'=>'error','message'=>'Invalid ID']);
    exit;
}

$sql = "DELETE FROM students WHERE id=$id";
if($conn->query($sql)){
    echo json_encode(['status'=>'success','message'=>'Student deleted successfully']);
} else {
    echo json_encode(['status'=>'error','message'=>'DB Error: '.$conn->error]);
}
