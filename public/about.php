<?php
require_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - VisionNex ERA</title>
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
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    <?php include 'components/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-primary to-secondary py-20 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">About VisionNex ERA</h1>
                    <p class="text-xl md:text-2xl max-w-3xl mx-auto">Pioneering the future of AI-powered vision solutions for businesses and organizations.</p>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="py-16 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Our Story</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="space-y-4 text-gray-700 dark:text-gray-300">
                        <p>VisionNex ERA was founded in 2023 with a clear mission: to revolutionize how organizations leverage computer vision and AI technologies to enhance security, efficiency, and user experience.</p>
                        <p>Our team of AI specialists, computer vision experts, and software engineers work tirelessly to develop cutting-edge solutions that are not only powerful but also accessible and easy to implement.</p>
                    </div>
                    <div class="rounded-xl overflow-hidden shadow-lg">
                        <img src="assets/images/hero-image.svg" alt="VisionNex ERA Team" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Mission Section -->
        <section class="py-16 px-4 bg-gray-100 dark:bg-gray-800">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Our Mission</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-8 transform transition-transform hover:scale-105">
                        <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-eye text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3">Innovation</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-center">Continuously pushing the boundaries of what's possible with computer vision and AI technologies.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-8 transform transition-transform hover:scale-105">
                        <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-shield-alt text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3">Security</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-center">Ensuring the highest standards of data protection and privacy in all our solutions.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-8 transform transition-transform hover:scale-105">
                        <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-users text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-center mb-3">Accessibility</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-center">Making advanced AI vision technology accessible to organizations of all sizes.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="py-16 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Our Leadership Team</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden transition-transform hover:scale-105">
                        <div class="h-48 overflow-hidden">
                            <img src="assets/images/testimonial-1.svg" alt="Team Member" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-1">Alex Johnson</h3>
                            <p class="text-primary font-medium">CEO & Founder</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden transition-transform hover:scale-105">
                        <div class="h-48 overflow-hidden">
                            <img src="assets/images/testimonial-2.svg" alt="Team Member" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-1">Sarah Chen</h3>
                            <p class="text-primary font-medium">CTO</p>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden transition-transform hover:scale-105">
                        <div class="h-48 overflow-hidden">
                            <img src="assets/images/testimonial-3.svg" alt="Team Member" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-1">Michael Rodriguez</h3>
                            <p class="text-primary font-medium">Head of AI Research</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 px-4 bg-gradient-to-r from-primary to-secondary text-white">
            <div class="container mx-auto max-w-4xl text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to transform your organization with AI vision?</h2>
                <p class="text-xl mb-8">Get in touch with our team to learn how VisionNex ERA can help you implement cutting-edge AI solutions.</p>
                <a href="contact.php" class="px-8 py-3 bg-white text-primary font-medium rounded-lg hover:bg-gray-100 transition">Contact Us Today</a>
            </div>
        </section>
    </main>

    <?php include 'components/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>