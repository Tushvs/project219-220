
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TutorSphere | Learn Without Limits</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #10b981;
            --accent: #f59e0b;
        }

        body {
        background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-x: hidden;
        margin: 0; /* Add this to remove default margin */
        padding: 20px; /* Add some padding for mobile devices */
    }

    .form-container {
        position: relative;
        width: 100%;
        display: flex; /* Add this */
        justify-content: center; /* Add this */
        align-items: center; /* Add this */
    }

        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 380px;
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
        }

        .input-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s;
        }

        .input-box input,
        .input-box select {
            width: 100%;
            padding: 12px 20px 12px 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        .input-box input:focus,
        .input-box select:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .input-box input:focus+i {
            color: var(--primary);
        }

        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .text-link {
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s;
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin: 5px 0;
        }

        .text-link:hover {
            color: white;
            text-decoration: underline;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(to right, #4f46e5, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
            text-align: center;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-switch {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .form-container {
            position: relative;
            width: 100%;
        }

        .form-content {
            transition: all 0.4s ease;
        }

        .hidden-form {
            opacity: 0;
            pointer-events: none;
            transform: translateY(20px);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .active-form {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s;
        }

        .social-btn:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.2);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .divider::before {
            margin-right: 10px;
        }

        .divider::after {
            margin-left: 10px;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="particles" id="particles"></div>

    <div class="form-container">
        <!-- LOGIN FORM -->
        <div class="form-content active-form" id="login-container">
            <div class="glass-card floating">
                <div class="logo">TutorSphere</div>
                <div class="subtitle">Unlock your learning potential</div>
                
                <form method="post" action="process.php">
                    <input type="hidden" name="action" value="login">
                    <div class="input-box">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="login-email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="login-pass" placeholder="Enter your password" required>
                    </div>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="text-red-500 text-sm mb-3 text-center"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    <button type="submit" name="login" class="btn-primary">Login</button>
                    <a href="#" class="text-link" onclick="showRegister(); return false;">Don't have an account? Register</a>
                </form>
            </div>
        </div>

        <!-- REGISTER FORM -->
        <div class="form-content hidden-form" id="register-container">
            <div class="glass-card floating">
                <div class="logo">TutorSphere</div>
                <div class="subtitle">Start your learning journey</div>
                
                <form method="post" action="process.php">
                    <input type="hidden" name="action" value="register">
                    <div class="input-box">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" id="Name" placeholder="Enter your full name" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="register-email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="register-pass" placeholder="Create password" required>
                    </div>
                    <button type="submit" name="signup" class="btn-primary">Register</button>
                    <a href="#" class="text-link" onclick="showLogin(); return false;">Already have an account? Login</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form switching functionality
        function showRegister() {
            document.getElementById('login-container').classList.remove('active-form');
            document.getElementById('login-container').classList.add('hidden-form');
            document.getElementById('register-container').classList.remove('hidden-form');
            document.getElementById('register-container').classList.add('active-form');
        }

        function showLogin() {
            document.getElementById('register-container').classList.remove('active-form');
            document.getElementById('register-container').classList.add('hidden-form');
            document.getElementById('login-container').classList.remove('hidden-form');
            document.getElementById('login-container').classList.add('active-form');
        }

        // Account type selection
        document.getElementById('logingtype').addEventListener('change', function() {
            var logingtype = this.value;
            var courseSelection = document.getElementById('course-selection');

            if (logingtype === 'Student') {
                courseSelection.classList.remove('hidden');
            } else {
                courseSelection.classList.add('hidden');
            }
        });

        // Particles background
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random size between 2px and 6px
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random animation
                const duration = Math.random() * 20 + 10;
                particle.style.animation = `float ${duration}s ease-in-out infinite`;
                
                particlesContainer.appendChild(particle);
            }
        });
    </script>
</body>

</html>