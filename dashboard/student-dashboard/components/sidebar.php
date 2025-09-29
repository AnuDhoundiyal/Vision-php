  <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg z-40 sidebar-transition">
        <div class="p-6 border-b">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-graduate text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">VisionNex</h1>
                    <p class="text-sm text-gray-500">Student Portal</p>
                </div>
            </div>
        </div>
        
        <nav class="mt-6">
            <a href="#dashboard" onclick="showSection('dashboard')" class="nav-item flex items-center px-6 py-3 text-blue-600 bg-blue-50 border-r-2 border-blue-600">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="#attendance" onclick="showSection('attendance')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-calendar-check mr-3"></i> My Attendance
            </a>
            <a href="#classes" onclick="showSection('classes')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-door-open mr-3"></i> My Classes
            </a>
            <a href="#syllabus" onclick="showSection('syllabus')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-book mr-3"></i> Syllabus Progress
            </a>
            <a href="#performance" onclick="showSection('performance')" class="nav-item flex items-center px-6 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-line mr-3"></i> Performance
            </a>
        </nav>
        
        <div class="absolute bottom-4 left-6 right-6">
            <button onclick="logout()" class="w-full flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
        </div>
    </div>
