<?php
session_start();
require_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - VisionNex ERA</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        dark: '#1E293B',
                        light: '#F8FAFC'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-500">
    <?php include_once 'components/header.php'; ?>

    <main>
        <!-- Aurora Gradient Hero -->
        <!-- Hero Section -->
<section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden">
  <!-- Light mode background -->
  <div 
    class="absolute inset-0 z-0 block dark:hidden" 
    style="
      background-color:#f5f5dc;
      background-image:
        radial-gradient(circle at 20% 80%, rgba(120,119,198,0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.5) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120,119,198,0.1) 0%, transparent 50%);
    ">
  </div>

  <!-- Dark mode background -->
  <div 
    class="absolute inset-0 z-0 hidden dark:block" 
    style="
      background:
        radial-gradient(ellipse 120% 80% at 70% 20%, rgba(252, 174, 216, 0.15), transparent 50%),
        radial-gradient(ellipse 100% 60% at 30% 10%, rgba(173, 248, 248, 0.12), transparent 60%),
        radial-gradient(ellipse 90% 70% at 50% 0%, rgba(138,43,226,0.18), transparent 65%),
        radial-gradient(ellipse 110% 50% at 80% 30%, rgba(255,215,0,0.08), transparent 40%),
        #000000;
    ">
  </div>

  <!-- Hero Content -->
  <div class="relative z-10 container mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-6xl font-bold mb-6 text-gray-900 dark:text-white">
      The AI platform for 
      <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-green-400">
        intelligent environments
      </span>
    </h1>
    <p class="text-xl md:text-2xl max-w-3xl mx-auto mb-8 text-gray-700 dark:text-gray-300">
      VisionNex transforms educational and office spaces with face-based attendance, smart dashboards, and real-time analytics.
    </p>
    <div class="flex flex-wrap justify-center gap-4">
      <a href="register.php" class="px-6 py-3 rounded-lg font-medium text-white bg-gradient-to-r from-blue-500 to-green-400 hover:from-green-400 hover:to-blue-500 transition">
        Get started
      </a>
      <a href="demo.php" class="px-6 py-3 rounded-lg font-medium border border-gray-800 text-gray-900 hover:bg-gray-900 hover:text-white dark:border-gray-400 dark:text-white dark:hover:bg-white dark:hover:text-gray-900 transition">
        Request demo
      </a>
    </div>
  </div>
</section>


        <!-- Features Section (kept same) -->
        <section class="py-16 px-4">
            <div class="container mx-auto max-w-6xl">
                <!-- ... your existing tabbed features code stays unchanged ... -->
                <!-- Feature Categories Section -->
                <section class="py-16 px-4">
                    <div class="container mx-auto max-w-6xl">
                        <div class="text-center mb-12">
                            <h2 class="text-3xl font-bold mb-4">Our Core Technologies</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">Explore our comprehensive suite of AI-powered solutions</p>
                        </div>
                        <div class="mb-8">
                            <div class="flex flex-wrap justify-center gap-4 mb-8"> <button class="feature-tab-button active px-6 py-3 rounded-full bg-primary text-white font-medium hover:bg-blue-600 transition" data-tab="face-recognition">Face Recognition</button> <button class="feature-tab-button px-6 py-3 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition" data-tab="liveness-detection">Liveness Detection</button> <button class="feature-tab-button px-6 py-3 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition" data-tab="crowd-analysis">Crowd Analysis</button> </div>
                            <div class="feature-tab-content"> <!-- Face Recognition Tab -->
                                <div class="feature-tab-pane active" id="face-recognition">
                                    <div class="grid md:grid-cols-2 gap-8 items-center">
                                        <div class="rounded-xl overflow-hidden shadow-lg"> <img src="assets/images/face-attendance.svg" alt="Face Recognition Technology" class="w-full h-auto"> </div>
                                        <div>
                                            <h3 class="text-2xl font-bold mb-4">AI Face Attendance System</h3>
                                            <p class="text-gray-600 dark:text-gray-400 mb-6">Our AI-powered face attendance system revolutionizes how organizations track attendance, providing seamless, contactless, and accurate identification.</p>
                                            <ul class="space-y-6">
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Contactless attendance tracking</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">99.9% accuracy in face recognition</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Real-time reporting and analytics</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Integration with existing HR systems</p>
                                                    </div>
                                                </li>
                                            </ul> <a href="contact.php" class="inline-block mt-8 px-6 py-3 bg-primary hover:bg-blue-600 text-white font-medium rounded-lg transition">Request Demo</a>
                                        </div>
                                    </div>
                                </div> <!-- Liveness Detection Tab -->
                                <div class="feature-tab-pane hidden" id="liveness-detection">
                                    <div class="grid md:grid-cols-2 gap-8 items-center">
                                        <div class="rounded-xl overflow-hidden shadow-lg"> <img src="assets/images/human-liveness.svg" alt="Liveness Detection Technology" class="w-full h-auto"> </div>
                                        <div>
                                            <h3 class="text-2xl font-bold mb-4">Human Liveness Detection <span class="ml-2 px-3 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full">Coming Soon</span></h3>
                                            <p class="text-gray-600 dark:text-gray-400 mb-6">Our advanced human liveness detection ensures that only real humans can access your systems, preventing spoofing attacks using photos, videos, or masks.</p>
                                            <ul class="space-y-6">
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Multi-factor biometric verification</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Protection against sophisticated spoofing</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Works in various lighting conditions</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">GDPR and privacy-compliant implementation</p>
                                                    </div>
                                                </li>
                                            </ul> <a href="#" class="inline-block mt-8 px-6 py-3 bg-secondary hover:bg-green-600 text-white font-medium rounded-lg transition">Join Waitlist</a>
                                        </div>
                                    </div>
                                </div> <!-- Crowd Analysis Tab -->
                                <div class="feature-tab-pane hidden" id="crowd-analysis">
                                    <div class="grid md:grid-cols-2 gap-8 items-center">
                                        <div class="rounded-xl overflow-hidden shadow-lg"> <img src="assets/images/crowd-analysis.svg" alt="Crowd Analysis Technology" class="w-full h-auto"> </div>
                                        <div>
                                            <h3 class="text-2xl font-bold mb-4">Crowd Analysis <span class="ml-2 px-3 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full">Coming Soon</span></h3>
                                            <p class="text-gray-600 dark:text-gray-400 mb-6">Our crowd analysis technology provides real-time insights into crowd density, movement patterns, and behavior, helping organizations optimize space usage and ensure safety.</p>
                                            <ul class="space-y-6">
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Real-time crowd density monitoring</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Movement pattern analysis</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Anomaly detection for security</p>
                                                    </div>
                                                </li>
                                                <li class="flex gap-4">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center"> <i class="fas fa-check-circle text-primary"></i> </div>
                                                    <div>
                                                        <p class="text-gray-600 dark:text-gray-400">Privacy-preserving implementation</p>
                                                    </div>
                                                </li>
                                            </ul> <a href="#" class="inline-block mt-8 px-6 py-3 bg-secondary hover:bg-green-600 text-white font-medium rounded-lg transition">Join Waitlist</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative overflow-hidden py-20">
            <!-- Light mode background -->
            <div
                class="absolute inset-0 z-0 block dark:hidden transition-colors"
                style="
          background-color:#f9fafb;
          background-image:
          repeating-linear-gradient(135deg, rgba(236, 236, 237, 0.25) 0 8px, transparent 8px 20px),
        repeating-linear-gradient(-135deg, rgba(236, 244, 240, 0.25) 0 8px, transparent 8px 20px);
        background-size: 40px 40px;
        ">
            </div>

            <!-- Dark mode background -->
            <div
                class="absolute inset-0 z-0 hidden dark:block transition-colors"
                style="
          background-color:#020617;
          background-image:
            linear-gradient(to right, rgba(71,85,105,0.15) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(71,85,105,0.15) 1px, transparent 1px),
            radial-gradient(circle at 50% 60%, rgba(236,72,153,0.15) 0%, rgba(168,85,247,0.05) 40%, transparent 70%);
          background-size: 40px 40px, 40px 40px, 100% 100%;
        ">
            </div>

            <!-- CTA content -->
            <div class="relative z-10 container mx-auto max-w-4xl text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 dark:text-white">
                    Ready to transform your business with AI vision?
                </h2>
                <p class="text-xl mb-8 text-gray-700 dark:text-gray-300">
                    Join thousands of businesses already using VisionNex ERA to improve security, efficiency, and insights.
                </p>
                <a href="register.php"
                    class="px-8 py-3 rounded-lg font-medium text-white bg-gradient-to-r from-blue-500 to-green-400 hover:from-green-400 hover:to-blue-500 transition">
                    Get Started Today
                </a>
            </div>
        </section>
    </main>

    <?php include_once 'components/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        // Tab functionality (same as before)
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.feature-tab-button');
            const tabPanes = document.querySelectorAll('.feature-tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
                    });
                    this.classList.add('active', 'bg-primary', 'text-white');
                    this.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
                    tabPanes.forEach(pane => pane.classList.add('hidden'));
                    document.getElementById(this.dataset.tab).classList.remove('hidden');
                });
            });
        });
    </script>
</body>

</html>