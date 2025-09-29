# VisionNEX PHP Attendance System - Quick Setup Guide

## 🚀 Quick Installation (5 Minutes)

### Step 1: Download and Extract
1. Extract all project files to your web server directory
2. For XAMPP: Place in `C:\xampp\htdocs\visionnex\`
3. For WAMP: Place in `C:\wamp64\www\visionnex\`

### Step 2: Database Setup in phpMyAdmin

1. **Open phpMyAdmin**
   - XAMPP: `http://localhost/phpmyadmin`
   - WAMP: `http://localhost/phpmyadmin`

2. **Create Database**
   - Click "New" in the left sidebar
   - Database name: `system`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import Schema**
   - Select the `system` database
   - Click "Import" tab
   - Click "Choose File" → Select `database/schema.sql`
   - Click "Go" to import

### Step 3: Configure Database Connection

1. **Edit Database Settings**
   - Open `config/database.php`
   - Update these lines:
   ```php
   private $host = 'localhost';        // Usually 'localhost'
   private $username = 'root';         // Your MySQL username
   private $password = '';             // Your MySQL password (if any)
   private $database = 'system';       // Keep as 'system'
   ```

### Step 4: Test Installation

1. **Run Connection Test**
   - Navigate to: `http://localhost/visionnex/test-connection.php`
   - Verify all green checkmarks ✅
   - Fix any red errors ❌ before proceeding

### Step 5: First Login

1. **Access Login Page**
   - Navigate to: `http://localhost/visionnex/public/login.php`

2. **Default Admin Credentials**
   - **Email**: `admin@visionnex.com`
   - **Password**: `admin123`

3. **⚠️ IMPORTANT**: Change the default password immediately!

## 🎯 Quick Start Checklist

- [ ] Extract files to web directory
- [ ] Create `system` database in phpMyAdmin
- [ ] Import `database/schema.sql`
- [ ] Configure `config/database.php`
- [ ] Run `test-connection.php` (all green checks)
- [ ] Login with default admin credentials
- [ ] Change default admin password
- [ ] Add your first student and teacher

## 🔧 Common Issues & Solutions

### ❌ Database Connection Failed
**Solution**: Check MySQL service is running and credentials are correct

### ❌ Upload Directory Not Writable
**Solution**: Set folder permissions to 755:
```bash
chmod 755 uploads/
chmod 755 uploads/students/
chmod 755 uploads/teachers/
chmod 755 uploads/admins/
```

### ❌ GD Extension Not Loaded
**Solution**: Enable GD in `php.ini`:
```ini
extension=gd
```
Restart Apache/Nginx after changes.

### ❌ Camera Access Denied
**Solution**: 
- Use HTTPS for secure camera access
- Allow camera permissions in browser
- Try Chrome browser (recommended)

## 📱 System URLs

- **Login**: `http://localhost/visionnex/public/login.php`
- **Admin Dashboard**: `http://localhost/visionnex/dashboard/admin/admin-dashboard.php`
- **Teacher Dashboard**: `http://localhost/visionnex/dashboard/teacher/dashboard.php`
- **Student Dashboard**: `http://localhost/visionnex/dashboard/student/dashboard.php`
- **Face Recognition Kiosk**: `http://localhost/visionnex/kiosk.php`

## 🎉 You're Ready!

Once all checkmarks are green in the connection test, your VisionNEX system is ready to use. Start by logging in as admin and adding your first students and teachers.

For detailed documentation, see the main `README.md` file.