# VisionNex Face Recognition Attendance System

## Overview
VisionNex is a comprehensive face recognition-based attendance management system designed for educational institutions. The system automates attendance tracking using facial recognition technology, providing a contactless, efficient, and secure method for recording student and teacher presence.

## Features
- **Face Recognition**: Automated attendance marking using facial recognition
- **Real-time Processing**: Instant recognition and attendance marking
- **User-friendly Kiosk Interface**: Interactive interface for attendance capture
- **Comprehensive Dashboard**: For administrators, teachers, and students
- **Attendance Reports**: Generate and export detailed attendance reports
- **Class Management**: Create and manage classes, subjects, and schedules
- **User Management**: Manage students, teachers, and administrators

## System Requirements
- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser with camera access

## Installation

### Database Setup
1. Create a MySQL database named `system`
2. Import the database schema from `database/schema.sql`
3. Configure database connection in `public/config/db.php`

```php
$servername = "localhost";
$username   = "root";
$password   = "YOUR_PASSWORD"; // Set your database password here
$dbname     = "system";
```

### Web Server Setup
1. Clone or extract the project files to your web server's document root
2. Ensure the web server has write permissions for the upload directories
3. Configure your web server to point to the project directory

## Usage

### Admin Dashboard
- URL: `/dashboard/admin/`
- Default credentials: admin/admin123
- Manage users, classes, and system settings

### Teacher Dashboard
- URL: `/dashboard/teacher/`
- View and manage class attendance
- Generate attendance reports

### Student Dashboard
- URL: `/dashboard/student/`
- View personal attendance records
- Check class schedules

### Kiosk Mode
- URL: `/attendance_project/kiosk/`
- Used for face recognition and attendance marking
- Settings for recognition confidence and auto-capture

## Face Recognition API

The system uses a PHP-based face recognition API located at:
```
/attendance_project/backend_php/api/face_recognition.php
```

API Endpoints:
- `?action=recognize` - Process an uploaded image for face recognition
- `?action=faces` - Check API status and get loaded student count

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `public/config/db.php`
- Ensure MySQL service is running
- Check database user permissions

### Camera Access Issues
- Ensure browser has camera permissions
- Use HTTPS for secure camera access
- Try a different browser if issues persist

### Face Recognition Issues
- Ensure proper lighting for better recognition
- Update student photos if recognition fails frequently
- Adjust confidence threshold in kiosk settings

## Security Considerations
- Change default admin credentials immediately
- Use strong passwords for all accounts
- Implement HTTPS for secure data transmission
- Regularly backup the database

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Support
For support and inquiries, please contact support@visionnex.example.com