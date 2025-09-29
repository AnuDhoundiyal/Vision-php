```php
<?php
require_once '../../public/config/config.php'; // Includes db.php and functions.php
check_auth('admin'); // Ensure only admins can access

$pageTitle = "Admin Profile";
$conn = $db->getConnection();

// ---------- CONFIG ----------
$adminId = $_SESSION['user_id']; // Use session user ID

// ---------- FETCH DATA ----------
$admin = null;
$stmt  = $conn->prepare("SELECT id, full_name, email, profile_image FROM users WHERE id = ? AND role = 'admin'");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$admin  = $result->fetch_assoc();
$stmt->close();

// fallback if empty
if (!$admin) {
    // This should ideally not happen if check_auth works, but as a safeguard
    $admin = [
        'id' => $adminId,
        'full_name' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'profile_image' => $_SESSION['profile_image'] ?? ''
    ];
    show_toast("Admin profile data not found in database. Displaying session data.", "warning");
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName  = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'] ?? ''; // Optional password change

    $updateFields = "full_name = ?, email = ?";
    $updateParams = [$fullName, $email];
    $updateTypes = "ss";

    // Handle profile image upload
    $profileImageRelPath = $admin['profile_image']; // Keep old image by default
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = upload_file($_FILES['profile_image'], $config['UPLOAD_DIR'] . '/users'); // Store in a generic 'users' subfolder
        if (!$uploadResult['success']) {
            show_toast("Image upload failed: " . $uploadResult['message'], 'error');
            goto end_profile_update; // Skip further processing
        }
        $profileImageRelPath = 'users/' . $uploadResult['filename'];
        // Optionally delete old image file
        if ($admin['profile_image'] && file_exists($config['UPLOAD_DIR'] . '/' . $admin['profile_image'])) {
            unlink($config['UPLOAD_DIR'] . '/' . $admin['profile_image']);
        }
    }
    $updateFields .= ", profile_image = ?";
    $updateParams[] = $profileImageRelPath;
    $updateTypes .= "s";

    // Handle password change if provided
    if (!empty($password)) {
        if (strlen($password) < 6) {
            show_toast("Password must be at least 6 characters.", 'error');
            goto end_profile_update;
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $updateFields .= ", password = ?";
        $updateParams[] = $hashedPassword;
        $updateTypes .= "s";
    }

    $updateParams[] = $adminId; // Add ID for WHERE clause
    $updateTypes .= "i";

    $stmt = $conn->prepare("UPDATE users SET $updateFields WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param($updateTypes, ...$updateParams);
        if ($stmt->execute()) {
            $_SESSION['username'] = $fullName;
            $_SESSION['email'] = $email;
            $_SESSION['profile_image'] = $profileImageRelPath; // Update session with new image path
            show_toast("Profile updated successfully!", 'success');
            log_activity($adminId, 'Admin Profile Updated', "Admin {$fullName} updated their profile.");
            header("Location: profile.php"); // Redirect to refresh page and session data
            exit;
        } else {
            show_toast("Error updating profile: " . $conn->error, 'error');
            log_activity($adminId, 'Admin Profile Update Failed', "Error: {$conn->error}");
        }
        $stmt->close();
    } else {
        show_toast("Database error during profile update. Please try again.", 'error');
        error_log("Profile update prepare statement failed: " . $conn->error);
    }
    end_profile_update:;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?> - VisionNex ERA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <?php include_once __DIR__ . '/../components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto lg:ml-64 p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 dark:text-white"><?= $pageTitle ?></h1>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col md:flex-row gap-6">
            <!-- Left: Profile Image -->
            <div class="flex flex-col items-center md:w-1/3">
                <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-gray-300 dark:border-gray-600 bg-indigo-200 flex items-center justify-center text-white text-4xl font-bold">
                    <?php if(!empty($admin['profile_image'])): ?>
                        <img id="profilePreview" src="<?= $config['UPLOAD_DIR_REL'] . '/' . htmlspecialchars($admin['profile_image']); ?>" class="w-full h-full object-cover" alt="Profile Image">
                    <?php else: ?>
                        <img id="profilePreview" src="https://ui-avatars.com/api/?name=<?= urlencode($admin['full_name'] ?: 'Admin') ?>&background=0D8ABC&color=fff" class="w-full h-full object-cover" alt="Profile Image">
                    <?php endif; ?>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" class="mt-4 w-full">
                    <input type="file" name="profile_image" id="profile_image_input" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-200">
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
                            <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name']); ?>" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-gray-700 dark:text-gray-300">New Password (Leave blank to keep current)</label>
                            <input type="password" name="password" class="w-full p-2 border rounded dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Min 6 characters if changing.</p>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 gap-4">
                        <button type="submit" name="update_profile" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Changes</button>
                        <a href="/public/logout.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Logout</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div id="toast-container"></div>
<?php display_toast_from_session(); ?>
<script>
    // Profile Image Preview
    const profileInput = document.getElementById('profile_image_input');
    if(profileInput){
        profileInput.addEventListener('change', function(event){
            const file = event.target.files[0];
            if(file){
                const reader = new FileReader();
                reader.onload = function(e){
                    document.getElementById('profilePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
</body>
</html>
```