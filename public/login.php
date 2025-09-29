```php
<?php
require_once "config/config.php"; // Includes db.php and functions.php
// secure_session_start() is called in config.php

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = sanitize_input($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Basic input validation
    if (empty($email) || empty($password)) {
        $error = "⚠ Email and Password are required!";
    } else {
        // Account lockout logic (simple session-based)
        $failed_attempts_key = 'failed_attempts_' . $email;
        $last_attempt_time_key = 'last_attempt_time_' . $email;
        $lockout_duration = 300; // 5 minutes lockout
        $max_attempts = 5;

        if (isset($_SESSION[$failed_attempts_key]) && $_SESSION[$failed_attempts_key] >= $max_attempts) {
            if (isset($_SESSION[$last_attempt_time_key]) && (time() - $_SESSION[$last_attempt_time_key] < $lockout_duration)) {
                $remaining_time = $lockout_duration - (time() - $_SESSION[$last_attempt_time_key]);
                $error = "Account locked. Please try again in " . $remaining_time . " seconds.";
                log_activity(null, 'Login Attempt - Locked', "Email: $email, IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A'));
                show_toast($error, 'error');
                goto end_login_process; // Skip further processing
            } else {
                // Lockout expired, reset attempts
                $_SESSION[$failed_attempts_key] = 0;
                unset($_SESSION[$last_attempt_time_key]);
            }
        }

        $conn = $db->getConnection();
        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password, role, profile_image FROM users WHERE email = ? LIMIT 1"
        );
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    // Reset failed attempts on successful login
                    unset($_SESSION[$failed_attempts_key]);
                    unset($_SESSION[$last_attempt_time_key]);

                    // Set session variables
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['full_name'];
                    $_SESSION['email']    = $user['email'];
                    $_SESSION['role']     = $user['role'];
                    $_SESSION['profile_image'] = $user['profile_image']; // Store profile image path

                    log_activity($user['id'], 'Login Success', "User: {$user['email']}, Role: {$user['role']}");
                    show_toast('Login successful!', 'success');

                    // Redirect by role
                    switch ($user['role']) {
                        case 'admin':
                            header("Location: dashboard/admin/admin-dashboard.php");
                            break;
                        case 'teacher':
                            header("Location: dashboard/teacher/index.php");
                            break;
                        case 'student':
                            header("Location: dashboard/student/dashboard.php");
                            break;
                        default:
                            header("Location: /"); // Fallback to homepage
                            break;
                    }
                    exit;
                } else {
                    $error = "Invalid password.";
                    // Increment failed attempts
                    $_SESSION[$failed_attempts_key] = ($_SESSION[$failed_attempts_key] ?? 0) + 1;
                    $_SESSION[$last_attempt_time_key] = time();
                    log_activity(null, 'Login Attempt - Failed Password', "Email: $email, IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A'));
                }
            } else {
                $error = "User not found.";
                // Increment failed attempts even for non-existent users to prevent enumeration
                $_SESSION[$failed_attempts_key] = ($_SESSION[$failed_attempts_key] ?? 0) + 1;
                $_SESSION[$last_attempt_time_key] = time();
                log_activity(null, 'Login Attempt - User Not Found', "Email: $email, IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A'));
            }
            $stmt->close();
        } else {
            $error = "Database error during login. Please try again.";
            error_log("Login prepare statement failed: " . $conn->error);
        }
    }
    end_login_process:; // Label for goto
    show_toast($error, 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - VisionNex ERA</title>

<!-- Fonts & Icons -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: '#3B82F6',
        secondary: '#10B981',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif']
      }
    }
  }
}
</script>
<style>
body::before {
  content: "";
  position: fixed;
  inset: 0;
  background: url('assets/images/bg-pattern.jpg') center/cover no-repeat,
              linear-gradient(135deg, rgba(59,130,246,0.2), rgba(16,185,129,0.2));
  filter: blur(12px);
  z-index: -1;
}
</style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans">

<!-- Card Container -->
<div class="w-full max-w-5xl flex rounded-2xl shadow-2xl overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-md">

  <!-- Left: Login Form -->
  <div class="w-1/2 p-10 flex flex-col justify-center">
    <div class="mb-8 text-center">
      <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
        VisionNex <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">ERA</span>
      </h2>
      <p class="mt-2 text-gray-600 dark:text-gray-400">Welcome back, sign in to continue</p>
    </div>

    <!-- Login Form -->
    <form id="login-form" method="POST" action="" class="space-y-6">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
        <div class="mt-1">
          <input id="email" name="email" type="email" autocomplete="email" required
                 class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 hover:shadow-md focus:outline-none focus:ring-primary focus:border-primary sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
        <div class="mt-1">
          <input id="password" name="password" type="password" autocomplete="current-password" required
                 class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 hover:shadow-md focus:outline-none focus:ring-primary focus:border-primary sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="text-red-600 text-sm"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div>
        <button type="submit"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-primary to-secondary hover:from-primary-dark hover:to-secondary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
          Sign in
        </button>
      </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
      <p class="text-blue-400 text-sm">
        Don't have an account?
        <a href="register.php" class="text-gray-600 font-semibold hover:underline">
            Register here
        </a>
      </p>
    </div>
  </div>

  <!-- Right: Branding -->
  <div class="w-1/2 bg-gradient-to-br from-primary/90 to-secondary/90 text-white p-10 flex flex-col justify-center relative">
    <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
    <div class="relative z-10">
      <h3 class="text-3xl font-bold mb-4">About VisionNex</h3>
      <p class="text-lg leading-relaxed">
        VisionNex ERA is an AI-powered attendance and analytics platform. 
        Experience seamless face recognition, smart dashboards, and advanced 
        insights designed to make education and workplace management effortless.
      </p>
      <div class="mt-8">
        <img src="assets/images/astronaut.svg" alt="VisionNex" class="w-64 mx-auto drop-shadow-lg">
      </div>
    </div>
  </div>

</div>
<div id="toast-container"></div>
<?php display_toast_from_session(); ?>
</body>
</html>
```