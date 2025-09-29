
<!-- Teacher Dashboard Sidebar -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg
              transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    
    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-6 border-b dark:border-gray-700">
        <a href="index.php" class="flex items-center">
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                VisionNex ERA
            </span>
        </a>
        <button id="closeSidebar" class="p-2 rounded-md lg:hidden">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Optional welcome text (static – edit or remove) -->
    <div class="px-4 py-4 border-b dark:border-gray-700">
        <p class="text-gray-700 dark:text-gray-200">
            Welcome, <span class="font-semibold">Teacher</span>
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">Dashboard</p>
    </div>

    <!-- Navigation Links -->
    <nav class="px-4 py-6">
        <ul class="space-y-1">
            <li>
                <a href="index.php" class="flex items-center px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="students_management.php" class="flex items-center px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="manual_attendance.php" class="flex items-center px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Manual Attendance</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="flex items-center px-4 py-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Mobile Toggle Button -->
<button id="sidebar-toggle"
        class="fixed bottom-4 right-4 z-40 md:hidden bg-blue-600 text-white p-3 rounded-full shadow-lg">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    document.getElementById('sidebar-toggle').addEventListener('click',
        () => sidebar.classList.toggle('-translate-x-full'));
    document.getElementById('closeSidebar').addEventListener('click',
        () => sidebar.classList.add('-translate-x-full'));
});
</script>
