

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - VisionNex ERA</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-indigo-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-10">
            <a href="dashboard.php" class="text-white flex items-center space-x-2 px-4">
                <i class="fas fa-brain text-2xl"></i>
                <span class="text-2xl font-bold">VisionNex ERA</span>
            </a>
            
            <nav class="mt-10">
                <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="profile.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-user mr-2"></i>Profile
                </a>
                <a href="students.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-user-graduate mr-2"></i>Students
                </a>
                <a href="teachers.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>Teachers
                </a>
                <a href="courses.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-book mr-2"></i>Courses
                </a>
                <a href="attendance.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-clipboard-check mr-2"></i>Attendance
                </a>
                <a href="reports.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
                <a href="settings.php" class="block py-2.5 px-4 rounded transition duration-200 bg-indigo-700">
                    <i class="fas fa-cog mr-2"></i>Settings
                </a>
                <a href="../logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-md flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="text-gray-500 focus:outline-none md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800 ml-4">System Settings</h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center focus:outline-none">
                            <span class="mr-2 text-sm font-semibold text-gray-700"><?php echo $_SESSION['name']; ?></span>
                            <img class="h-8 w-8 rounded-full object-cover" src="../assets/images/admin-avatar.png" alt="Admin Avatar">
                        </button>
                        <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 hidden">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 bg-gray-100">
                <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?php echo $success_message; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo $error_message; ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Settings Tabs -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="flex border-b">
                        <button id="tab-general" class="tab-btn px-6 py-3 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">General</button>
                        <button id="tab-attendance" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">Attendance</button>
                        <button id="tab-permissions" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">Permissions</button>
                        <button id="tab-backup" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">Backup & Security</button>
                        <button id="tab-maintenance" class="tab-btn px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">Maintenance</button>
                    </div>
                    
                    <!-- General Settings -->
                    <div id="content-general" class="tab-content p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">General Settings</h3>
                        
                        <form action="settings.php" method="POST" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="institution_name" class="block text-sm font-medium text-gray-700 mb-1">Institution Name</label>
                                    <input type="text" id="institution_name" name="institution_name" value="<?php echo $settings['institution_name']; ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                </div>
                                
                                <div>
                                    <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-1">Theme Color</label>
                                    <select id="theme_color" name="theme_color" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="indigo" <?php echo $settings['theme_color'] == 'indigo' ? 'selected' : ''; ?>>Indigo (Default)</option>
                                        <option value="blue" <?php echo $settings['theme_color'] == 'blue' ? 'selected' : ''; ?>>Blue</option>
                                        <option value="green" <?php echo $settings['theme_color'] == 'green' ? 'selected' : ''; ?>>Green</option>
                                        <option value="red" <?php echo $settings['theme_color'] == 'red' ? 'selected' : ''; ?>>Red</option>
                                        <option value="purple" <?php echo $settings['theme_color'] == 'purple' ? 'selected' : ''; ?>>Purple</option>
                                    </select>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Institution Logo</label>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <?php if (!empty($settings['logo'])): ?>
                                                <img id="logo_preview" class="h-16 w-auto object-contain" src="../assets/images/<?php echo $settings['logo']; ?>" alt="Institution Logo">
                                            <?php else: ?>
                                                <div id="logo_preview" class="h-16 w-32 flex items-center justify-center bg-gray-100 text-gray-400 text-xs">No Logo</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" id="logo" name="logo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*">
                                            <p class="mt-1 text-xs text-gray-500">JPG, PNG or GIF. Max 2MB.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="update_general" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    Save General Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Attendance Settings -->
                    <div id="content-attendance" class="tab-content p-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Attendance Settings</h3>
                        
                        <form action="settings.php" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="enable_face_recognition" name="enable_face_recognition" class="rounded text-indigo-600 focus:ring-indigo-500" <?php echo $settings['enable_face_recognition'] ? 'checked' : ''; ?>>
                                        <label for="enable_face_recognition" class="ml-2 block text-sm text-gray-700">Enable Face Recognition</label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Use facial recognition for attendance tracking.</p>
                                </div>
                                
                                <div>
                                    <label for="attendance_threshold" class="block text-sm font-medium text-gray-700 mb-1">Attendance Confidence Threshold (%)</label>
                                    <input type="number" id="attendance_threshold" name="attendance_threshold" value="<?php echo $settings['attendance_threshold']; ?>" min="50" max="100" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">Minimum confidence level required for face recognition.</p>
                                </div>
                                
                                <div>
                                    <label for="late_threshold" class="block text-sm font-medium text-gray-700 mb-1">Late Threshold (minutes)</label>
                                    <input type="number" id="late_threshold" name="late_threshold" value="<?php echo $settings['late_threshold']; ?>" min="1" max="60" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">Minutes after scheduled time to mark as late.</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="update_attendance" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    Save Attendance Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Permissions Settings -->
                    <div id="content-permissions" class="tab-content p-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Role Permissions</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dashboard</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Students</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teachers</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Courses</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Attendance</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reports</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Settings</th>
                                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($roles)): ?>
                                        <tr>
                                            <td colspan="9" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">No roles defined.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($roles as $role): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="py-2 px-4 border-b border-gray-200 font-medium"><?php echo ucfirst($role['name']); ?></td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none">No Access</option>
                                                        <option value="view" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none" <?php echo $role['name'] === 'student' ? 'selected' : ''; ?>>No Access</option>
                                                        <option value="view" <?php echo $role['name'] === 'teacher' ? 'selected' : ''; ?>>View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none" <?php echo $role['name'] === 'student' ? 'selected' : ''; ?>>No Access</option>
                                                        <option value="view" <?php echo $role['name'] === 'teacher' ? 'selected' : ''; ?>>View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none" <?php echo $role['name'] === 'student' ? 'selected' : ''; ?>>No Access</option>
                                                        <option value="view" <?php echo $role['name'] === 'teacher' ? 'selected' : ''; ?>>View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none" <?php echo $role['name'] === 'student' ? 'selected' : ''; ?>>No Access</option>
                                                        <option value="view" <?php echo $role['name'] === 'teacher' ? 'selected' : ''; ?>>View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none" <?php echo $role['name'] === 'student' ? 'selected' : ''; ?>>No Access</option>
                                                        <option value="view" <?php echo $role['name'] === 'teacher' ? 'selected' : ''; ?>>View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <select class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                        <option value="none" <?php echo $role['name'] !== 'admin' ? 'selected' : ''; ?>>No Access</option>
                                                        <option value="view">View</option>
                                                        <option value="edit" <?php echo $role['name'] === 'admin' ? 'selected' : ''; ?>>Edit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    <button class="text-indigo-600 hover:text-indigo-900" <?php echo $role['name'] === 'admin' ? 'disabled' : ''; ?>>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6 flex justify-between">
                            <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                                <i class="fas fa-plus mr-2"></i> Add New Role
                            </button>
                            
                            <button class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                Save Permissions
                            </button>
                        </div>
                    </div>
                    
                    <!-- Backup & Security Settings -->
                    <div id="content-backup" class="tab-content p-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Backup & Security Settings</h3>
                        
                        <form action="settings.php" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="backup_frequency" class="block text-sm font-medium text-gray-700 mb-1">Backup Frequency</label>
                                    <select id="backup_frequency" name="backup_frequency" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="daily" <?php echo $settings['backup_frequency'] == 'daily' ? 'selected' : ''; ?>>Daily</option>
                                        <option value="weekly" <?php echo $settings['backup_frequency'] == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                        <option value="monthly" <?php echo $settings['backup_frequency'] == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                        <option value="manual" <?php echo $settings['backup_frequency'] == 'manual' ? 'selected' : ''; ?>>Manual Only</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="email_notifications" name="email_notifications" class="rounded text-indigo-600 focus:ring-indigo-500" <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                        <label for="email_notifications" class="ml-2 block text-sm text-gray-700">Email Backup Notifications</label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Receive email notifications when backups are completed.</p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Manual Backup</h4>
                                <p class="text-sm text-gray-600 mb-4">Create a manual backup of your database and system files.</p>
                                
                                <div class="flex space-x-4">
                                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                        <i class="fas fa-database mr-2"></i> Backup Database
                                    </button>
                                    
                                    <button type="button" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50">
                                        <i class="fas fa-file-archive mr-2"></i> Backup Files
                                    </button>
                                    
                                    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i> Backup All
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Backup History</h4>
                                
                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                                        <div>
                                            <span class="font-medium">Full Backup</span>
                                            <span class="text-sm text-gray-500 ml-2">2023-06-15 08:30:22</span>
                                        </div>
                                        <div>
                                            <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 mr-2">
                                                <i class="fas fa-redo-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-2 border-b border-gray-200">
                                        <div>
                                            <span class="font-medium">Database Backup</span>
                                            <span class="text-sm text-gray-500 ml-2">2023-06-08 09:15:47</span>
                                        </div>
                                        <div>
                                            <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 mr-2">
                                                <i class="fas fa-redo-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-2">
                                        <div>
                                            <span class="font-medium">Files Backup</span>
                                            <span class="text-sm text-gray-500 ml-2">2023-06-01 14:22:10</span>
                                        </div>
                                        <div>
                                            <button class="text-blue-600 hover:text-blue-800 mr-2">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="text-green-600 hover:text-green-800 mr-2">
                                                <i class="fas fa-redo-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="update_backup" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    Save Backup Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Maintenance Settings -->
                    <div id="content-maintenance" class="tab-content p-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Maintenance Settings</h3>
                        
                        <form action="settings.php" method="POST">
                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" class="rounded text-indigo-600 focus:ring-indigo-500" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                    <label for="maintenance_mode" class="ml-2 block text-sm text-gray-700">Enable Maintenance Mode</label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">When enabled, only administrators can access the system.</p>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-2">System Cleanup</h4>
                                <p class="text-sm text-gray-600 mb-4">Clean up temporary files and optimize database.</p>
                                
                                <div class="flex space-x-4">
                                    <button type="button" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                        <i class="fas fa-broom mr-2"></i> Clear Cache
                                    </button>
                                    
                                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                                        <i class="fas fa-database mr-2"></i> Optimize Database
                                    </button>
                                    
                                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                        <i class="fas fa-trash-alt mr-2"></i> Clear Logs
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-2">System Information</h4>
                                
                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm"><span class="font-medium">PHP Version:</span> <?php echo phpversion(); ?></p>
                                            <p class="text-sm"><span class="font-medium">MySQL Version:</span> 5.7.36</p>
                                            <p class="text-sm"><span class="font-medium">Server:</span> Apache/2.4.51</p>
                                        </div>
                                        <div>
                                            <p class="text-sm"><span class="font-medium">System Version:</span> VisionNex ERA 1.0</p>
                                            <p class="text-sm"><span class="font-medium">Last Update:</span> June 15, 2023</p>
                                            <p class="text-sm"><span class="font-medium">Database Size:</span> 24.5 MB</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="update_maintenance" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">
                                    Save Maintenance Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });
        
        // User Menu Toggle
        document.getElementById('userMenuBtn').addEventListener('click', function() {
            document.getElementById('userMenu').classList.toggle('hidden');
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userMenuBtn = document.getElementById('userMenuBtn');
            
            if (!userMenuBtn.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Tab Navigation
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-b-2', 'border-indigo-600', 'text-indigo-600');
                    btn.classList.add('text-gray-500');
                });
                
                // Add active class to clicked button
                button.classList.add('border-b-2', 'border-indigo-600', 'text-indigo-600');
                button.classList.remove('text-gray-500');
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show corresponding tab content
                const tabId = button.id.replace('tab-', 'content-');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });
        
        // Logo preview
        document.getElementById('logo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logo_preview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        // Replace div with img
                        const img = document.createElement('img');
                        img.id = 'logo_preview';
                        img.className = 'h-16 w-auto object-contain';
                        img.src = e.target.result;
                        img.alt = 'Institution Logo';
                        preview.parentNode.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>