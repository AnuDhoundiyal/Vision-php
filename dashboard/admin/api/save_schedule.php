<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../../../public/config/db.php';

// Get data from GET
$course_id  = $_GET['course_id'] ?? '';
$teacher_id = $_GET['teacher_id'] ?? null; // nullable
$day        = $_GET['day'] ?? '';
$time       = $_GET['time'] ?? '';
$room       = $_GET['room'] ?? '';

if(!$course_id || !$day || !$time || !$room){
    echo json_encode(["status"=>"error","message"=>"No data submitted or missing required fields"]);
    exit;
}

// Split time
$parts = explode("-", $time);
if(count($parts)!=2){
    echo json_encode(["status"=>"error","message"=>"Invalid time format"]);
    exit;
}
$start_time = date("H:i:s", strtotime(trim($parts[0])));
$end_time   = date("H:i:s", strtotime(trim($parts[1])));

// Fetch batch/division
$batch=$division='';
$res = $conn->query("SELECT batch, division FROM courses WHERE id=".intval($course_id));
if($res && $res->num_rows>0){
    $row = $res->fetch_assoc();
    $batch=$row['batch'];
    $division=$row['division'];
}

// Insert
$stmt = $conn->prepare("INSERT INTO schedule (course_id,batch,division,teacher_id,day_of_week,start_time,end_time,room) VALUES (?,?,?,?,?,?,?,?)");
$stmt->bind_param("ississss",$course_id,$batch,$division,$teacher_id,$day,$start_time,$end_time,$room);

if($stmt->execute()){
    echo json_encode(["status"=>"success","message"=>"✅ Schedule saved successfully!"]);
} else {
    echo json_encode(["status"=>"error","message"=>"❌ Failed: ".$conn->error]);
}

$stmt->close();
$conn->close();
