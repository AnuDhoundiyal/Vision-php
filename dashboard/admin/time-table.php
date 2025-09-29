<?php
require_once __DIR__ . '/../../public/config/db.php';
include_once __DIR__ . '/../components/sidebar.php'; // Sidebar included

// Fetch Courses
$courses = [];
$res = $conn->query("SELECT * FROM courses ORDER BY id ASC");
while($row=$res->fetch_assoc()) $courses[$row['id']]=$row;

// Fetch Teachers
$teachers = [];
$res = $conn->query("SELECT id,name FROM teachers ORDER BY name ASC");
while($row=$res->fetch_assoc()) $teachers[$row['id']]=$row;

// Fetch Schedule
$schedule = [];
$res = $conn->query("SELECT s.*, c.name as course_name, t.name as teacher_name 
                     FROM schedule s
                     LEFT JOIN courses c ON s.course_id=c.id
                     LEFT JOIN teachers t ON s.teacher_id=t.id");
while($row=$res->fetch_assoc()) $schedule[]=$row;

// Soft pastel colors
// Soft pastel colors with gentle tones
$colors = ["#FFF7E6","#E6F7FF","#E6FFFA","#F3E6FF","#FFE6F0","#FFF0E6","#E6FFFA","#FFE6E6","#E6E6FF","#E6FFF5"];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Timetable</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex">

<!-- Sidebar -->
<?php // Already included above ?>

<!-- Main Content -->
<div class="flex-1 overflow-auto lg:ml-64 p-6">

<div id="msgCard" class="fixed top-5 right-5 z-50"></div>

<h1 class="text-2xl font-bold mb-6 text-gray-800">Weekly Timetable</h1>

<!-- Timetable Table -->
<div class="overflow-x-auto shadow rounded-lg mb-8">
<table class="min-w-full border border-gray-200 text-sm text-center" id="timetableTable">
<thead class="bg-gray-100">
<tr>
<th class="border px-4 py-3 font-semibold">Time</th>
<?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day): ?>
<th class="border px-4 py-3 font-semibold"><?= $day ?></th>
<?php endforeach; ?>
</tr>
</thead>
<tbody class="bg-white" id="timetableBody">
<?php
$times=["07:30-08:20","08:30-09:20","09:30-10:20","10:30-11:20","11:30-12:20","12:30-01:00"];
foreach($times as $slot):
    echo "<tr data-slot='$slot'>";
    echo "<td class='border px-4 py-3 font-medium bg-gray-50'>$slot</td>";
    foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day){
        $cell="<td class='border px-4 py-6'></td>";
        foreach($schedule as $sch){
            // Format DB times to match $times
            $slotTime = date("H:i", strtotime($sch['start_time'])) . "-" . date("H:i", strtotime($sch['end_time']));
            if($sch['day_of_week']==$day && $slotTime==$slot){
                $color = $colors[($sch['course_id']+$sch['teacher_id']) % count($colors)];
                $cell="<td class='border px-4 py-3 rounded-lg font-semibold' style='background:$color'>
                    <div>{$sch['course_name']}</div>
                    <div class='text-xs text-gray-600'>{$sch['batch']}/{$sch['division']}</div>
                    <div class='text-xs text-gray-700'>{$sch['teacher_name']}</div>
                </td>";
            }
        }
        echo $cell;
    }
    echo "</tr>";
endforeach;
?>
</tbody>
</table>
</div>

<!-- Add / Update Schedule Form -->
<div class="bg-white shadow rounded-lg p-6">
<h2 class="text-xl font-semibold mb-4 text-gray-700">Add / Update Schedule</h2>
<form id="scheduleForm" class="grid grid-cols-2 gap-4">
<select name="course_id" class="p-3 border rounded col-span-1" required>
<option value="">Select Course</option>
<?php foreach($courses as $c): ?>
<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']." (".$c['batch']."/".$c['division'].")") ?></option>
<?php endforeach; ?>
</select>

<select name="teacher_id" class="p-3 border rounded col-span-1" required>
<option value="">Select Teacher</option>
<?php foreach($teachers as $t): ?>
<option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
<?php endforeach; ?>
</select>

<select name="day" class="p-3 border rounded col-span-1" required>
<option value="">Select Day</option>
<?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day): ?>
<option value="<?= $day ?>"><?= $day ?></option>
<?php endforeach; ?>
</select>

<select name="time" class="p-3 border rounded col-span-1" required>
<?php foreach($times as $slot): ?>
<option value="<?= $slot ?>"><?= $slot ?></option>
<?php endforeach; ?>
</select>

<input type="text" name="room" placeholder="Room No." class="p-3 border rounded col-span-2" required>
<button type="submit" class="col-span-2 mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
</form>
</div>

<script>
const colors = <?= json_encode($colors) ?>;

function addRowToTable(schedule){
    const tbody = document.getElementById("timetableBody");
    const rows = tbody.querySelectorAll("tr");
    rows.forEach(tr=>{
        if(tr.dataset.slot === schedule.start_time+"-"+schedule.end_time){
            const dayIndex = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"].indexOf(schedule.day_of_week);
            if(dayIndex===-1) return;
            const color = colors[(schedule.course_id+schedule.teacher_id) % colors.length];
            const cell = document.createElement("td");
            cell.className="border px-4 py-3 rounded-lg font-semibold";
            cell.style.background=color;
            cell.innerHTML = `
                <div>${schedule.course_name}</div>
                <div class='text-xs text-gray-600'>${schedule.batch}/${schedule.division}</div>
                <div class='text-xs text-gray-700'>${schedule.teacher_name}</div>`;
            tr.children[dayIndex+1].replaceWith(cell);
        }
    });
}

document.getElementById("scheduleForm").addEventListener("submit", function(e){
    e.preventDefault();
    const form = this;
    const params = new URLSearchParams(new FormData(form)).toString();
    fetch("/php_project/dashboard/admin/api/save_schedule.php?" + params)
        .then(r=>r.json())
        .then(res=>{
            const msgCard = document.getElementById('msgCard');
            msgCard.innerHTML='';
            const div=document.createElement('div');
            div.className = res.status==="success"
                ? 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-2 shadow-lg'
                : 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2 shadow-lg';
            div.textContent = res.message;
            msgCard.appendChild(div);
            setTimeout(()=>div.remove(),4000);

            if(res.status==="success" && res.data){
                addRowToTable(res.data);
                form.reset();
            }
        }).catch(err=>console.error("Fetch error:",err));
});
</script>

</div> <!-- Main content end -->
</body>
</html>
