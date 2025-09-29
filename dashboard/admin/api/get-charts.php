<?php
require_once __DIR__ . '/../../../public/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$labels = [];
$data   = [];

// Enrolled students per course
$sql = "
    SELECT c.id AS course_id, c.name AS course_name, COUNT(s.id) AS total
    FROM courses c
    LEFT JOIN students s ON s.course_id = c.id
    GROUP BY c.id, c.name
    ORDER BY c.name ASC
";

if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        $labels[] = $row['course_name'];
        $data[]   = (int)$row['total'];
    }
}

echo json_encode([
    'enrollment' => [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Enrolled Students',
            'data'  => $data,
            'borderColor' => 'rgba(79,70,229,0.7)',
            'backgroundColor' => 'rgba(79,70,229,0.3)'
        ]]
    ],
    'attendance' => [
        'labels' => $labels,
        'datasets' => [[
            'label' => 'Attendance %',
            'data'  => array_fill(0, count($labels), 75), // placeholder
            'backgroundColor' => 'rgba(16,185,129,0.7)'
        ]]
    ]
]);
exit;
?>
