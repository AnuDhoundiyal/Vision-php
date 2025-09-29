<?php
session_start();
include 'setup_add.php';

$type = $_POST['type'] ?? '';

if($type == 'department'){
    $name = $_POST['department_name'];
    $batch = $_POST['department_batch'];
    $division = $_POST['department_division'];

    $stmt = $conn->prepare("INSERT INTO departments (name, batch, division) VALUES (?,?,?)");
    $stmt->bind_param("sss", $name, $batch, $division);
    $stmt->execute();
    $id = $conn->insert_id;

    echo json_encode([
        'status'=>'success',
        'msg'=>"Department '$name' added!",
        'data'=>['id'=>$id,'name'=>$name,'batch'=>$batch,'division'=>$division]
    ]);
    exit;
}

if($type == 'course'){
    $name = $_POST['course_name'];
    $batch = $_POST['course_batch'];
    $division = $_POST['course_division'];
    $department_id = $_POST['department_id'];

    if(!$department_id){
        echo json_encode(['status'=>'error','msg'=>"Please select a department."]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO courses (name, batch, division, department_id) VALUES (?,?,?,?)");
    $stmt->bind_param("sssi", $name, $batch, $division, $department_id);
    $stmt->execute();
    $id = $conn->insert_id;

    // Get department name
    $deptName = $conn->query("SELECT name FROM departments WHERE id=$department_id")->fetch_assoc()['name'];

    echo json_encode([
        'status'=>'success',
        'msg'=>"Course '$name' added!",
        'data'=>['id'=>$id,'name'=>$name,'batch'=>$batch,'division'=>$division,'dept_name'=>$deptName]
    ]);
    exit;
}
?>
