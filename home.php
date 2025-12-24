<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="TutorSphere - Connect with expert tutors for personalized learning experiences in various subjects.">
    <title>TutorSphere | Personalized Learning Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .nav-link:hover {
            color: #93c5fd;
        }

        .button {
            background-color: #3B82F6;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            background-color: #2563EB;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .secondary-button {
            background-color: transparent;
            border: 2px solid white;
            color: white;
        }

        .secondary-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
        }

        .subject-badge {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid #3B82F6;
        }

        .subject-badge:hover {
            transform: translateY(-8px);
        }

        .footer-link:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.05);
            border-left: 4px solid #3B82F6;
        }

        .user-email {
            color: #4f46e5;
            font-weight: 500;
            margin-right: 15px;
            font-size: 0.9rem;
        }

        .logout {
            background-color: #f43f5e;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .logout:hover {
            background-color: #e11d48;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="flex justify-between items-center p-6 text-white z-50">
        <div class="flex items-center">
            <i class="fas fa-graduation-cap text-3xl text-blue-400 mr-3"></i>
            <span class="text-2xl font-bold">Tutor<span class="text-blue-400">Sphere</span></span>
        </div>
        <div class="hidden md:flex space-x-8">
            <a href="home.php" class="nav-link font-medium">Home</a>
            <a href="findtutor.php" class="nav-link font-medium">Tutors</a>
            <a href="login.php" class="nav-link font-medium">Your Dashboard</a>
        </div>
        <div class="flex items-center space-x-4">
        
            <?php if(isset($_SESSION['user_email'])): ?>
                <span class="user-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                <a href="process.php?action=logout" class="logout">Logout</a>
            <?php else: ?>
                <a href="mainlogin.php" class="button hidden sm:inline-block">Login</a>
                <a href="mainlogin.php" class="button secondary-button hidden sm:inline-block">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="flex-grow flex items-center justify-center text-white px-4">
        <div class="text-center max-w-4xl">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">Unlock Your <span
                    class="text-blue-400">Potential</span> With Personalized Learning</h1>
            <p class="text-xl mb-10 opacity-90 leading-relaxed">Connect with expert tutors in 50+ subjects, from
                academic help to professional skills. Learn at your own pace with customized lessons tailored to your
                goals.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="findtutor.html" class="button">Find a Tutor</a>
            </div>
        </div>
    </section>

    <!-- Features Section (would appear on scroll) -->
    <section class="py-20 px-4 bg-gray-900 text-white hidden">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-16">Why Choose <span class="text-blue-400">TutorSphere</span>
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card p-8">
                    <div class="text-blue-400 text-4xl mb-4">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Expert Tutors</h3>
                    <p class="opacity-80">All tutors are vetted professionals with verified qualifications and teaching
                        experience.</p>
                </div>
                <div class="feature-card p-8">
                    <div class="text-blue-400 text-4xl mb-4">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Flexible Learning</h3>
                    <p class="opacity-80">Learn from anywhere with our online platform or choose in-person sessions in
                        your area.</p>
                </div>
                <div class="feature-card p-8">
                    <div class="text-blue-400 text-4xl mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Progress Tracking</h3>
                    <p class="opacity-80">Monitor your improvement with detailed analytics and personalized feedback.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Subjects (would appear on scroll) -->
    <section class="py-20 px-4 bg-gray-800 text-white hidden">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6">Popular <span class="text-blue-400">Subjects</span></h2>
            <p class="text-center mb-16 max-w-2xl mx-auto opacity-90">We offer tutoring in a wide range of subjects to
                meet all your learning needs.</p>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-square-root-alt mr-2"></i> Mathematics
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-flask mr-2"></i> Science
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-language mr-2"></i> Languages
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-laptop-code mr-2"></i> Programming
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-business-time mr-2"></i> Business
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-music mr-2"></i> Music
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-paint-brush mr-2"></i> Arts
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-book mr-2"></i> Test Prep
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-heartbeat mr-2"></i> Health
                </div>
                <div class="subject-badge rounded-full py-3 px-6 text-center">
                    <i class="fas fa-plus-circle mr-2"></i> And More...
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials (would appear on scroll) -->


    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 px-4">
        <div class="max-w-6xl mx-auto grid md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center mb-4">
                    <i class="fas fa-graduation-cap text-2xl text-blue-400 mr-2"></i>
                    <span class="text-xl font-bold text-white">Tutor<span class="text-blue-400">Sphere</span></span>
                </div>
                <p class="mb-4">Empowering learners and educators through personalized education.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-xl hover:text-blue-400"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-xl hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-xl hover:text-blue-400"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-xl hover:text-blue-400"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">For Students</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="footer-link hover:text-blue-400">Find Tutors</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">How It Works</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Subjects</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Pricing</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Success Stories</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">For Tutors</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="footer-link hover:text-blue-400">Become a Tutor</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Tutor Resources</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Teaching Tools</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Tutor Community</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Company</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="footer-link hover:text-blue-400">About Us</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Careers</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Blog</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Press</a></li>
                    <li><a href="#" class="footer-link hover:text-blue-400">Contact</a></li>
                </ul>
            </div>
        </div>

        <div
            class="max-w-6xl mx-auto mt-12 pt-6 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
            <p>© 2023 TutorSphere. All rights reserved.</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="#" class="footer-link hover:text-blue-400">Privacy Policy</a>
                <a href="#" class="footer-link hover:text-blue-400">Terms of Service</a>
                <a href="#" class="footer-link hover:text-blue-400">Cookie Policy</a>
            </div>
        </div>
    </footer>

    <script>
        // This would be used to reveal sections on scroll
        document.addEventListener('DOMContentLoaded', function () {
            // Simple animation - in a real app you'd use IntersectionObserver
            setTimeout(() => {
                document.querySelectorAll('section.hidden').forEach((section, index) => {
                    setTimeout(() => {
                        section.classList.remove('hidden');
                    }, index * 300);
                });
            }, 1000);
        });
    </script>
</body>

</html>