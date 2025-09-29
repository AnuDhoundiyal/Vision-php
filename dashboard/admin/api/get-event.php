<?php
require_once __DIR__.'/../../../public/config/db.php';
header('Content-Type: application/json');
$out = [];
$res = $conn->query("SELECT id, title, start_date AS start, end_date AS end FROM events");
while($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
