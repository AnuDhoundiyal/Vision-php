<?php
$pageTitle = "Add / Update Teacher";
$activePage = "teachers";

require_once __DIR__ . '/../../public/config/db.php'; // correct DB path
// DB connection

// Initialize empty variables (data will come from DB)
$teacher = $teacher ?? [
    'name' => '',
    'email' => '',
    'phone' => '',
    'department' => '',
    'position' => '',
    'joining_date' => '',
    'status' => '',
    'profile_image' => '',
];
$id = $id ?? 0;

// Messages
$message = $message ?? '';
$messageType = $messageType ?? 'success';

// Fetch departments and courses from DB
$departments = [];
$result = $conn->query("SELECT * FROM departments ORDER BY name ASC");
while($row = $result->fetch_assoc()) $departments[] = $row;

$courses = [];
$result = $conn->query("SELECT * FROM courses ORDER BY name ASC");
while($row = $result->fetch_assoc()) $courses[] = $row;

$teacherCourses = $teacherCourses ?? []; // assigned courses

// Handle form submission
if($_SERVER['REQUEST_METHOD']=='POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];
    $selectedCourses = $_POST['courses'] ?? [];

    // Handle profile image upload
    $profile_image = $teacher['profile_image'];
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error']==0){
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $newName = 'uploads/teachers/'.time().'_'.rand(1000,9999).'.'.$ext;
        if(!is_dir('uploads/teachers')) mkdir('uploads/teachers',0777,true);
        move_uploaded_file($_FILES['profile_image']['tmp_name'],$newName);
        $profile_image = $newName;
    }

    if($id > 0){
        // Update teacher
        $stmt = $conn->prepare("UPDATE teachers SET name=?, email=?, phone=?, department=?, position=?, joining_date=?, status=?, profile_image=? WHERE id=?");
        $stmt->bind_param("ssssssssi",$name,$email,$phone,$department,$position,$joining_date,$status,$profile_image,$id);
        $stmt->execute();
        $teacher_id = $id;
        $message = "Teacher updated successfully!";
    } else {
        // Insert new teacher
        $stmt = $conn->prepare("INSERT INTO teachers (name,email,phone,department,position,joining_date,status,profile_image) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssss",$name,$email,$phone,$department,$position,$joining_date,$status,$profile_image);
        $stmt->execute();
        $teacher_id = $stmt->insert_id;
        $message = "Teacher added successfully!";
    }

    // Save assigned courses
    $conn->query("DELETE FROM teacher_courses WHERE teacher_id=".$teacher_id);
    foreach($selectedCourses as $course_id){
        $conn->query("INSERT INTO teacher_courses (teacher_id,course_id) VALUES ($teacher_id,$course_id)");
    }

    $messageType = 'success';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle; ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include_once '../components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto lg:ml-64">
        <header class="bg-white dark:bg-gray-800 shadow-sm z-10">
            <div class="px-6 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo $pageTitle; ?></h1>
            </div>
        </header>

        <main class="p-6">

            <!-- Notification -->
            <div id="notif" class="fixed top-5 right-5 p-4 rounded shadow flex items-center justify-between hidden w-96 bg-green-600 text-white">
                <span id="notifMsg"><?php echo $message; ?></span>
                <button onclick="$('#notif').hide()" class="ml-4 font-bold text-xl">✖</button>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <form action="" method="POST" enctype="multipart/form-data" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Profile Image -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-white border-b pb-2 dark:border-gray-700">Profile Image</h2>
                                <div class="mt-4 flex flex-col items-center">
                                    <div class="w-32 h-32 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                        <img src="<?php echo !empty($teacher['profile_image']) ? htmlspecialchars($teacher['profile_image']) : 'https://ui-avatars.com/api/?name=Teacher&background=0D8ABC&color=fff'; ?>" alt="Profile" class="w-full h-full object-cover">
                                    </div>
                                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="mt-4 w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-200">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">JPG, PNG or GIF. Max size 2MB.</p>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-white border-b pb-2 dark:border-gray-700">Basic Information</h2>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name *</label>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($teacher['phone']); ?>" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                        <select name="department" id="teacherDept" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <option value="">Select Department</option>
                                            <?php foreach($departments as $dept): ?>
                                            <option value="<?php echo htmlspecialchars($dept['id']); ?>" <?php echo ($teacher['department']==$dept['id'])?'selected':''; ?>><?php echo htmlspecialchars($dept['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                                        <input type="text" name="position" value="<?php echo htmlspecialchars($teacher['position']); ?>" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Joining Date</label>
                                        <input type="date" name="joining_date" value="<?php echo htmlspecialchars($teacher['joining_date']); ?>" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" class="w-full p-2.5 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <option value="active" <?php echo ($teacher['status']=='active')?'selected':''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($teacher['status']=='inactive')?'selected':''; ?>>Inactive</option>
                                            <option value="on_leave" <?php echo ($teacher['status']=='on_leave')?'selected':''; ?>>On Leave</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Courses -->
                        <div class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white border-b pb-2 dark:border-gray-700">Assigned Courses</h2>
                            <div class="mt-4 space-y-4 max-h-96 overflow-y-auto">
                                <?php foreach($courses as $course): ?>
                                <div class="flex items-center">
                                    <input type="checkbox" name="courses[]" value="<?php echo $course['id']; ?>" id="course_<?php echo $course['id']; ?>" class="h-4 w-4 text-blue-600 border-gray-300 rounded" <?php echo in_array($course['id'],$teacherCourses)?'checked':''; ?>>
                                    <label for="course_<?php echo $course['id']; ?>" class="ml-2 block text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($course['name']); ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-end">
                        <button type="button" onclick="window.location.href='teachers.php'" class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 mr-4">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><?php echo $id>0 ? 'Update Teacher' : 'Add Teacher'; ?></button>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Preview uploaded profile image
    document.getElementById('profile_image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                document.querySelector('.w-32.h-32.rounded-full img').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Show notification if message exists
    $(document).ready(function(){
        <?php if(!empty($message)): ?>
            $('#notif').fadeIn();
            setTimeout(()=>$('#notif').fadeOut(),3000);
        <?php endif; ?>
    });
</script>

</body>
</html>
