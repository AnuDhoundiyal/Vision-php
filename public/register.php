```php
<?php
// Public registration is disabled. Users are created by administrators.
header("Location: login.php");
exit;

// The original content of register.php is commented out below for reference,
// but it will not be executed due to the redirect above.

/*
require_once "config/db.php"; // include DB connection

// ✅ Success UI function
function showSuccess() {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Registration Successful</title>
        <script src='https://cdn.tailwindcss.com'></script>
        <meta http-equiv='refresh' content='5;url=login.php'> <!-- Auto redirect in 5s -->
    </head>
    <body class='min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900'>
        <div class='bg-white dark:bg-gray-800 shadow-2xl rounded-2xl p-10 text-center max-w-md w-full'>
            <div class='flex justify-center mb-6'>
                <div class='w-16 h-16 rounded-full bg-green-100 flex items-center justify-center'>
                    <svg class='w-10 h-10 text-green-600' fill='none' stroke='currentColor' stroke-width='2' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7' />
                    </svg>
                </div>
            </div>
            <h2 class='text-2xl font-bold text-gray-900 dark:text-white mb-2'>Registration Successful!</h2>
            <p class='text-gray-600 dark:text-gray-300 mb-6'>
                Your account has been created successfully. Redirecting you to login...
            </p>
            <a href='login.php' class='inline-block px-6 py-3 bg-gradient-to-r from-blue-500 to-green-500 text-white font-medium rounded-lg shadow-lg hover:from-blue-600 hover:to-green-600 transition'>
                Go to Login
            </a>
        </div>
    </body>
    </html>
    ";
    exit;
}

// ✅ Error UI function
function showError($msg) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900'>
        <div class='bg-red-50 border border-red-400 text-red-700 px-6 py-4 rounded-2xl shadow-md max-w-md w-full text-center'>
            <h2 class='text-xl font-bold mb-2'>Error</h2>
            <p class='mb-4'>$msg</p>
            <a href='register.php' class='inline-block px-5 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition'>Go Back</a>
        </div>
    </body>
    </html>
    ";
    exit;
}

// ✅ Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name         = trim($_POST['name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($organization) || empty($password) || empty($confirm)) {
        showError("⚠ All required fields must be filled!");
    }
    if ($password !== $confirm) {
        showError("⚠ Passwords do not match!");
    }
    if (strlen($password) < 6) {
        showError("⚠ Password must be at least 6 characters!");
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // check email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) showError("Database error: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        showError("⚠ Email already registered!");
    }
    $stmt->close();

    // ✅ correct variables & role
    $role = 'admin';  // or 'admin' if you are creating admin manually

    $stmt = $conn->prepare(
        "INSERT INTO users (full_name, email, phone, organization, password, role)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) showError("Prepare failed: " . $conn->error);

    $stmt->bind_param(
        "ssssss",
        $name,          // use $name here
        $email,
        $phone,
        $organization,
        $hashedPassword,
        $role
    );

    if ($stmt->execute()) {
        showSuccess();
    } else {
        showError("❌ Error: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();
}
*/
?>
```