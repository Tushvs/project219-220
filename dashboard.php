<?php
require_once 'config.php';
checkLogin();

// Redirect teachers to their dashboard
if (isTeacher()) {
    header("Location: teacher_dashboard.php");
    exit();
}

$student = getUserData();
if (!$student) {
    header("Location: logout.php");
    exit();
}

// Set default timezone
date_default_timezone_set('America/New_York');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #f72585;
            --sidebar-width: 280px;
            --header-height: 80px;
            --card-radius: 16px;
            --transition-speed: 0.4s;
            --notification-color: #ff9e00;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #2b2d42;
            overflow-x: hidden;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Glassmorphism Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(67, 97, 238, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            position: fixed;
            height: 100vh;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border-right: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .sidebar-header {
            padding: 30px 20px;
            background: rgba(58, 12, 163, 0.3);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header img:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            animation: fadeIn 1s ease-in-out;
        }
        
        .sidebar-header p {
            margin: 8px 0 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            animation: fadeIn 1.2s ease-in-out;
        }
        
        .sidebar-menu {
            padding: 25px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            position: relative;
            margin: 5px 15px;
            border-radius: 12px;
            overflow: hidden;
            animation: slideInLeft 0.5s ease-out;
            animation-fill-mode: both;
        }
        
        .sidebar-menu li:nth-child(1) { animation-delay: 0.1s; }
        .sidebar-menu li:nth-child(2) { animation-delay: 0.2s; }
        .sidebar-menu li:nth-child(3) { animation-delay: 0.3s; }
        .sidebar-menu li:nth-child(4) { animation-delay: 0.4s; }
        .sidebar-menu li:nth-child(5) { animation-delay: 0.5s; }
        .sidebar-menu li:nth-child(6) { animation-delay: 0.6s; }
        
        .sidebar-menu li::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .sidebar-menu li:hover::before {
            left: 100%;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 12px;
            position: relative;
            z-index: 1;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-menu a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 30px;
            transition: all var(--transition-speed) ease;
        }
        
        /* Floating header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            border-radius: var(--card-radius);
            transition: all 0.3s ease;
            animation: slideDown 0.5s ease-out;
        }
        
        .header:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .header h2 {
            color: var(--secondary-color);
            font-size: 1.8rem;
            font-weight: 600;
            background: linear-gradient(to right, #4361ee, #3a0ca3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
        }
        
        .user-menu img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .user-menu img:hover {
            transform: rotate(10deg) scale(1.1);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: var(--accent-color);
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .logout-btn i {
            margin-left: 8px;
        }
        
        .logout-btn:hover {
            color: #d0006e;
            transform: translateX(3px);
        }
        
        /* Animated dashboard cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .card {
            background: white;
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-header h3 {
            font-size: 1.1rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .card-header i {
            font-size: 1.8rem;
            color: var(--primary-color);
            background: rgba(67, 97, 238, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .card:hover .card-header i {
            transform: rotate(15deg) scale(1.1);
            color: white;
            background: var(--primary-color);
        }
        
        .card-body h1 {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .card-body p {
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        /* Notification Section */
        .notification-container {
            position: relative;
            margin-bottom: 30px;
            height: 120px;
            overflow: hidden;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.8s ease-out;
        }
        
        .notification-slider {
            position: absolute;
            width: 100%;
            height: 100%;
            transition: transform 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .notification-card {
            position: absolute;
            width: 100%;
            height: 100%;
            padding: 25px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.8s ease, transform 0.8s ease;
            transform: translateY(20px);
        }
        
        .notification-card.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .notification-card.leaving {
            opacity: 0;
            transform: translateY(-20px);
        }
        
        .notification-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            background: var(--notification-color);
            font-size: 1.2rem;
            box-shadow: 0 4px 15px rgba(255, 158, 0, 0.3);
            animation: pulse 2s infinite;
        }
        
        .notification-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .notification-content {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .notification-indicators {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }
        
        .notification-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(108, 117, 125, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .notification-indicator.active {
            background: var(--notification-color);
            transform: scale(1.2);
        }
        
        /* Content sections */
        .content-section {
            background: white;
            border-radius: var(--card-radius);
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            transition: all 0.4s ease;
            animation: fadeIn 0.8s ease-out;
        }
        
        .content-section:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .section-header h3 {
            color: var(--secondary-color);
            font-size: 1.4rem;
            font-weight: 600;
            position: relative;
        }
        
        .section-header h3::after {
            content: '';
            position: absolute;
            bottom: -16px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 3px;
        }
        
        /* Table styles */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
            position: sticky;
            top: 0;
        }
        
        table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        table tr:hover td {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .badge-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        /* Profile section */
        #profile-view {
            display: flex;
            margin-bottom: 30px;
            animation: fadeIn 0.6s ease-out;
        }
        
        #profile-view img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 40px;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        #profile-view img:hover {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        #profile-view table {
            flex-grow: 1;
        }
        
        #profile-view table tr td:first-child {
            font-weight: 600;
            color: var(--secondary-color);
            width: 150px;
        }
        
        /* Edit profile form */
        #profile-edit {
            animation: fadeIn 0.6s ease-out;
        }
        
        #profile-form {
            display: flex;
            margin-bottom: 30px;
        }
        
        #profile-pic-preview {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 40px;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        #profile-pic-preview:hover {
            transform: scale(1.03);
        }
        
        #profile-pic-upload {
            margin-top: 20px;
            width: 180px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary-color);
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            transform: translateY(-2px);
        }
        
        /* Action buttons */
        .action-btn {
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .action-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from { 
                opacity: 0;
                transform: translateX(-20px);
            }
            to { 
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .sidebar-header h3, 
            .sidebar-header p,
            .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            
            .sidebar-menu a i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            
            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            #profile-view {
                flex-direction: column;
            }
            
            #profile-view img {
                margin-right: 0;
                margin-bottom: 30px;
            }
            
            #profile-form {
                flex-direction: column;
            }
            
            #profile-pic-preview,
            #profile-pic-upload {
                margin-right: 0;
                margin-bottom: 30px;
            }
            
            .notification-container {
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo !empty($student['profile_pic']) ? htmlspecialchars($student['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($student['email']); ?></p>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="#" class="active" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                    <li><a href="#" data-section="profile"><i class="fas fa-user"></i> <span>Profile</span></a></li>
                    <li><a href="#" data-section="classes"><i class="fas fa-calendar-alt"></i> <span>Today's Classes</span></a></li>
                    <li><a href="#" data-section="marks"><i class="fas fa-chart-bar"></i> <span>Marks Obtained</span></a></li>
                    <li><a href="#" data-section="materials"><i class="fas fa-book"></i> <span>Study Materials</span></a></li>
                    <li><a href="#" data-section="attendance"><i class="fas fa-clipboard-check"></i> <span>Attendance</span></a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>Student Dashboard</h2>
                <div class="user-menu">
                    <img src="<?php echo !empty($student['profile_pic']) ? htmlspecialchars($student['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="User Image">
                    <button class="logout-btn" onclick="location.href='logout.php'">Logout <i class="fas fa-sign-out-alt"></i></button>
                </div>
            </div>
            
            <!-- Dashboard Overview -->
            <div id="dashboard-section" class="content-section">
                <!-- Notification Section -->
                <div class="notification-container">
                    <div class="notification-slider" id="notification-slider">
                        <!-- Notification cards will be added dynamically by JavaScript -->
                    </div>
                    <div class="notification-indicators" id="notification-indicators">
                        <!-- Indicators will be added dynamically by JavaScript -->
                    </div>
                </div>
                
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-header">
                            <h3>Enrolled Classes</h3>
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="enrolled-classes-count">0</h1>
                            <p>Total classes you're enrolled in</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Today's Classes</h3>
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="todays-classes-count">0</h1>
                            <p>Classes scheduled for today</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Attendance Rate</h3>
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="attendance-rate">0%</h1>
                            <p>Your overall attendance percentage</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Average Marks</h3>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="average-marks">0%</h1>
                            <p>Your average score across all classes</p>
                        </div>
                    </div>
                </div>
                
                <div class="content-section">
                    <div class="section-header">
                        <h3>Today's Schedule</h3>
                    </div>
                    <div id="todays-schedule">
                        <p>Loading today's schedule...</p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Section (Hidden by default) -->
            <div id="profile-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Profile Information</h3>
                    <button id="edit-profile-btn" class="action-btn">Edit Profile <i class="fas fa-edit"></i></button>
                </div>
                <div id="profile-view">
                    <div style="display: flex; margin-bottom: 20px;">
                        <div style="margin-right: 30px;">
                            <img id="profile-pic" src="<?php echo !empty($student['profile_pic']) ? htmlspecialchars($student['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div style="flex-grow: 1;">
                            <table>
                                <tr>
                                    <td style="width: 150px; font-weight: bold;">Full Name:</td>
                                    <td id="profile-fullname"><?php echo htmlspecialchars($student['full_name']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Username:</td>
                                    <td id="profile-username"><?php echo htmlspecialchars($student['username']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Email:</td>
                                    <td id="profile-email"><?php echo htmlspecialchars($student['email']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Phone:</td>
                                    <td id="profile-phone"><?php echo htmlspecialchars($student['phone'] ?? 'Not provided'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="profile-edit" style="display: none;">
                    <form id="profile-form" enctype="multipart/form-data">
                        <div style="display: flex; margin-bottom: 20px;">
                            <div style="margin-right: 30px;">
                                <img id="profile-pic-preview" src="<?php echo !empty($student['profile_pic']) ? htmlspecialchars($student['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                                <input type="file" id="profile-pic-upload" name="profile_pic" accept="image/*" style="margin-top: 10px;">
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="margin-bottom: 15px;">
                                    <label for="fullname" style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($student['full_name']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="phone" style="display: block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">New Password (leave blank to keep current)</label>
                                    <input type="password" id="password" name="password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" id="cancel-edit-btn" class="action-btn" style="background: #f5f5f5; color: #495057; margin-right: 10px;">Cancel <i class="fas fa-times"></i></button>
                            <button type="submit" class="action-btn">Save Changes <i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Today's Classes Section -->
            <div id="classes-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Today's Classes</h3>
                </div>
                <div id="classes-content">
                    <p>Loading today's classes...</p>
                </div>
            </div>
            
            <!-- Marks Section -->
            <div id="marks-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Marks Obtained</h3>
                </div>
                <div id="marks-content">
                    <p>Loading marks...</p>
                </div>
            </div>
            
            <!-- Study Materials Section -->
            <div id="materials-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Study Materials</h3>
                </div>
                <div id="materials-content">
                    <p>Loading study materials...</p>
                </div>
            </div>
            
            <!-- Attendance Section -->
            <div id="attendance-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Attendance Record</h3>
                </div>
                <div id="attendance-content">
                    <p>Loading attendance records...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // CSRF token for AJAX requests
            const csrfToken = "<?php echo generateCsrfToken(); ?>";
            
            // Sample notifications data (in a real app, this would come from the server)
            const notifications = [
                {
                    title: "New Assignment Posted",
                    content: "Math assignment #3 has been posted. Due date: Friday, 3:00 PM",
                    icon: "fas fa-book"
                },
                {
                    title: "Upcoming Class",
                    content: "Science class starts in 15 minutes. Room: B-204",
                    icon: "fas fa-clock"
                },
                {
                    title: "Attendance Reminder",
                    content: "Your attendance is below 80% in History. Please attend regularly.",
                    icon: "fas fa-exclamation-triangle"
                },
                {
                    title: "New Study Material",
                    content: "New study material uploaded for English Literature - Chapter 5",
                    icon: "fas fa-file-alt"
                }
            ];
            
            // Initialize notifications
            let currentNotification = 0;
            const notificationSlider = $('#notification-slider');
            const indicatorsContainer = $('#notification-indicators');
            
            // Create notification cards and indicators
            notifications.forEach((notification, index) => {
                // Create notification card
                const card = $(`
                    <div class="notification-card" data-index="${index}">
                        <div class="notification-header">
                            <div class="notification-icon">
                                <i class="${notification.icon}"></i>
                            </div>
                            <div class="notification-title">${notification.title}</div>
                        </div>
                        <div class="notification-content">${notification.content}</div>
                    </div>
                `);
                
                notificationSlider.append(card);
                
                // Create indicator
                const indicator = $(`<div class="notification-indicator" data-index="${index}"></div>`);
                indicator.click(function() {
                    showNotification(index);
                });
                indicatorsContainer.append(indicator);
            });
            
            // Show first notification
            showNotification(0);
            
            // Set up automatic rotation
            setInterval(() => {
                currentNotification = (currentNotification + 1) % notifications.length;
                showNotification(currentNotification);
            }, 3000);
            
            // Function to show a specific notification
            function showNotification(index) {
                // Update current notification
                currentNotification = index;
                
                // Remove active classes
                $('.notification-card').removeClass('active').removeClass('leaving');
                $('.notification-indicator').removeClass('active');
                
                // Add leaving class to current active card (if any)
                const currentActive = $('.notification-card.active');
                if (currentActive.length) {
                    currentActive.addClass('leaving');
                }
                
                // Show new notification
                const notificationCard = $(`.notification-card[data-index="${index}"]`);
                notificationCard.addClass('active');
                
                // Update indicator
                $(`.notification-indicator[data-index="${index}"]`).addClass('active');
            }
            
            // Navigation between sections
            $('.sidebar-menu a').click(function(e) {
                e.preventDefault();
                $('.sidebar-menu a').removeClass('active');
                $(this).addClass('active');
                
                const section = $(this).data('section');
                $('.content-section').hide();
                $(`#${section}-section`).show();
                
                // Load section-specific data
                switch(section) {
                    case 'dashboard':
                        loadDashboardData();
                        break;
                    case 'profile':
                        // Already loaded with page
                        break;
                    case 'classes':
                        loadTodaysClasses();
                        break;
                    case 'marks':
                        loadMarks();
                        break;
                    case 'materials':
                        loadStudyMaterials();
                        break;
                    case 'attendance':
                        loadAttendance();
                        break;
                }
            });
            
            // Profile edit functionality
            $('#edit-profile-btn').click(function() {
                $('#profile-view').hide();
                $('#profile-edit').show();
            });
            
            $('#cancel-edit-btn').click(function() {
                $('#profile-edit').hide();
                $('#profile-view').show();
            });
            
            // Profile picture preview
            $('#profile-pic-upload').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile-pic-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Profile form submission
            $('#profile-form').submit(function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('csrf_token', csrfToken);
                
                $.ajax({
                    url: 'update_profile.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Profile updated successfully!');
                                // Update profile view
                                $('#profile-fullname').text(data.full_name);
                                $('#profile-email').text(data.email);
                                $('#profile-phone').text(data.phone || 'Not provided');
                                if (data.profile_pic) {
                                    $('#profile-pic').attr('src', data.profile_pic);
                                    $('#profile-pic-preview').attr('src', data.profile_pic);
                                    $('.user-menu img').attr('src', data.profile_pic);
                                    $('.sidebar-header img').attr('src', data.profile_pic);
                                }
                                $('#profile-edit').hide();
                                $('#profile-view').show();
                            } else {
                                alert('Error: ' + (data.message || 'Unknown error'));
                            }
                        } catch (e) {
                            alert('Error parsing server response');
                        }
                    },
                    error: function(xhr) {
                        alert('Error updating profile. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
            
            // Load dashboard data
            function loadDashboardData() {
                $.ajax({
                    url: 'get_dashboard_data.php',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        $('#enrolled-classes-count').text(data.enrolled_classes);
                        $('#todays-classes-count').text(data.todays_classes);
                        $('#attendance-rate').text(data.attendance_rate + '%');
                        $('#average-marks').text(data.average_marks + '%');
                        
                        // Animate the numbers
                        $('.card-body h1').each(function() {
                            const $this = $(this);
                            const target = parseInt($this.text());
                            $this.text('0');
                            animateValue($this, 0, target, 1000);
                        });
                        
                        function animateValue(element, start, end, duration) {
                            let startTimestamp = null;
                            const step = (timestamp) => {
                                if (!startTimestamp) startTimestamp = timestamp;
                                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                                const value = Math.floor(progress * (end - start) + start);
                                element.text(value + (element.attr('id') === 'attendance-rate' || element.attr('id') === 'average-marks' ? '%' : ''));
                                if (progress < 1) {
                                    window.requestAnimationFrame(step);
                                }
                            };
                            window.requestAnimationFrame(step);
                        }
                        
                        // Display today's schedule
                        let scheduleHtml = '<table><tr><th>Class</th><th>Time</th><th>Location</th></tr>';
                        if (data.todays_schedule.length > 0) {
                            data.todays_schedule.forEach(function(cls) {
                                scheduleHtml += `<tr>
                                    <td>${cls.class_name}</td>
                                    <td>${cls.start_time} - ${cls.end_time}</td>
                                    <td>${cls.location}</td>
                                </tr>`;
                            });
                        } else {
                            scheduleHtml += '<tr><td colspan="3" style="text-align: center;">No classes scheduled for today</td></tr>';
                        }
                        scheduleHtml += '</table>';
                        $('#todays-schedule').html(scheduleHtml);
                        
                        // Add animation to table rows
                        $('#todays-schedule tr:not(:first-child)').each(function(index) {
                            $(this).css('opacity', '0');
                            $(this).css('transform', 'translateX(-20px)');
                            $(this).delay(100 * index).animate({
                                opacity: 1,
                                transform: 'translateX(0)'
                            }, 300);
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading dashboard data:", xhr.responseText);
                        $('#todays-schedule').html('<p class="error">Error loading schedule data</p>');
                    }
                });
            }
            
            // Load today's classes
            function loadTodaysClasses() {
                $.ajax({
                    url: 'get_todays_classes.php',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = '<table><tr><th>Class</th><th>Time</th><th>Location</th></tr>';
                        if (data.length > 0) {
                            data.forEach(function(cls) {
                                html += `<tr>
                                    <td>${cls.class_name}</td>
                                    <td>${cls.start_time} - ${cls.end_time}</td>
                                    <td>${cls.location}</td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="3" style="text-align: center;">No classes scheduled for today</td></tr>';
                        }
                        html += '</table>';
                        $('#classes-content').html(html);
                        
                        // Add animation to table rows
                        $('#classes-content tr:not(:first-child)').each(function(index) {
                            $(this).css('opacity', '0');
                            $(this).css('transform', 'translateY(20px)');
                            $(this).delay(100 * index).animate({
                                opacity: 1,
                                transform: 'translateY(0)'
                            }, 300);
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading today's classes:", xhr.responseText);
                        $('#classes-content').html('<p class="error">Error loading classes data</p>');
                    }
                });
            }
            
            // Load marks
            function loadMarks() {
                $.ajax({
                    url: 'get_marks.php',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            $('#marks-content').html('<p>No marks recorded yet.</p>');
                            return;
                        }
                        
                        // Group marks by class
                        const classes = {};
                        data.forEach(function(mark) {
                            if (!classes[mark.class_id]) {
                                classes[mark.class_id] = {
                                    class_name: mark.class_name,
                                    marks: []
                                };
                            }
                            classes[mark.class_id].marks.push(mark);
                        });
                        
                        let html = '';
                        for (const classId in classes) {
                            const cls = classes[classId];
                            html += `<div style="margin-bottom: 30px;">
                                <h4 style="margin-bottom: 10px; color: #2c3e50;">${cls.class_name}</h4>
                                <table>
                                    <tr>
                                        <th>Assessment</th>
                                        <th>Date</th>
                                        <th>Marks Obtained</th>
                                        <th>Percentage</th>
                                        <th>Comments</th>
                                    </tr>`;
                            
                            cls.marks.forEach(function(mark) {
                                const percentage = Math.round((mark.marks_obtained / mark.total_marks) * 100);
                                html += `<tr>
                                    <td>${mark.assessment_name}</td>
                                    <td>${mark.assessment_date}</td>
                                    <td>${mark.marks_obtained} / ${mark.total_marks}</td>
                                    <td>${percentage}%</td>
                                    <td>${mark.comments || '-'}</td>
                                </tr>`;
                            });
                            
                            html += `</table></div>`;
                        }
                        
                        $('#marks-content').html(html);
                        
                        // Add animation to tables
                        $('#marks-content table').each(function(tableIndex) {
                            $(this).find('tr:not(:first-child)').each(function(rowIndex) {
                                $(this).css('opacity', '0');
                                $(this).css('transform', 'translateX(-20px)');
                                $(this).delay(50 * (tableIndex + rowIndex)).animate({
                                    opacity: 1,
                                    transform: 'translateX(0)'
                                }, 300);
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading marks:", xhr.responseText);
                        $('#marks-content').html('<p class="error">Error loading marks data</p>');
                    }
                });
            }
            
            // Load study materials
            function loadStudyMaterials() {
                $.ajax({
                    url: 'get_study_materials.php',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            $('#materials-content').html('<p>No study materials available.</p>');
                            return;
                        }
                        
                        // Group materials by class
                        const classes = {};
                        data.forEach(function(material) {
                            if (!classes[material.class_id]) {
                                classes[material.class_id] = {
                                    class_name: material.class_name,
                                    materials: []
                                };
                            }
                            classes[material.class_id].materials.push(material);
                        });
                        
                        let html = '';
                        for (const classId in classes) {
                            const cls = classes[classId];
                            html += `<div style="margin-bottom: 30px;">
                                <h4 style="margin-bottom: 10px; color: #2c3e50;">${cls.class_name}</h4>
                                <table>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Upload Date</th>
                                        <th>Action</th>
                                    </tr>`;
                            
                            cls.materials.forEach(function(material) {
                                html += `<tr>
                                    <td>${material.title}</td>
                                    <td>${material.description || '-'}</td>
                                    <td>${material.upload_date}</td>
                                    <td><a href="${material.file_path}" target="_blank" style="color: #4CAF50; text-decoration: none;">Download <i class="fas fa-download"></i></a></td>
                                </tr>`;
                            });
                            
                            html += `</table></div>`;
                        }
                        
                        $('#materials-content').html(html);
                        
                        // Add animation to tables
                        $('#materials-content table').each(function(tableIndex) {
                            $(this).find('tr:not(:first-child)').each(function(rowIndex) {
                                $(this).css('opacity', '0');
                                $(this).css('transform', 'translateY(20px)');
                                $(this).delay(50 * (tableIndex + rowIndex)).animate({
                                    opacity: 1,
                                    transform: 'translateY(0)'
                                }, 300);
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading study materials:", xhr.responseText);
                        $('#materials-content').html('<p class="error">Error loading study materials</p>');
                    }
                });
            }
            
            // Load attendance
            function loadAttendance() {
                $.ajax({
                    url: 'get_attendance.php',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            $('#attendance-content').html('<p>No attendance records found.</p>');
                            return;
                        }
                        
                        // Group attendance by class
                        const classes = {};
                        data.forEach(function(record) {
                            if (!classes[record.class_id]) {
                                classes[record.class_id] = {
                                    class_name: record.class_name,
                                    records: [],
                                    present: 0,
                                    total: 0
                                };
                            }
                            classes[record.class_id].records.push(record);
                            classes[record.class_id].total++;
                            if (record.status === 'Present') {
                                classes[record.class_id].present++;
                            }
                        });
                        
                        let html = '';
                        for (const classId in classes) {
                            const cls = classes[classId];
                            const percentage = Math.round((cls.present / cls.total) * 100);
                            
                            html += `<div style="margin-bottom: 30px;">
                                <h4 style="margin-bottom: 10px; color: #2c3e50;">${cls.class_name} (Attendance: ${percentage}%)</h4>
                                <table>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>`;
                            
                            cls.records.forEach(function(record) {
                                let badgeClass = '';
                                if (record.status === 'Present') {
                                    badgeClass = 'badge-success';
                                } else if (record.status === 'Late') {
                                    badgeClass = 'badge-warning';
                                } else {
                                    badgeClass = 'badge-danger';
                                }
                                
                                html += `<tr>
                                    <td>${record.date}</td>
                                    <td><span class="badge ${badgeClass}">${record.status}</span></td>
                                </tr>`;
                            });
                            
                            html += `</table></div>`;
                        }
                        
                        $('#attendance-content').html(html);
                        
                        // Add animation to tables
                        $('#attendance-content table').each(function(tableIndex) {
                            $(this).find('tr:not(:first-child)').each(function(rowIndex) {
                                $(this).css('opacity', '0');
                                $(this).css('transform', 'translateX(20px)');
                                $(this).delay(50 * (tableIndex + rowIndex)).animate({
                                    opacity: 1,
                                    transform: 'translateX(0)'
                                }, 300);
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading attendance records:", xhr.responseText);
                        $('#attendance-content').html('<p class="error">Error loading attendance records</p>');
                    }
                });
            }
            
            // Initialize dashboard on page load
            loadDashboardData();
        });
    </script>
</body>
</html>