```javascript
document.addEventListener('DOMContentLoaded', function() {
    fetchTeacherData();
    setupNavigation();
});

const UPLOAD_DIR_REL = "<?= $config['UPLOAD_DIR_REL'] ?>"; // From config.php

/**
 * Fetches all teacher data from the API and updates the dashboard.
 */
function fetchTeacherData() {
    fetch('../../api/teacher/get-teacher-data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateTeacherInfo(data.data.teacher_info);
                updateStatsCards(data.data.stats);
                updateTodaySchedule(data.data.today_schedule);
                updateWeeklyAttendanceChart(data.data.weekly_attendance_data);
                // Populate other sections if needed
                updateMyClassesSection(data.data.assigned_classes);
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching teacher data:', error);
            showToast('Failed to load teacher data. Please refresh or contact support.', 'error');
        });
}

/**
 * Updates teacher's name and avatar in the header.
 */
function updateTeacherInfo(teacherInfo) {
    if (!teacherInfo) return;
    document.getElementById('teacherName').textContent = teacherInfo.full_name;
    const avatarImg = document.getElementById('teacherAvatar');
    if (avatarImg) {
        avatarImg.src = teacherInfo.profile_image ? \`${UPLOAD_DIR_REL}/${teacherInfo.profile_image}` : \`https://ui-avatars.com/api/?name=${encodeURIComponent(teacherInfo.full_name)}&background=random`;
    }
}

/**
 * Updates the statistics cards on the dashboard.
 */
function updateStatsCards(stats) {
    document.getElementById('totalClasses').textContent = stats.total_classes;
    document.getElementById('totalStudents').textContent = stats.total_students_assigned;
    document.getElementById('todayClasses').textContent = stats.today_classes_count;
    document.getElementById('attendanceRate').textContent = \`${stats.average_attendance_rate}%`;
}

/**
 * Updates today's schedule section.
 */
function updateTodaySchedule(schedule) {
    const scheduleContainer = document.getElementById('todaySchedule');
    if (!scheduleContainer) return;

    if (!schedule || schedule.length === 0) {
        scheduleContainer.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">No classes scheduled for today.</div>';
        return;
    }

    let html = '';
    schedule.forEach(item => {
        html += `
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex items-center shadow-sm">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-clock"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800 dark:text-white">${item.course_name} (${item.batch}/${item.division})</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">${formatTime(item.start_time)} - ${formatTime(item.end_time)} in Room ${item.room}</p>
            </div>
        </div>
        `;
    });
    scheduleContainer.innerHTML = html;
}

/**
 * Initializes and updates the weekly attendance chart.
 */
let attendanceChartInstance = null;
function updateWeeklyAttendanceChart(weeklyData) {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    if (!ctx) return;

    const labels = weeklyData.map(day => day.day_label);
    const presentData = weeklyData.map(day => day.present);
    const absentData = weeklyData.map(day => day.absent);
    const lateData = weeklyData.map(day => day.late);

    if (attendanceChartInstance) {
        attendanceChartInstance.destroy();
    }

    attendanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Present',
                    data: presentData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)', // Green
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Absent',
                    data: absentData,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)', // Red
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Late',
                    data: lateData,
                    backgroundColor: 'rgba(245, 158, 11, 0.8)', // Orange
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(200, 200, 200, 0.2)' }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: document.body.classList.contains('dark') ? '#cbd5e1' : '#4b5563'
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
}

/**
 * Populates the "My Classes" section.
 */
function updateMyClassesSection(classes) {
    const classesContent = document.getElementById('classesContent');
    if (!classesContent) return;

    if (!classes || classes.length === 0) {
        classesContent.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-4">No classes assigned.</div>';
        return;
    }

    let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
    classes.forEach(cls => {
        html += `
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-5 border border-gray-200 dark:border-gray-600">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">${cls.course_name}</h4>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">${cls.batch}/${cls.division} - ${cls.department_name}</p>
            <div class="flex items-center text-gray-700 dark:text-gray-300 text-sm">
                <i class="fas fa-user-graduate mr-2"></i>
                <span>${cls.student_count} Students</span>
            </div>
            <div class="mt-4 flex justify-end">
                <a href="mark_attendance.php?course_id=${cls.course_id}&batch=${cls.batch}&division=${cls.division}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Mark Attendance <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        `;
    });
    html += '</div>';
    classesContent.innerHTML = html;
}


/**
 * Helper function to format time (HH:MM:SS to h:mm a)
 */
function formatTime(timeString) {
    if (!timeString) return '';
    const [hours, minutes] = timeString.split(':');
    let hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12;
    hour = hour ? hour : 12; // Convert 0 to 12
    return \`${hour}:${minutes} ${ampm}`;
}

/**
 * Sets up navigation for sidebar links.
 */
function setupNavigation() {
    const navItems = document.querySelectorAll('.sidebar-nav-item');
    const sections = document.querySelectorAll('.section-content');

    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            navItems.forEach(nav => nav.classList.remove('bg-green-50', 'text-green-600', 'border-green-600'));
            this.classList.add('bg-green-50', 'text-green-600', 'border-green-600');

            const targetSectionId = this.getAttribute('href').substring(1);
            sections.forEach(section => {
                if (section.id === targetSectionId) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            });
            document.getElementById('pageTitle').textContent = this.textContent.trim();
        });
    });

    // Show dashboard by default
    document.getElementById('dashboard-section').classList.remove('hidden');
    document.querySelector('.sidebar-nav-item[href="#dashboard"]').classList.add('bg-green-50', 'text-green-600', 'border-green-600');
}

/**
 * Displays a toast notification.
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        console.warn('Toast container not found. Create a div with id="toast-container"');
        return;
    }

    const toast = document.createElement('div');
    toast.className = \`p-4 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-y-full opacity-0
        ${type === 'success' ? 'bg-green-500' :
          type === 'error' ? 'bg-red-500' :
          type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'}`;
    toast.textContent = message;
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-y-full', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-full', 'opacity-0');
        toast.addEventListener('transitionend', function() {
            toast.remove();
        });
    }, 3000);
}
```