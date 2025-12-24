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
        $password = $_POST['password'];// Added course variable

        $checkemail = "SELECT * FROM userdata WHERE email='$email'";
        $result = $con->query($checkemail);
        
        if ($result->num_rows > 0) {
            header("Location: mainlogin.php?error=Email Address Already Exists");
            exit();
        } else {
            $insert = "INSERT INTO userdata(Name, email, password, course, Type) VALUES ('$firstName', '$email', '$password', '$course', 'Student')";
            
            if ($con->query($insert)) {
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
        
        $sql = "SELECT * FROM userdata WHERE email='$email' AND password='$password'";
        $result = $con->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_name'] = $row['Name'];
            header("Location: home.php");
            exit();
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