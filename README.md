# VisionNEX PHP Attendance System

## 🎯 Overview
VisionNEX is a comprehensive face recognition-based attendance management system built with PHP, MySQL, HTML, CSS, JavaScript, and jQuery. It provides automated attendance tracking using facial recognition technology with role-based dashboards for administrators, teachers, and students.

## ✨ Key Features

### 🔐 Authentication & Security
- **Role-based Access Control**: Admin, Teacher, and Student roles with specific permissions
- **Secure Login System**: Password hashing, session management, and account lockout protection
- **Admin-only Registration**: Only administrators can create new user accounts
- **Session Security**: Automatic timeout and secure session handling

### 👨‍💼 Admin Dashboard
- **User Management**: Complete CRUD operations for students, teachers, and classes
- **Real-time Statistics**: Live dashboard with attendance percentages and user counts
- **Attendance Monitoring**: View and manage attendance records with filtering options
- **Credential Generation**: Create secure login credentials for students and teachers
- **System Settings**: Configure face recognition thresholds and system parameters

### 👨‍🏫 Teacher Dashboard
- **Profile Management**: View and edit personal information
- **Class Management**: Overview of assigned classes with student lists
- **Attendance Marking**: Manual attendance entry with bulk operations
- **Student Reports**: Generate attendance reports for assigned classes
- **Analytics**: View attendance trends and statistics

### 👨‍🎓 Student Dashboard
- **Personal Profile**: View and edit basic personal information
- **Attendance History**: Complete attendance records with filtering and search
- **Statistics**: Personal attendance percentage and trends
- **Calendar View**: Monthly attendance overview with visual indicators

### 🎥 Face Recognition Kiosk
- **Live Camera Feed**: Real-time video capture using HTML5
- **PHP-based Recognition**: Image processing using PHP GD library
- **Auto-capture**: Configurable automatic image capture (3-5 second intervals)
- **Confidence Threshold**: Adjustable recognition accuracy (default 85%)
- **Automatic Attendance**: Instant attendance marking upon successful recognition
- **Visual Feedback**: Success/failure overlays and toast notifications

## 🛠️ System Requirements

### Server Requirements
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 8.0 or higher
- **MySQL**: Version 5.7 or higher (MySQL 8.0+ recommended)
- **PHP Extensions**:
  - `mysqli` or `pdo_mysql`
  - `gd` or `imagick` (for image processing)
  - `json`
  - `session`
  - `fileinfo`

### Client Requirements
- **Modern Web Browser**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Camera Access**: Required for face recognition kiosk functionality
- **JavaScript**: Must be enabled
- **Screen Resolution**: Minimum 1024x768 (responsive design supports mobile devices)

## 📦 Installation Guide

### Step 1: Database Setup

1. **Open phpMyAdmin**
   - Navigate to `http://localhost/phpmyadmin` (XAMPP/WAMP)
   - Or access your hosting provider's phpMyAdmin panel

2. **Create Database**
   ```sql
   CREATE DATABASE system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Schema**
   - Select the `system` database
   - Go to the "Import" tab
   - Click "Choose File" and select `database/schema.sql`
   - Click "Go" to import all tables and default data

4. **Verify Installation**
   - Check that all tables are created: `admins`, `students`, `teachers`, `classes`, `attendance`, `face_encodings`, `system_settings`, `activity_logs`, `notifications`
   - Verify the default admin user is created

### Step 2: File Structure Setup

1. **Extract Project Files**
   ```
   your-web-directory/
   ├── config/
   │   └── database.php
   ├── includes/
   │   └── functions.php
   ├── api/
   │   ├── admin/
   │   ├── teacher/
   │   └── student/
   ├── dashboard/
   │   ├── admin/
   │   ├── teacher/
   │   └── student/
   ├── public/
   │   ├── assets/
   │   ├── components/
   │   └── config/
   ├── uploads/
   │   ├── students/
   │   ├── teachers/
   │   └── admins/
   └── database/
       └── schema.sql
   ```

2. **Set Directory Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/students/
   chmod 755 uploads/teachers/
   chmod 755 uploads/admins/
   ```

### Step 3: Database Configuration

1. **Edit Database Connection**
   - Open `config/database.php`
   - Update the following variables:
   ```php
   private $host = 'localhost';        // Your MySQL host
   private $username = 'root';         // Your MySQL username
   private $password = '';             // Your MySQL password
   private $database = 'system';       // Database name
   ```

2. **Test Database Connection**
   - Navigate to `http://localhost/your-project/test-connection.php`
   - Verify successful database connection

### Step 4: Initial Admin Access

**Default Admin Credentials:**
- **Username**: `admin`
- **Email**: `admin@visionnex.com`
- **Password**: `admin123`

**⚠️ IMPORTANT**: Change the default admin password immediately after first login!

## 🚀 Usage Instructions

### Admin Dashboard Access
1. Navigate to `http://localhost/your-project/public/login.php`
2. Login with admin credentials
3. You'll be redirected to `dashboard/admin/admin-dashboard.php`

### Admin Functions

#### Adding Students
1. Go to **Students** → **Add Student**
2. Fill in required information:
   - Full Name (required)
   - Email (required, must be unique)
   - Roll Number (auto-generated if empty)
   - Class Assignment
   - Profile Image (required for face recognition)
3. Click **Save Student**
4. System will generate secure login credentials
5. Credentials can be printed or emailed to the student

#### Adding Teachers
1. Go to **Teachers** → **Add Teacher**
2. Fill in required information:
   - Full Name (required)
   - Email (required, must be unique)
   - Employee ID
   - Department and Position
   - Assigned Classes
   - Profile Image (required for face recognition)
3. Click **Save Teacher**
4. System will generate secure login credentials

#### Managing Classes
1. Go to **Classes** → **Manage Classes**
2. Create new classes with:
   - Class Name
   - Section
   - Academic Year
   - Assigned Teacher
   - Room Number and Schedule
3. Assign students to classes
4. Set class schedules and timetables

### Face Recognition Kiosk

#### Setup Instructions
1. Navigate to `http://localhost/your-project/kiosk.php`
2. Allow camera access when prompted
3. Position the kiosk device at eye level for optimal recognition
4. Ensure adequate lighting for best results

#### How It Works
1. **Auto-capture**: System captures images every 3-5 seconds
2. **Face Detection**: PHP processes the image to detect faces
3. **Recognition**: Compares detected face with stored student/teacher images
4. **Confidence Check**: Verifies recognition meets minimum threshold (85%)
5. **Attendance Marking**: Automatically records attendance in database
6. **Feedback**: Shows success/failure notification with user details

#### Kiosk Settings
- **Recognition Threshold**: Adjust confidence level (50-100%)
- **Auto-capture Delay**: Set capture interval (3-10 seconds)
- **Sound Notifications**: Enable/disable audio feedback
- **Default Class**: Set default class for attendance marking

### Teacher Dashboard

#### Accessing Teacher Dashboard
1. Login with teacher credentials provided by admin
2. Navigate to assigned dashboard at `dashboard/teacher/dashboard.php`

#### Teacher Functions
- **View Profile**: See personal information and assigned classes
- **Mark Attendance**: Manually mark student attendance for assigned classes
- **View Reports**: Generate attendance reports for students
- **Class Management**: View student lists and class schedules

### Student Dashboard

#### Accessing Student Dashboard
1. Login with student credentials provided by admin
2. Navigate to student dashboard at `dashboard/student/dashboard.php`

#### Student Functions
- **View Profile**: See personal information and class details
- **Attendance History**: View complete attendance records
- **Statistics**: See attendance percentage and trends
- **Calendar View**: Monthly attendance overview

## 🔧 Configuration

### Face Recognition Settings

#### Adjusting Recognition Threshold
1. Login as admin
2. Go to **Settings** → **System Configuration**
3. Modify **Face Recognition Threshold** (0.50 - 1.00)
4. Higher values = more strict recognition
5. Lower values = more lenient recognition

#### Image Requirements
- **Format**: JPG, PNG, or WEBP
- **Size**: Maximum 5MB per image
- **Resolution**: Minimum 300x300 pixels
- **Quality**: Clear, well-lit face images for best recognition
- **Face Position**: Face should occupy 30-70% of the image

### System Settings Configuration

#### Database Settings
```php
// config/database.php
private $host = 'localhost';
private $username = 'your_db_username';
private $password = 'your_db_password';
private $database = 'system';
```

#### Upload Directory Settings
```php
// public/config/config.php
$config = [
    'UPLOAD_DIR' => __DIR__ . '/../uploads',
    'MAX_FILE_SIZE' => 5 * 1024 * 1024, // 5MB
    'ALLOWED_MIMES' => ['image/jpeg', 'image/png', 'image/webp']
];
```

## 🔍 API Endpoints

### Admin APIs
- `api/admin/students.php` - Student CRUD operations
- `api/admin/teachers.php` - Teacher CRUD operations
- `api/admin/classes.php` - Class management
- `api/admin/attendance.php` - Attendance monitoring
- `api/admin/stats.php` - Dashboard statistics

### Teacher APIs
- `api/teacher/profile.php` - Teacher profile management
- `api/teacher/classes.php` - Assigned classes data
- `api/teacher/attendance.php` - Attendance marking and reports

### Student APIs
- `api/student/profile.php` - Student profile data
- `api/student/attendance.php` - Personal attendance records

### Face Recognition APIs
- `api/recognition/process.php` - Process captured images
- `api/recognition/enroll.php` - Enroll new face data
- `api/recognition/verify.php` - Verify face against database

## 🛡️ Security Features

### Data Protection
- **SQL Injection Prevention**: All queries use prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Token-based form protection
- **File Upload Security**: Type validation and secure storage
- **Password Security**: Bcrypt hashing with salt

### Access Control
- **Role-based Permissions**: Strict access control based on user roles
- **Session Management**: Secure session handling with timeout
- **Login Protection**: Account lockout after failed attempts
- **Audit Trail**: Complete activity logging for security monitoring

## 📊 Database Schema Details

### Core Tables

#### `admins` Table
```sql
- id (Primary Key)
- username (Unique)
- email (Unique)
- password (Hashed)
- full_name
- profile_image
- status (active/inactive)
- created_at, updated_at
```

#### `students` Table
```sql
- id (Primary Key)
- name
- roll_number (Unique)
- class_id (Foreign Key)
- email (Unique)
- password (Hashed)
- image_path
- phone, address, batch, division
- guardian information
- status (active/inactive/graduated/suspended)
- created_at, updated_at
```

#### `teachers` Table
```sql
- id (Primary Key)
- name
- employee_id (Unique)
- email (Unique)
- password (Hashed)
- image_path
- assigned_classes
- department, position, qualification
- status (active/inactive/on_leave/retired)
- created_at, updated_at
```

#### `attendance` Table
```sql
- id (Primary Key)
- user_id, user_type
- class_id (Foreign Key)
- date, time_in, time_out
- status (present/absent/late/on_leave/excused)
- recognition_confidence
- recognition_method (face_recognition/manual/kiosk/mobile)
- marked_by, ip_address, device_info
- location coordinates
- notes
- created_at, updated_at
```

## 🚨 Troubleshooting

### Common Issues

#### Database Connection Failed
**Problem**: Cannot connect to MySQL database
**Solutions**:
1. Verify MySQL service is running
2. Check database credentials in `config/database.php`
3. Ensure database `system` exists
4. Verify user has proper permissions

#### Camera Access Denied
**Problem**: Kiosk cannot access camera
**Solutions**:
1. Use HTTPS for secure camera access
2. Check browser permissions for camera
3. Ensure no other applications are using the camera
4. Try different browsers (Chrome recommended)

#### Face Recognition Not Working
**Problem**: Images not being recognized
**Solutions**:
1. Verify GD or ImageMagick extension is installed
2. Check image quality and lighting
3. Adjust recognition threshold in settings
4. Ensure profile images are properly uploaded
5. Verify upload directory permissions

#### AJAX Requests Failing
**Problem**: Forms not submitting or data not loading
**Solutions**:
1. Check browser console for JavaScript errors
2. Verify API endpoint paths are correct
3. Ensure proper file permissions
4. Check PHP error logs
5. Verify jQuery library is loaded

### Error Logs
- **PHP Errors**: Check `error_log` in your PHP configuration
- **MySQL Errors**: Check MySQL error logs
- **Application Logs**: Check `activity_logs` table for system activities

## 🔧 Advanced Configuration

### Performance Optimization
1. **Database Indexing**: Ensure all indexes are properly created
2. **Image Optimization**: Compress uploaded images for faster processing
3. **Caching**: Implement PHP caching for frequently accessed data
4. **Session Storage**: Configure session storage for better performance

### Backup Procedures
1. **Database Backup**:
   ```bash
   mysqldump -u username -p system > backup_$(date +%Y%m%d).sql
   ```
2. **File Backup**: Regularly backup the `uploads/` directory
3. **Automated Backups**: Set up cron jobs for regular backups

### Security Hardening
1. **Change Default Passwords**: Update all default credentials
2. **SSL Certificate**: Implement HTTPS for production
3. **File Permissions**: Set restrictive permissions on sensitive files
4. **Regular Updates**: Keep PHP and MySQL updated

## 📱 Mobile Compatibility
- **Responsive Design**: Works on tablets and smartphones
- **Touch-friendly Interface**: Optimized for touch interactions
- **Mobile Camera**: Supports mobile camera for attendance marking
- **Offline Capability**: Basic offline functionality for critical features

## 🎨 Customization

### Theming
- **CSS Variables**: Easy color scheme customization
- **Logo Replacement**: Upload custom institution logo
- **Layout Modification**: Responsive grid system for easy layout changes

### Feature Extensions
- **Email Notifications**: SMTP configuration for automated emails
- **SMS Integration**: Add SMS notifications for attendance alerts
- **Report Generation**: PDF and Excel export functionality
- **API Integration**: Connect with external systems

## 📞 Support & Maintenance

### Regular Maintenance Tasks
1. **Database Cleanup**: Remove old logs and temporary data
2. **Image Optimization**: Compress and organize uploaded images
3. **Security Updates**: Apply security patches regularly
4. **Performance Monitoring**: Monitor system performance and optimize

### Getting Help
- **Documentation**: Refer to inline code comments
- **Error Logs**: Check system logs for detailed error information
- **Community Support**: Join VisionNEX community forums
- **Professional Support**: Contact VisionNEX support team

## 📋 Quick Start Checklist

- [ ] Import `database/schema.sql` into phpMyAdmin
- [ ] Configure database connection in `config/database.php`
- [ ] Set proper file permissions for `uploads/` directory
- [ ] Test admin login with default credentials
- [ ] Change default admin password
- [ ] Add first student and teacher accounts
- [ ] Test face recognition kiosk functionality
- [ ] Configure system settings as needed
- [ ] Set up regular backup procedures

## 🔄 System Workflow

### Daily Operations
1. **Morning Setup**: Start kiosk systems and verify camera functionality
2. **Attendance Tracking**: Students/teachers use kiosk for automatic attendance
3. **Manual Override**: Teachers can manually mark attendance if needed
4. **Real-time Monitoring**: Admins monitor attendance throughout the day
5. **End-of-day Reports**: Generate daily attendance summaries

### Weekly/Monthly Tasks
1. **Data Backup**: Perform regular database and file backups
2. **Performance Review**: Check system performance and optimize
3. **User Management**: Add/remove users as needed
4. **Report Generation**: Create attendance reports for management
5. **System Updates**: Apply any necessary updates or patches

## 🎯 Success Metrics
- **Face Recognition Accuracy**: Target >85% recognition rate
- **System Uptime**: 99.9% availability during operational hours
- **User Satisfaction**: Positive feedback from students, teachers, and administrators
- **Data Integrity**: Zero data loss with proper backup procedures
- **Security**: No security incidents or unauthorized access

---

**© 2024 VisionNEX PHP Attendance System. All rights reserved.**

For technical support, contact: support@visionnex.com