<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../public/config/db.php';

$res = $conn->query("SELECT s.*, c.name as course_name, t.name as teacher_name 
                     FROM schedule s
                     LEFT JOIN courses c ON s.course_id=c.id
                     LEFT JOIN teachers t ON s.teacher_id=t.id");
$data=[];
while($row=$res->fetch_assoc()) $data[]=$row;

echo json_encode($data);
