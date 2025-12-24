<?php
// =============================================
// SECURITY CONFIGURATION
// =============================================

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Set default timezone
date_default_timezone_set('UTC');

// =============================================
// DATABASE CONFIGURATION
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_portal');
define('DB_PORT', 3306);

// Create database connection with error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset and collation
    $conn->set_charset("utf8mb4");
    $conn->query("SET collation_connection = utf8mb4_unicode_ci");
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die("System maintenance in progress. Please try again later.");
}

// =============================================
// SESSION CONFIGURATION
// =============================================

session_name('SecureStudentPortal');
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Enable strict session mode
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.sid_length', 128);
ini_set('session.sid_bits_per_character', 6);

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// =============================================
// SECURITY FUNCTIONS
// =============================================

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Password hashing function
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// =============================================
// AUTHENTICATION FUNCTIONS
// =============================================

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id'], $_SESSION['user_type'], $_SESSION['username'], $_SESSION['ip_address']) 
           && $_SESSION['ip_address'] === $_SERVER['REMOTE_ADDR']
           && $_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT'];
}

/**
 * Check if user is a teacher
 */
function isTeacher() {
    return isLoggedIn() && $_SESSION['user_type'] === 'teacher';
}

/**
 * Check if user is a parent
 */
function isParent() {
    return isLoggedIn() && $_SESSION['user_type'] === 'parent';
}

/**
 * Check if user is a student
 */
function isStudent() {
    return isLoggedIn() && $_SESSION['user_type'] === 'student';
}

/**
 * Check if user is logged in, otherwise redirect to login page
 */
function checkLogin() {
    if (!isLoggedIn()) {
        // Store the current URL for redirecting back after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        
        // Clear any sensitive session data
        unset($_SESSION['user_id']);
        unset($_SESSION['user_type']);
        unset($_SESSION['username']);
        
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        
        // Redirect to login page
        header("Location: login.php");
        exit();
    }
    
    // Additional security checks
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
        $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        forceLogout();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Check for inactivity timeout (30 minutes)
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > 1800)) {
        forceLogout();
    }
}

/**
 * Get current user data
 */
function getUserData() {
    if (!isLoggedIn()) return null;

    global $conn;
    $user_id = $_SESSION['user_id'];
    $table = $_SESSION['user_type'];
    $id_field = $table . '_id';

    try {
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $id_field = ?");
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to get user data: " . $stmt->error);
        }

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}

/**
 * Redirect to appropriate dashboard
 */
function redirectToDashboard() {
    if (isLoggedIn()) {
        if (isTeacher()) {
            $location = 'teacher_dashboard.php';
        } elseif (isParent()) {
            $location = 'parent_dashboard.php';
        } else {
            $location = 'dashboard.php';
        }
        
        // Clear redirect URL if set
        if (isset($_SESSION['redirect_url'])) {
            unset($_SESSION['redirect_url']);
        }
        
        header("Location: $location");
        exit();
    }
}

/**
 * Get linked student for parent
 */
function getLinkedStudent($parent_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT s.* FROM students s 
                               JOIN parent_student ps ON s.student_id = ps.student_id 
                               WHERE ps.parent_id = ?");
        $stmt->bind_param("i", $parent_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to get linked student: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}

/**
 * Force logout and redirect to login page
 */
function forceLogout() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    header("Location: login.php");
    exit();
}

// =============================================
// RATE LIMITING FUNCTIONALITY
// =============================================

/**
 * Check if request is rate limited
 */
function isRateLimited($key, $limit = 5, $timeout = 300) {
    if (!isset($_SESSION['rate_limits'][$key])) {
        $_SESSION['rate_limits'][$key] = [
            'attempts' => 0,
            'last_attempt' => 0
        ];
    }
    
    $now = time();
    $rateData = &$_SESSION['rate_limits'][$key];
    
    // Reset if timeout has passed
    if ($now - $rateData['last_attempt'] > $timeout) {
        $rateData['attempts'] = 0;
        $rateData['last_attempt'] = $now;
    }
    
    $rateData['attempts']++;
    $rateData['last_attempt'] = $now;
    
    return $rateData['attempts'] > $limit;
}
?>