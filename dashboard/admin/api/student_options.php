<?php
include '../setup_student.php';
require_once '/../../public/config/db.php';

$response = ['status'=>'error', 'message'=>'Something went wrong','courses'=>[],'batches'=>[],'divisions'=>[]];

$courses = [];
$res = $conn->query("SELECT id, name FROM courses ORDER BY name ASC");
if($res) {
    while($row = $res->fetch_assoc()) $courses[] = $row;
}

$batches = [];
$res = $conn->query("SELECT DISTINCT batch AS id, batch AS name FROM courses WHERE batch IS NOT NULL ORDER BY batch ASC");
if($res) {
    while($row = $res->fetch_assoc()) $batches[] = $row;
}

$divisions = [];
$res = $conn->query("SELECT DISTINCT division AS id, division AS name FROM courses WHERE division IS NOT NULL ORDER BY division ASC");
if($res) {
    while($row = $res->fetch_assoc()) $divisions[] = $row;
}

$response['status'] = 'success';
$response['courses'] = $courses;
$response['batches'] = $batches;
$response['divisions'] = $divisions;

echo json_encode($response);
?>
