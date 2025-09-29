document.addEventListener('DOMContentLoaded', function() {
    // Fetch student data from API
    fetchStudentData();
});

/**
 * Fetch all student data from the API
 */
function fetchStudentData() {
    fetch('api/get-student-data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                showError(data.error);
                return;
            }
            
            // Update all dashboard sections with the data
            updateStudentInfo(data.student);
            updateAttendanceStats(data.attendance);
            updateTodaySchedule(data.schedule);
            updateEnrolledClasses(data.classes);
            updateSyllabusProgress(data.syllabus);
            updateRecentActivity(data.activity);
            updateWeeklyAttendance(data.attendance.weekly);
        })
        .catch(error => {
            console.error('Error fetching student data:', error);
            showError('Failed to load student data. Please refresh the page or contact support.');
        });
}

/**
 * Display error message on the dashboard
 */
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
    errorDiv.innerHTML = `<p>${message}</p>`;
    
    const container = document.querySelector('.dashboard-container') || document.body;
    container.prepend(errorDiv);
}

/**
 * Update student information section
 */
function updateStudentInfo(student) {
    if (!student) return;
    
    // Update student name and info in the sidebar
    const nameElements = document.querySelectorAll('.student-name');
    nameElements.forEach(el => {
        el.textContent = student.name;
    });
    
    // Update student ID
    const idElements = document.querySelectorAll('.student-id');
    idElements.forEach(el => {
        el.textContent = student.student_id;
    });
    
    // Update student course
    const courseElements = document.querySelectorAll('.student-course');
    courseElements.forEach(el => {
        el.textContent = student.course_name;
    });
    
    // Update profile image if available
    if (student.profile_image) {
        const imgElements = document.querySelectorAll('.student-image');
        imgElements.forEach(el => {
            el.src = '../../' + student.profile_image;
            el.onerror = function() {
                this.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(student.name) + '&background=random';
            };
        });
    }
}

/**
 * Update attendance statistics
 */
function updateAttendanceStats(attendance) {
    if (!attendance) return;
    
    // Update overall attendance percentage
    const attendancePercentElement = document.getElementById('attendance-percent');
    if (attendancePercentElement) {
        attendancePercentElement.textContent = attendance.overall + '%';
    }
    
    // Update attendance counts
    document.querySelectorAll('.present-count').forEach(el => {
        el.textContent = attendance.present;
    });
    
    document.querySelectorAll('.absent-count').forEach(el => {
        el.textContent = attendance.absent;
    });
    
    document.querySelectorAll('.late-count').forEach(el => {
        el.textContent = attendance.late;
    });
}

/**
 * Update today's schedule section
 */
function updateTodaySchedule(schedule) {
    const scheduleContainer = document.getElementById('today-schedule');
    if (!scheduleContainer) return;
    
    if (!schedule || schedule.length === 0) {
        scheduleContainer.innerHTML = '<div class="text-center py-4">No classes scheduled for today</div>';
        return;
    }
    
    let html = '';
    schedule.forEach(item => {
        html += `
        <div class="bg-white rounded-lg shadow-sm p-4 mb-3 flex items-center">
            <div class="bg-blue-100 text-blue-800 rounded-full p-3 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800">${item.course_name}</h3>
                <p class="text-sm text-gray-600">${item.teacher_name} • Room ${item.room}</p>
                <p class="text-xs text-gray-500 mt-1">${formatTime(item.start_time)} - ${formatTime(item.end_time)}</p>
            </div>
        </div>
        `;
    });
    
    scheduleContainer.innerHTML = html;
}

/**
 * Update enrolled classes section
 */
function updateEnrolledClasses(classes) {
    const classesContainer = document.getElementById('enrolled-classes');
    if (!classesContainer) return;
    
    if (!classes || classes.length === 0) {
        classesContainer.innerHTML = '<div class="text-center py-4">No enrolled classes found</div>';
        return;
    }
    
    let html = '';
    classes.forEach(item => {
        html += `
        <div class="bg-white rounded-lg shadow-sm p-4 mb-3">
            <h3 class="font-semibold text-gray-800">${item.name}</h3>
            <p class="text-sm text-gray-600">Instructor: ${item.teacher}</p>
            <p class="text-xs text-gray-500 mt-1">${item.description || 'No description available'}</p>
        </div>
        `;
    });
    
    classesContainer.innerHTML = html;
}

/**
 * Update syllabus progress section
 */
function updateSyllabusProgress(syllabus) {
    if (!syllabus) return;
    
    const progressElement = document.getElementById('syllabus-progress');
    if (progressElement) {
        progressElement.style.width = syllabus.progress + '%';
        progressElement.setAttribute('aria-valuenow', syllabus.progress);
    }
    
    const progressTextElement = document.getElementById('syllabus-progress-text');
    if (progressTextElement) {
        progressTextElement.textContent = syllabus.progress + '%';
    }
}

/**
 * Update recent activity section
 */
function updateRecentActivity(activities) {
    const activityContainer = document.getElementById('recent-activity');
    if (!activityContainer) return;
    
    if (!activities || activities.length === 0) {
        activityContainer.innerHTML = '<div class="text-center py-4">No recent activity</div>';
        return;
    }
    
    let html = '';
    activities.forEach(item => {
        let iconClass = 'text-blue-500';
        let icon = 'calendar';
        
        if (item.type === 'attendance') {
            if (item.details === 'present') {
                iconClass = 'text-green-500';
                icon = 'check-circle';
            } else if (item.details === 'absent') {
                iconClass = 'text-red-500';
                icon = 'x-circle';
            } else if (item.details === 'late') {
                iconClass = 'text-yellow-500';
                icon = 'clock';
            }
        }
        
        html += `
        <div class="flex items-start mb-4">
            <div class="${iconClass} mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M${getIconPath(icon)}" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-800">${item.message}</p>
                <p class="text-xs text-gray-500">${formatDate(item.date)}</p>
            </div>
        </div>
        `;
    });
    
    activityContainer.innerHTML = html;
}

/**
 * Update weekly attendance chart
 */
function updateWeeklyAttendance(weeklyData) {
    if (!weeklyData || !weeklyData.length) return;
    
    const chartCanvas = document.getElementById('weekly-attendance-chart');
    if (!chartCanvas) return;
    
    const labels = weeklyData.map(day => day.formatted_date);
    const data = weeklyData.map(day => {
        if (day.status === 'present') return 1;
        if (day.status === 'late') return 0.5;
        return 0; // absent
    });
    
    const statusColors = weeklyData.map(day => {
        if (day.status === 'present') return '#10B981'; // green
        if (day.status === 'late') return '#F59E0B'; // yellow
        return '#EF4444'; // red for absent
    });
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }
    
    // Destroy existing chart if it exists
    if (window.weeklyAttendanceChart) {
        window.weeklyAttendanceChart.destroy();
    }
    
    // Create new chart
    window.weeklyAttendanceChart = new Chart(chartCanvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Attendance',
                data: data,
                backgroundColor: statusColors,
                borderColor: statusColors,
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        callback: function(value) {
                            if (value === 0) return 'Absent';
                            if (value === 0.5) return 'Late';
                            if (value === 1) return 'Present';
                            return '';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            if (value === 0) return 'Absent';
                            if (value === 0.5) return 'Late';
                            if (value === 1) return 'Present';
                            return '';
                        }
                    }
                }
            }
        }
    });
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
    
    return `${hour}:${minutes} ${ampm}`;
}

/**
 * Helper function to format date (YYYY-MM-DD to Month DD, YYYY)
 */
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

/**
 * Helper function to get SVG path for different icons
 */
function getIconPath(icon) {
    switch (icon) {
        case 'check-circle':
            return '9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'x-circle':
            return '10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'clock':
            return '12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
        case 'calendar':
        default:
            return '8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z';
    }
}