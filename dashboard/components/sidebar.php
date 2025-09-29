<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = isset($userData['role']) ? $userData['role'] : 'student';

// Function to highlight active menu item
function isActive($page, $current_page){
    return $page === $current_page ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
}
?>

<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300" id="sidebar">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-4 py-6 border-b dark:border-gray-700">
        <a href="dashboard.php" class="flex items-center">
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">VisionNex ERA</span>
        </a>
        <button id="closeSidebar" class="p-2 rounded-md lg:hidden">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="px-4 py-6">
        <ul class="space-y-1">
            <li>
                <a href="./admin-dashboard.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('admin-dashboard.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href=".\porfile.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('profile.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="./student_management.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('students_management.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="./teacher_management.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('teachers_management.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"></path>
                    </svg>
                    <span>Teachers</span>
                </a>
            </li>
            <li>
                <a href="./Add.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('courses.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13M7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253M16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Courses</span>
                </a>
            </li>
            <li>
                <a href="./time-table.php" class="flex items-center px-4 py-3 rounded-md <?php echo isActive('settings.php', $current_page); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Schedule</span>
                </a>
            </li>
        </ul>

        <!-- Logout -->
        <div class="mt-8 pt-6 border-t dark:border-gray-700">
            <a href="../api/logout.php" class="flex items-center px-4 py-3 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Mobile Sidebar Toggle Button -->
<button id="sidebar-toggle" class="fixed bottom-4 right-4 z-40 md:hidden bg-blue-600 text-white p-3 rounded-full shadow-lg">
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
