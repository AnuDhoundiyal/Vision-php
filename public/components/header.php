<header class="bg-white dark:bg-gray-800 shadow-md transition-all duration-300">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    
    <!-- Logo (Left) -->
    <div class="flex-shrink-0">
      <a href="index.php" class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white">
        VisionNex <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">ERA</span>
      </a>
    </div>

    <!-- Nav Menu (Center) -->
    <nav class="hidden md:flex space-x-6 items-center" id="nav-menu">
      <a href="index.php" class="text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'font-medium text-primary dark:text-white' : ''; ?>">Home</a>
      
      <!-- Features Dropdown -->
      <div class="relative group">
        <a href="features.php" class="text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'features.php') ? 'font-medium text-primary dark:text-white' : ''; ?>">Features</a>
        <div class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden group-hover:block z-10">
          <div class="py-4 flex flex-col">
            <a href="features.php#face-attendance" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">AI Face Attendance System</a>
            <a href="features.php#human-liveness" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
              Human Liveness <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Coming Soon</span>
            </a>
            <a href="features.php#crowd-analysis" class="block px-6 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
              Crowd Analysis <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Coming Soon</span>
            </a>
          </div>
        </div>
      </div>

      <a href="demo.php" class="text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'demo.php') ? 'font-medium text-primary dark:text-white' : ''; ?>">Demo</a>
      <a href="kiosk.php" class="text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'kiosk.php') ? 'font-medium text-primary dark:text-white' : ''; ?>">Kiosk</a>
      <a href="about.php" class="text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'font-medium text-primary dark:text-white' : ''; ?>">About Us</a>
      <a href="contact.php" class="text-gray-700 hover:text-primary dark:text-gray-300 dark:hover:text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'font-medium text-primary dark:text-white' : ''; ?>">Contact</a>
    </nav>

    <!-- Right Side (Theme Toggle + Auth Buttons) -->
    <div class="hidden md:flex items-center space-x-4">
      <!-- Theme Toggle -->
      <button id="theme-toggle" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none" aria-label="Toggle dark mode">
        <i class="fas fa-moon dark-icon"></i>
        <i class="fas fa-sun light-icon" style="display: none;"></i>
      </button>

      <!-- Auth Buttons -->
      <a href="login.php" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-100 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">Login</a>
      <a href="register.php" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary-dark transition-colors">Get Started</a>
    </div>

    <!-- Mobile Menu Button -->
    <button id="mobile-menu-toggle" class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none" aria-label="Toggle mobile menu">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <!-- Mobile Nav -->
  <nav id="mobile-nav" class="hidden flex-col space-y-2 px-4 pb-4 md:hidden bg-white dark:bg-gray-800 shadow-lg">
    <a href="index.php" class="block py-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-primary font-medium' : 'text-gray-700 dark:text-gray-300'; ?>">Home</a>
    <a href="features.php" class="block py-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'features.php') ? 'text-primary font-medium' : 'text-gray-700 dark:text-gray-300'; ?>">Features</a>
    <a href="demo.php" class="block py-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'demo.php') ? 'text-primary font-medium' : 'text-gray-700 dark:text-gray-300'; ?>">Demo</a>
    <a href="kiosk.php" class="block py-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'kiosk.php') ? 'text-primary font-medium' : 'text-gray-700 dark:text-gray-300'; ?>">Kiosk</a>
    <a href="about.php" class="block py-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'text-primary font-medium' : 'text-gray-700 dark:text-gray-300'; ?>">About Us</a>
    <a href="contact.php" class="block py-2 <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'text-primary font-medium' : 'text-gray-700 dark:text-gray-300'; ?>">Contact</a>
    
    <!-- Mobile Theme Toggle -->
    <button id="theme-toggle-mobile" class="p-2 rounded-md text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none w-fit" aria-label="Toggle dark mode">
      <i class="fas fa-moon dark-icon"></i>
      <i class="fas fa-sun light-icon" style="display: none;"></i>
    </button>

    <!-- Mobile Auth Buttons -->
    <a href="login.php" class="block px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-100 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">Login</a>
    <a href="register.php" class="block px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary-dark transition-colors">Get Started</a>
  </nav>
</header>

<!-- Include theme and animation CSS -->
<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/animations.css">

<script>
  // Mobile nav toggle
  document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
    document.getElementById('mobile-nav').classList.toggle('hidden');
  });
  
  // Include Animation Scripts
  document.addEventListener('DOMContentLoaded', function() {
    const animationScript = document.createElement('script');
    animationScript.src = 'assets/js/animations.js';
    animationScript.defer = true;
    document.body.appendChild(animationScript);
  });

  // Dark mode toggle (desktop + mobile)
  function toggleTheme() {
    // Add transition class for smooth animation
    document.body.classList.add('theme-transition');
    
    // Toggle dark mode class
    document.body.classList.toggle('dark-mode');
    document.documentElement.classList.toggle("dark");
    
    const darkIcon = document.querySelectorAll(".dark-icon");
    const lightIcon = document.querySelectorAll(".light-icon");
    
    if (document.documentElement.classList.contains("dark")) {
      darkIcon.forEach(i => i.style.display = "none");
      lightIcon.forEach(i => i.style.display = "inline-block");
      localStorage.setItem("theme", "dark");
    } else {
      darkIcon.forEach(i => i.style.display = "inline-block");
      lightIcon.forEach(i => i.style.display = "none");
      localStorage.setItem("theme", "light");
    }
    
    // Remove transition class after animation completes
    setTimeout(() => {
      document.body.classList.remove('theme-transition');
    }, 500);
  }
  
  document.getElementById("theme-toggle").addEventListener("click", toggleTheme);
  document.getElementById("theme-toggle-mobile").addEventListener("click", toggleTheme);

  // Load saved theme
  if (localStorage.getItem("theme") === "dark") {
    document.documentElement.classList.add("dark");
    document.body.classList.add("dark-mode");
    document.querySelectorAll(".dark-icon").forEach(i => i.style.display = "none");
    document.querySelectorAll(".light-icon").forEach(i => i.style.display = "inline-block");
  } else if (localStorage.getItem("theme") === "light") {
    document.documentElement.classList.remove("dark");
    document.body.classList.remove("dark-mode");
  } else {
    // Check system preference
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (prefersDark) {
      document.documentElement.classList.add("dark");
      document.body.classList.add("dark-mode");
      document.querySelectorAll(".dark-icon").forEach(i => i.style.display = "none");
      document.querySelectorAll(".light-icon").forEach(i => i.style.display = "inline-block");
      localStorage.setItem("theme", "dark");
    }
  }
</script>
