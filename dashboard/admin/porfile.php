<?php
require_once '../../public/config/config.php';
require_once '../../public/config/db.php';

$pageTitle = "Admin Profile";

// ---------- CONFIG ----------
$adminId = 1; // static admin record; change to your actual admin row id

// ---------- FETCH DATA ----------
$admin = null;
$stmt  = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$admin  = $result->fetch_assoc();
$stmt->close();

// fallback if empty
if (!$admin) {
    $admin = [
        'id' => $adminId,
        'name' => '',
        'email' => '',
        'username' => '',
        'phone' => '',
        'bio' => '',
        'profile_image' => ''
    ];
}
// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name  = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $bio = $conn->real_escape_string($_POST['bio'] ?? '');

    $photo_sql = '';
    if (!empty($_FILES['profile_image']['name'])) {
        $file = $_FILES['profile_image'];
        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $target_file = $config['UPLOAD_DIR'] . '/' . uniqid() . '.' . $ext;

        if (in_array($file['type'], $config['ALLOWED_MIMES']) && $file['size'] <= $config['MAX_FILE_SIZE']) {
            move_uploaded_file($file['tmp_name'], $target_file);
            $photo_db = $config['UPLOAD_DIR_REL'] . '/' . basename($target_file);
            $photo_sql = ", profile_image='$photo_db'";
        } else {
            $_SESSION['error'] = "Invalid file type or size. Max 2MB, jpg/png/webp only.";
        }
    }

    $sql = "UPDATE admins SET name='$name', email='$email', phone='$phone', username='$username', bio='$bio' $photo_sql WHERE id=$adminId";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating profile: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include_once '../components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto lg:ml-64 p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white"><?= $pageTitle ?></h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-700 flex justify-between">
                <span><?= $_SESSION['success'] ?></span>
                <button onclick="this.parentElement.remove();" class="font-bold">✖</button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-700 flex justify-between">
                <span><?= $_SESSION['error'] ?></span>
                <button onclick="this.parentElement.remove();" class="font-bold">✖</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col md:flex-row gap-6">
            <!-- Left: Profile Image -->
            <div class="flex flex-col items-center md:w-1/3">
                <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-gray-300 dark:border-gray-600 bg-indigo-200 flex items-center justify-center text-white text-4xl font-bold">
                    <?php if(!empty($admin['profile_image'])): ?>
                        <img src="<?= htmlspecialchars($admin['profile_image']); ?>" class="w-full h-full object-cover">
                    <?php elseif(!empty($admin['name'])): ?>
                        <?= strtoupper($admin['name'][0]) ?>
                    <?php else: ?>
                        A
                    <?php endif; ?>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" class="mt-4 w-full">
                    <input type="file" name="profile_image" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-200">
                    <button type="submit" name="update_profile" class="mt-3 w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Update Photo</button>
                </form>
            </div>

            <!-- Right: Admin Info -->
            <div class="md:w-2/3">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($admin['name']); ?>" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone']); ?>" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($admin['username']); ?>" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">Bio</label>
                            <textarea name="bio" rows="3" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($admin['bio']); ?></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 gap-4">
                        <button type="submit" name="update_profile" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Changes</button>
                        <a href="logout.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Logout</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    // Profile Image Preview
    const profileInput = document.querySelector('input[name="profile_image"]');
    if(profileInput){
        profileInput.addEventListener('change', function(event){
            const reader = new FileReader();
            reader.onload = function(e){
                document.querySelector('.w-40 img')?.setAttribute('src', e.target.result);
            };
            reader.readAsDataURL(event.target.files[0]);
        });
    }
</script>
</body>
</html>
