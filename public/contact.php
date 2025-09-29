<?php
require_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - VisionNex ERA</title>
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
        <!-- Contact Hero Section -->
        <section class="bg-gradient-to-r from-primary to-secondary py-20 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Us</h1>
                    <p class="text-xl md:text-2xl max-w-3xl mx-auto">Get in touch with our team to learn more about VisionNex ERA</p>
                </div>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="py-16 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="grid md:grid-cols-2 gap-12">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md">
                        <h2 class="text-2xl font-bold mb-6">Get in Touch</h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-8">Have questions about our AI facial recognition solutions? Our team is here to help you find the right solution for your organization.</p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold mb-2">Visit Us</h3>
                                    <p class="text-gray-600 dark:text-gray-300">123 AI Innovation Center<br>Tech District, San Francisco, CA 94105</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold mb-2">Email Us</h3>
                                    <p class="text-gray-600 dark:text-gray-300">info@visionnexera.com<br>support@visionnexera.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone-alt text-primary text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold mb-2">Call Us</h3>
                                    <p class="text-gray-600 dark:text-gray-300">+1 (555) 123-4567<br>Mon-Fri, 9am-5pm PST</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-12">
                            <h3 class="text-lg font-semibold mb-4">Connect With Us</h3>
                            <div class="flex space-x-4">
                                <a href="#" aria-label="LinkedIn" class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                                <a href="#" aria-label="Twitter" class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" aria-label="Facebook" class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="#" aria-label="Instagram" class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md">
                        <h2 class="text-2xl font-bold mb-6">Send Us a Message</h2>
                        <form action="#" method="post" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium mb-2">Full Name</label>
                                <input type="text" id="name" name="name" required 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                                <input type="email" id="email" name="email" required 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="company" class="block text-sm font-medium mb-2">Company Name</label>
                                    <input type="text" id="company" name="company" 
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700">
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium mb-2">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" 
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700">
                                </div>
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium mb-2">Subject</label>
                                <select id="subject" name="subject" required 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700">
                                    <option value="" disabled selected>Select a subject</option>
                                    <option value="sales">Sales Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="demo">Request a Demo</option>
                                    <option value="partnership">Partnership Opportunity</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium mb-2">Message</label>
                                <textarea id="message" name="message" rows="5" required 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white dark:bg-gray-700"></textarea>
                            </div>
                            
                            <div class="flex items-start">
                                <input type="checkbox" id="privacy" name="privacy" required 
                                    class="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="privacy" class="ml-2 block text-sm">
                                    I agree to the <a href="#" class="text-primary hover:underline">Privacy Policy</a> and consent to being contacted regarding my inquiry.
                                </label>
                            </div>
                            
                            <button type="submit" 
                                class="w-full py-3 px-6 bg-primary hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="py-12 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="rounded-xl overflow-hidden shadow-lg">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0968870204824!2d-122.39568308439042!3d37.78289997975805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085807ded297e89%3A0x9eb37fccf5e78b7!2sSan%20Francisco%2C%20CA%2094105!5e0!3m2!1sen!2sus!4v1625612000000!5m2!1sen!2sus" 
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-16 px-4 bg-gray-100 dark:bg-gray-800">
            <div class="container mx-auto max-w-4xl">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold mb-4">Frequently Asked Questions</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Find quick answers to common questions about VisionNex ERA</p>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" 
                                onclick="toggleFAQ(this)">
                            <span>How secure is the facial recognition data?</span>
                            <i class="fas fa-plus transform transition-transform"></i>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-300">VisionNex ERA employs end-to-end encryption and follows strict data protection protocols. All facial data is securely stored and processed in compliance with global privacy regulations including GDPR and CCPA.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" 
                                onclick="toggleFAQ(this)">
                            <span>Can VisionNex ERA integrate with our existing systems?</span>
                            <i class="fas fa-plus transform transition-transform"></i>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-300">Yes, our platform is designed with flexibility in mind. We offer comprehensive APIs and integration tools that allow VisionNex ERA to seamlessly connect with your existing security systems, HR software, and access control infrastructure.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" 
                                onclick="toggleFAQ(this)">
                            <span>How accurate is the facial recognition technology?</span>
                            <i class="fas fa-plus transform transition-transform"></i>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-300">Our advanced AI algorithms achieve over 99.8% accuracy in controlled environments and over 97% accuracy in challenging conditions. The system continuously improves through machine learning to enhance recognition capabilities over time.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" 
                                onclick="toggleFAQ(this)">
                            <span>What kind of support do you offer after implementation?</span>
                            <i class="fas fa-plus transform transition-transform"></i>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-300">We provide comprehensive support including 24/7 technical assistance, regular software updates, and dedicated account management. Our support packages can be tailored to your organization's specific needs and usage levels.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" 
                                onclick="toggleFAQ(this)">
                            <span>How long does implementation typically take?</span>
                            <i class="fas fa-plus transform transition-transform"></i>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-300">Implementation timelines vary based on the scale and complexity of your requirements. A basic setup can be operational within 1-2 weeks, while enterprise-wide deployments typically take 4-8 weeks, including system integration, testing, and staff training.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'components/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
    <script>
        function toggleFAQ(element) {
            const content = element.nextElementSibling;
            const icon = element.querySelector('i');
            
            // Toggle content visibility
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
</body>
</html>