<?php
require_once "config/db.php"; // DB connection
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        $error = "⚠ Email and Password are required!";
    } else {
        // column name fixed to full_name
        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // ✅ Set session variables
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['full_name'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['role']     = $user['role'];

                // ✅ Redirect by role
                if ($user['role'] === 'admin') {
                    header("Location: admin-dashboard/settings.php");
                } elseif ($user['role'] === 'teacher') {
                    header("Location: teacher-dashboard/dashboard.php");
                } else {
                    header("Location: student-dashboard/dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
    $conn->close();
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
</body>
</html>
