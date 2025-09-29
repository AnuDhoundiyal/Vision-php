# ------------------------------
# PowerShell script for Attendance API
# ------------------------------

# 1️⃣ Navigate to project folder
cd "C:\xampp\htdocs\php_project - Copy\attendance_project\face_service"

# 2️⃣ Activate virtual environment
Write-Host "Activating virtual environment..."
.\venv\Scripts\Activate.ps1

# 3️⃣ Install requirements (if missing)
Write-Host "Installing required packages..."
pip install flask mysql-connector-python -q

# 4️⃣ Start Flask server in background
Write-Host "Starting Flask server..."
Start-Process powershell -ArgumentList "-NoExit","python app.py"

Start-Sleep -Seconds 3  # Give the server a few seconds to start

# 5️⃣ Test homepage
Write-Host "`nTesting homepage '/'..."
$response = Invoke-WebRequest -Uri "http://127.0.0.1:5000" -Method GET
Write-Host "Response:"
Write-Host $response.Content

# 6️⃣ Test mark_attendance endpoint
Write-Host "`nTesting '/mark_attendance'..."
$attendanceData = @{
    student_id = 3
    course_id  = 101
    status     = "present"
} | ConvertTo-Json

$response = Invoke-WebRequest -Uri "http://127.0.0.1:5000/mark_attendance" `
    -Method POST `
    -Body $attendanceData `
    -ContentType "application/json"

Write-Host "Response:"
Write-Host $response.Content

# 7️⃣ Test list attendance
Write-Host "`nTesting '/attendance'..."
$response = Invoke-WebRequest -Uri "http://127.0.0.1:5000/attendance" -Method GET
Write-Host "Response:"
Write-Host $response.Content

Write-Host "`n✅ All tests executed. Check Flask console for server logs."
