<?php
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
?>
<!-- Form Html  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - VisionNex ERA</title>

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
                linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(16, 185, 129, 0.2));
            filter: blur(12px);
            z-index: -1;
        }
    </style>
</head>

<body class="min-h-screen  flex items-center justify-center py-12 px-6 sm:px-6 lg:px-8 bg-gray-100 dark:bg-gray-900 font-sans">

    <!-- Card Container -->
    <div class="w-full max-w-5xl flex rounded-2xl shadow-2xl overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-md">

        <!-- Left: Branding -->
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

        <!-- Right: Registration Form -->
        
        <div class="w-1/2 p-10 flex flex-col justify-center">
             <form action="register.php" method="POST" class="space-y-6">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    Create Your Account
                </h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Join VisionNex ERA today</p>
            </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                    <input id="name" name="name" type="text" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm hover:shadow-md
                   focus:outline-none focus:ring-primary focus:border-primary sm:text-sm 
                   dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="email" name="email" type="email" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm hover:shadow-md
                   focus:outline-none focus:ring-primary focus:border-primary sm:text-sm 
                   dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone (Optional)</label>
                    <input id="phone" name="phone" type="tel"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm hover:shadow-md
                   focus:outline-none focus:ring-primary focus:border-primary sm:text-sm 
                   dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <!-- Organization -->
                <div>
                    <label for="organization" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Organization</label>
                    <input id="organization" name="organization" type="text" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm hover:shadow-md
                   focus:outline-none focus:ring-primary focus:border-primary sm:text-sm 
                   dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <div class="relative mt-1">
                        <input id="password" name="password" type="password" required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm hover:shadow-md
                     focus:outline-none focus:ring-primary focus:border-primary sm:text-sm 
                     dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-10">
                        <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500" onclick="togglePassword('password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>

                    <!-- Strength bar
                    <div class="w-full h-2 mt-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="strength-bar" class="h-2 w-0 bg-red-500 transition-all duration-300"></div>
                    </div>
                    <p id="password-strength" class="mt-1 text-xs font-medium text-gray-600 dark:text-gray-300"></p>
                </div> -->
                    <!-- Strength bar -->
                    <div class="w-full h-2 mt-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="strength-bar" class="h-2 w-0 bg-red-500 transition-all duration-300"></div>
                    </div>
                    <p id="password-strength" class="mt-1 text-xs font-medium"></p>


                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <div class="relative mt-1">
                            <input id="confirm-password" name="confirm_password" type="password" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm hover:shadow-md
                     focus:outline-none focus:ring-primary focus:border-primary sm:text-sm 
                     dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-10">
                            <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-500" onclick="togglePassword('confirm-password', this)">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                        <p id="confirm-error" class="mt-1 text-xs font-medium text-red-500 hidden">Passwords do not match</p>
                    </div>

                    <!-- Submit -->
                    <div class="pt-6">
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 rounded-md shadow-md  text-sm font-medium text-white 
                   bg-gradient-to-r from-primary to-secondary hover:from-blue-600 hover:to-green-600 
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Sign Up
                        </button>
                    </div>
            </form>

            <!-- Already have account -->
            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Already have an account?
                <a href="./login.php" class="text-blue-500 font-semibold hover:underline">Sign in</a>
            </div>
        </div>
        </form>
    </div>
   

    <!-- Scripts -->
    <script>
        // Toggle password visibility
        function togglePassword(id, el) {
            const input = document.getElementById(id);
            const icon = el.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Password strength checker
        const passwordInput = document.getElementById("password");
        const strengthBar = document.getElementById("strength-bar");
        const strengthText = document.getElementById("password-strength");
        const confirmPasswordInput = document.getElementById("confirm-password");
        const confirmError = document.getElementById("confirm-error");

        passwordInput.addEventListener("input", () => {
            const value = passwordInput.value;
            let strength = 0;

            if (value.length >= 6) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/\d/.test(value)) strength++;
            if (/[\W]/.test(value)) strength++;

            // Default values
            let width = (strength / 4) * 100;
            let color = "bg-red-500";
            let text = "Weak";
            let textColor = "text-red-500";

            if (strength === 2) {
                color = "bg-yellow-500";
                text = "Medium";
                textColor = "text-yellow-500";
            }
            if (strength >= 3) {
                color = "bg-green-600";
                text = "Strong";
                textColor = "text-green-600";
            }

            // Apply updates
            strengthBar.style.width = width + "%";
            strengthBar.className = `h-2 transition-all duration-300 ${color}`;
            strengthText.textContent = "Password Strength: " + text;
            strengthText.className = `mt-1 text-xs font-medium ${textColor}`;
        });

        // Confirm password match
        confirmPasswordInput.addEventListener("input", () => {
            if (confirmPasswordInput.value !== passwordInput.value) {
                confirmError.classList.remove("hidden");
            } else {
                confirmError.classList.add("hidden");
            }
        });
    </script>
</body>

</html>
</body>
</html>
