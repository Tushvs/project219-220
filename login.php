<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard();
}

// Initialize variables
$error = '';
$csrf_token = generateCsrfToken();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (empty($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error = "Invalid form submission";
    } else {
        // Rate limiting check
        $rateKey = 'login_attempt_' . $_SERVER['REMOTE_ADDR'];
        if (isRateLimited($rateKey)) {
            $error = "Too many login attempts. Please try again later.";
        } else {
            // Sanitize inputs
            $username = sanitizeInput($_POST['username']);
            $password = $_POST['password']; // Don't sanitize password
            $user_type = isset($_POST['user_type']) ? sanitizeInput($_POST['user_type']) : 'student';
            
            // Validate user type
            $valid_types = ['student', 'teacher', 'parent'];
            if (!in_array($user_type, $valid_types)) {
                $error = "Invalid user type";
            } else {
                $table = $user_type; // Now using plural table names (students, teachers, parents)
                $id_field = $user_type . '_id';
                
                try {
                    // Prepare and execute query
                    $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
                    $stmt->bind_param("s", $username);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Login query failed: " . $stmt->error);
                    }
                    
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        
                        // Verify password
                        if (password_verify($password, $user['password'])) {
                            // Successful login - set session variables
                            $_SESSION['user_id'] = $user[$id_field];
                            $_SESSION['user_type'] = $user_type;
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                            $_SESSION['last_activity'] = time();
                            
                            // For parents, store linked student ID
                            if ($user_type === 'parent') {
                                $student = getLinkedStudent($user[$id_field]);
                                if ($student) {
                                    $_SESSION['student_id'] = $student['student_id'];
                                }
                            }
                        
                            // Regenerate session ID to prevent fixation
                            session_regenerate_id(true);
                            
                            // Clear any rate limits
                            unset($_SESSION['rate_limits']['login_attempt_' . $_SERVER['REMOTE_ADDR']]);
                            
                            // Redirect to dashboard or original URL
                            if (isset($_SESSION['redirect_url'])) {
                                $redirect = $_SESSION['redirect_url'];
                                unset($_SESSION['redirect_url']);
                                header("Location: $redirect");
                                exit();
                            } else {
                                redirectToDashboard();
                            }
                        } else {
                            // Password verification failed
                            $error = "Invalid username or password failed";
                        }
                    } else {
                        // User not found
                        $error = "Invalid username or password";
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $error = "System error. Please try again later.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Login</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #7209b7;
            --accent-color: #f72585;
            --error-color: #ef233c;
            --error-bg: #ffebee;
            --text-color: #2b2d42;
            --light-text: #8d99ae;
            --bg-color: #f8f9fa;
            --white: #ffffff;
            --border-color: #e9ecef;
            --border-radius: 8px;
            --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 420px;
            transform: perspective(1000px);
            animation: fadeInUp 0.6s cubic-bezier(0.39, 0.575, 0.565, 1) both;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            animation: rainbow 8s linear infinite;
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(40px) perspective(1000px) rotateX(10deg);
            }
            to { 
                opacity: 1;
                transform: translateY(0) perspective(1000px) rotateX(0);
            }
        }
        
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 3px;
        }
        
        /* NEW ERROR MESSAGE DESIGN */
        .error-message {
            display: flex;
            align-items: center;
            background: var(--error-bg);
            color: var(--error-color);
            padding: 1rem 1.25rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transform: translateY(-20px);
            opacity: 0;
            box-shadow: 0 2px 8px rgba(239, 35, 60, 0.1);
        }
        
        .error-message::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--error-color);
        }
        
        .error-icon {
            margin-right: 12px;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .error-content {
            flex: 1;
        }
        
        .error-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .error-text {
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        @keyframes slideIn {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        
        .error-message.shake {
            animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards, 
                       shake 0.5s cubic-bezier(.36,.07,.19,.97) 0.4s;
        }
        
        /* User Type Selector Styles */
        .user-type-selector {
            margin-bottom: 2rem;
            position: relative;
        }
        
        .selector-options {
            display: flex;
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 5px;
            position: relative;
        }
        
        .selector-options::after {
            content: '';
            position: absolute;
            top: 5px;
            left: 0;
            width: 33.33%;
            height: calc(100% - 10px);
            background: var(--white);
            border-radius: calc(var(--border-radius) - 5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            z-index: 0;
        }
        
        .selector-options[data-selected="1"]::after {
            transform: translateX(0);
        }
        
        .selector-options[data-selected="2"]::after {
            transform: translateX(100%);
        }
        
        .selector-options[data-selected="3"]::after {
            transform: translateX(200%);
        }
        
        .option {
            flex: 1;
            text-align: center;
            padding: 12px 5px;
            cursor: pointer;
            position: relative;
            z-index: 1;
            transition: var(--transition);
            font-weight: 500;
            color: var(--light-text);
        }
        
        .option i {
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
            transition: var(--transition);
        }
        
        .selector-options[data-selected="1"] .option:nth-child(1),
        .selector-options[data-selected="2"] .option:nth-child(2),
        .selector-options[data-selected="3"] .option:nth-child(3) {
            color: var(--primary-color);
        }
        
        .option:hover {
            color: var(--primary-color);
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light-text);
            font-weight: 500;
            transition: var(--transition);
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background-color: #f8f9fa;
        }
        
        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .forgot-password a {
            color: var(--light-text);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .option i {
                font-size: 20px;
            }
            
            .error-message {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .error-icon {
                margin-right: 0;
                margin-bottom: 8px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <h2>Dashboard Login</h2>
        
        <?php if ($error): ?>
            <div class="error-message shake">
                <div class="error-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="error-content">
                    <div class="error-title">Login Error</div>
                    <div class="error-text"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="user-type-selector">
                <div class="selector-options" data-selected="1">
                    <label class="option">
                        <input type="radio" name="user_type" value="student" checked>
                        <i class="fas fa-user-graduate"></i>
                        Student
                    </label>
                    <label class="option">
                        <input type="radio" name="user_type" value="teacher">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Teacher
                    </label>
                    <label class="option">
                        <input type="radio" name="user_type" value="parent">
                        <i class="fas fa-user-friends"></i>
                        Parent
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">
                <span>Login</span>
            </button>
        </form>
    </div>

    <script>
        // Add interactivity to the selector
        document.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function() {
                const selector = this.closest('.selector-options');
                const options = selector.querySelectorAll('.option');
                const index = Array.from(options).indexOf(this) + 1;
                selector.setAttribute('data-selected', index);
                
                // Update the radio button state
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
    </script>
</body>
</html>