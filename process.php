<?php
session_start();
 
$host = "localhost";
$user = "root";
$pass = "";
$db = "logindata";
$con = new mysqli($host, $user, $pass, $db);
 
if ($con->connect_error) {
    die("Failed to connect to Database: " . $con->connect_error);
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        // Registration handling
        $firstName = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $course = $_POST['course'] ?? ''; // BUG FIX: this was previously used but never read from POST
 
        // SECURITY FIX: prepared statement instead of string interpolation
        $checkStmt = $con->prepare("SELECT * FROM userdata WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
 
        if ($result->num_rows > 0) {
            header("Location: mainlogin.php?error=Email Address Already Exists");
            exit();
        } else {
            // SECURITY FIX: hash the password instead of storing it in plaintext
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
 
            $insertStmt = $con->prepare(
                "INSERT INTO userdata (Name, email, password, course, Type) VALUES (?, ?, ?, ?, 'Student')"
            );
            $insertStmt->bind_param("ssss", $firstName, $email, $hashedPassword, $course);
 
            if ($insertStmt->execute()) {
                header("Location: mainlogin.php?success=Registration successful. Please login.");
                exit();
            } else {
                header("Location: mainlogin.php?error=Registration failed: " . $con->error);
                exit();
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'login') {
        // Login handling
        $email = $_POST['email'];
        $password = $_POST['password'];
 
        // SECURITY FIX: prepared statement, look up by email only, then verify hash in PHP
        $stmt = $con->prepare("SELECT * FROM userdata WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
 
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
 
            // SECURITY FIX: verify against hashed password instead of plaintext comparison
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_name'] = $row['Name'];
                header("Location: home.php");
                exit();
            } else {
                header('Location: mainlogin.php?error=Incorrect email or password');
                exit();
            }
        } else {
            header('Location: mainlogin.php?error=Incorrect email or password');
            exit();
        }
    }
}
 
// Logout handling
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: mainlogin.php");
    exit();
}
 
$con->close();
?>
 
