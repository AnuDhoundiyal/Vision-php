@echo off
echo Starting VisionNex Face Recognition Service...
echo.

REM Check if Python is installed
python --version >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Python is not installed or not in PATH. Please install Python 3.6 or higher.
    pause
    exit /b 1
)

REM Check if required packages are installed
echo Checking required packages...
pip show flask face_recognition mysql-connector-python >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Installing required packages...
    pip install flask face_recognition mysql-connector-python
    if %ERRORLEVEL% NEQ 0 (
        echo Failed to install required packages.
        pause
        exit /b 1
    )
)

echo Starting Flask server...
echo.
echo Access the service at: http://localhost:5000
echo.
echo Press Ctrl+C to stop the service
echo.

python app.py

pause