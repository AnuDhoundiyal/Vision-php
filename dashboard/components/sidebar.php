```php
<?php
// Ensure session is started and user data is available
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? 'guest'; // Default to guest if not set
$user_profile_image = $_SESSION['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username'] ?? 'User');
$upload_dir_rel = 'uploads'; // Relative path to uploads directory

// Function to highlight active menu item
function isActive($page, $current_page){
    return $page === $current_page ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
}
?>

<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300" id="sidebar">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-4 py-6 border-b dark:border-gray-700">
        <a href="/index.php" class="flex items-center">
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">VisionNex ERA</span>
        </a>
        <button id="closeSidebar" class="p-2 rounded-md lg:hidden">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- User Profile Info (Admin) -->
    <?php if ($user_role === 'admin'): ?>
    <div class="px-4 py-4 border-b dark:border-gray-700 flex items-center space-x-3">
        <img src="<?= $user_profile_image ? $upload_dir_rel . '/' . $user_profile_image : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username'] ?? 'Admin') ?>" alt="Admin Profile" class="w-10 h-10 rounded-full object-cover">
        <div>
            <p class="text-gray-800 dark:text-white font-semibold"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
            <p class="text-sm text-gray-500 dark:text-gray-400 capitalize"><?= htmlspecialchars($user_role) ?> Dashboard</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="px-4 py-6">
        <ul class="space-y-1">
            <?php if ($user_role === 'admin'): ?>
            <li>
                <a href="./admin-dashboard.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('admin-dashboard.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="./profile.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('profile.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="./student_management.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('student_management.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="./teacher_management.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('teacher_management.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"></path>
                    </svg>
                    <span>Teachers</span>
                </a>
            </li>
            <li>
                <a href="./Add.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('Add.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13M7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253M16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Courses & Depts</span>
                </a>
            </li>
            <li>
                <a href="./time-table.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('time-table.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="./settings.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('settings.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>
            </li>
            <?php elseif ($user_role === 'teacher'): ?>
            <li>
                <a href="/dashboard/teacher/index.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('index.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/dashboard/teacher/mark_attendance.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('mark_attendance.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Mark Attendance</span>
                </a>
            </li>
            <li>
                <a href="/dashboard/teacher/profile.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('profile.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
            </li>
            <?php elseif ($user_role === 'student'): ?>
            <li>
                <a href="/dashboard/student/dashboard.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('dashboard.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/dashboard/student/profile.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('profile.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <!-- Logout -->
        <div class="mt-8 pt-6 border-t dark:border-gray-700">
            <a href="/public/logout.php" class="flex items-center px-4 py-3 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Mobile Sidebar Toggle Button -->
<button id="sidebar-toggle" class="fixed bottom-4 right-4 z-40 lg:hidden bg-blue-600 text-white p-3 rounded-full shadow-lg">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const closeBtn = document.getElementById('closeSidebar');

    toggle.addEventListener('click', () => sidebar.classList.toggle('-translate-x-full'));
    closeBtn.addEventListener('click', () => sidebar.classList.add('-translate-x-full'));
});
</script>
```