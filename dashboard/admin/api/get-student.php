<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../public/config/db.php';

$result = $conn->query("
    SELECT s.id, s.name, s.student_id, s.batch, s.division, 
           c.name AS course, s.status 
    FROM students s 
    LEFT JOIN courses c ON s.course_id = c.id
    ORDER BY s.id DESC
");

$students = [];
while($row = $result->fetch_assoc()){
    $students[] = $row;
}

echo json_encode(['data'=>$students]);
