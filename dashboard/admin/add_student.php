<?php
// dashboard/admin/add_student.php
$pageTitle = "Add Student";
$activePage = "students";

require_once __DIR__ . '/../../public/config/db.php';

// Fetch courses, batches, divisions
$courses = []; $batches = []; $divisions = [];
$res = $conn->query("SELECT * FROM courses ORDER BY name ASC"); while($row=$res->fetch_assoc()) $courses[]=$row;
$res = $conn->query("SELECT DISTINCT batch AS name FROM courses WHERE batch IS NOT NULL ORDER BY batch ASC"); while($row=$res->fetch_assoc()) $batches[]=$row;
$res = $conn->query("SELECT DISTINCT division AS name FROM courses WHERE division IS NOT NULL ORDER BY division ASC"); while($row=$res->fetch_assoc()) $divisions[]=$row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?></title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<div class="flex h-screen overflow-hidden">
    <?php include_once __DIR__ . '/../components/sidebar.php'; ?>

    <div class="flex-1 overflow-auto lg:ml-64">
        <header class="bg-white dark:bg-gray-800 shadow-sm z-10 px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?= $pageTitle ?></h1>
        </header>

        <main class="p-6">
            <!-- Notification container -->
            <div id="notification-container" class="fixed bottom-5 right-5 space-y-2 z-50"></div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <form id="studentForm" action="api/student_api.php" method="POST" enctype="multipart/form-data" class="col-span-2">
                    <!-- Left -->
                    <div class="space-y-6">
                        <h2 class="text-lg font-semibold border-b pb-2">Profile</h2>
                        <div class="flex flex-col items-center">
                            <div class="w-32 h-32 rounded-full bg-gray-200 overflow-hidden">
                                <img id="profilePreview" src="https://ui-avatars.com/api/?name=Student&background=0D8ABC&color=fff" class="w-full h-full object-cover">
                            </div>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*" class="mt-4 p-2 border rounded-md" required>
                        </div>

                        <input type="text" name="name" placeholder="Full Name *" class="w-full p-2 border rounded-md" required>
                        <input type="email" name="email" placeholder="Email *" class="w-full p-2 border rounded-md" required>
                        <input type="text" name="student_id" placeholder="Student ID" class="w-full p-2 border rounded-md">
                        <input type="tel" name="phone" placeholder="Phone" class="w-full p-2 border rounded-md">
                        <textarea name="address" placeholder="Address" class="w-full p-2 border rounded-md"></textarea>
                    </div>

                    <!-- Right -->
                    <div class="space-y-6">
                        <h2 class="text-lg font-semibold border-b pb-2">Academic</h2>
                        <select name="course_id" required class="w-full p-2 border rounded-md">
                            <option value="">Select Course</option>
                            <?php foreach($courses as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="batch" class="w-full p-2 border rounded-md">
                            <option value="">Select Batch</option>
                            <?php foreach($batches as $b): ?>
                                <option value="<?= htmlspecialchars($b['name']) ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="division" class="w-full p-2 border rounded-md">
                            <option value="">Select Division</option>
                            <?php foreach($divisions as $d): ?>
                                <option value="<?= htmlspecialchars($d['name']) ?>"><?= htmlspecialchars($d['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-2 flex justify-end space-x-4 mt-6">
                        <button type="reset" class="px-4 py-2 bg-gray-200 rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Add Student</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
// Preview
document.getElementById('profile_image').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(ev){ document.getElementById('profilePreview').src = ev.target.result; }
        reader.readAsDataURL(file);
    }
});

// Submit
document.getElementById('studentForm').addEventListener('submit', function(e){
    e.preventDefault();
    const form = this;
    const fd = new FormData(form);

    fetch(form.action, { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
        const notif = document.createElement('div');
        notif.className = `px-6 py-3 rounded shadow-lg text-white ${data.status==='success'?'bg-green-600':'bg-red-600'}`;
        notif.textContent = data.message;
        document.getElementById('notification-container').appendChild(notif);
        setTimeout(()=> notif.remove(), 4000);

        if(data.status==='success'){
            form.reset();
            document.getElementById('profilePreview').src = "https://ui-avatars.com/api/?name=Student&background=0D8ABC&color=fff";
        }
    })
    .catch(err => {
        const notif = document.createElement('div');
        notif.className = 'px-6 py-3 rounded shadow-lg bg-red-600 text-white';
        notif.textContent = 'Network or server error';
        document.getElementById('notification-container').appendChild(notif);
        setTimeout(()=> notif.remove(), 4000);
    });
});
</script>

</body>
</html>
