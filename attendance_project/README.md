# VisionNex Face Recognition Attendance System

## Overview
This system provides automated attendance tracking using facial recognition technology. It consists of two main components:

1. **Face Recognition Service**: A Flask-based API that handles face detection, recognition, and attendance marking
2. **Kiosk Interface**: A web-based kiosk mode for capturing and processing student faces

## Setup Instructions

### Prerequisites
- XAMPP (for PHP and MySQL)
- Python 3.6 or higher
- Required Python packages: flask, face_recognition, mysql-connector-python

### Installation

1. **Database Setup**:
   - Ensure MySQL is running through XAMPP
   - The system uses the 'system' database with tables for students and attendance

2. **Face Recognition Service**:
   - Navigate to the `face_service` directory
   - Run `run_service.bat` to start the service
   - The service will run on http://localhost:5000

3. **Kiosk Interface**:
   - Access the kiosk through your web browser at: http://localhost/php_project%20-%20Copy/attendance_project/kiosk/

## Usage

### Face Recognition Service

The service provides these endpoints:
- `GET /`: Service status and information
- `POST /recognize`: Recognize a face from an uploaded image
- `GET /reload`: Reload student faces from the database

### Kiosk Mode

1. Click "Start Camera" to activate your webcam
2. Position your face in the frame
3. Either:
   - Click "Capture" to manually capture and recognize
   - Enable auto-capture in settings for automatic recognition
4. Upon successful recognition:
   - Student details will be displayed
   - Attendance will be automatically marked in the database

### Settings

Access settings by clicking the gear icon in the top-right corner:
- **Recognition Confidence Threshold**: Minimum confidence level for a positive match (50-100%)
- **Auto-capture Delay**: Time between automatic captures when enabled
- **Sound Notifications**: Enable/disable sound effects
- **Auto-capture**: Enable/disable automatic face capture

## Troubleshooting

- **Camera not working**: Ensure browser permissions are granted for camera access
- **Recognition service offline**: Check that the Flask service is running (run_service.bat)
- **Database connection issues**: Verify MySQL is running and credentials are correct
- **No faces recognized**: Ensure student profile images are properly uploaded and contain clear face images

## System Architecture

- **Backend**: PHP for web interface, Python/Flask for face recognition
- **Frontend**: HTML, CSS (Tailwind), JavaScript
- **Database**: MySQL for student data and attendance records
- **Face Recognition**: Uses face_recognition library (based on dlib)