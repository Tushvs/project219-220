<?php
require_once 'config.php';
checkLogin();

// Redirect students to their dashboard
if (!isTeacher()) {
    header("Location: dashboard.php");
    exit();
}

$teacher = getUserData();
if (!$teacher) {
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
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --success-color: #4ade80;
            --warning-color: #fbbf24;
            --danger-color: #f87171;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --sidebar-width: 280px;
            --header-height: 70px;
            --transition-speed: 0.3s;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f1f5f9;
            color: #334155;
            overflow-x: hidden;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark-color) 0%, #0f172a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            transition: all var(--transition-speed) ease;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 25px;
            background-color: rgba(0, 0, 0, 0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid var(--accent-color);
            transition: all 0.5s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-header img:hover {
            transform: rotate(10deg) scale(1.05);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .sidebar-header p {
            margin: 8px 0 0;
            font-size: 0.85rem;
            color: #94a3b8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
            height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            position: relative;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all var(--transition-speed) ease;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: white;
            background-color: rgba(67, 97, 238, 0.1);
            border-left: 3px solid var(--accent-color);
            transform: translateX(5px);
        }
        
        .sidebar-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .sidebar-menu a.active i {
            color: var(--accent-color);
        }
        
        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 25px;
            transition: all var(--transition-speed) ease;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            border-radius: 10px;
            animation: slideDown 0.5s ease;
        }
        
        .header h2 {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-menu img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 0;
            border: 2px solid var(--accent-color);
            transition: all 0.3s ease;
        }
        
        .user-menu img:hover {
            transform: scale(1.1);
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: var(--danger-color);
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }
        
        .logout-btn:hover {
            color: #dc2626;
            transform: translateX(2px);
        }
        
        /* Dashboard cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            border: none;
        }
        
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
            transform: translateY(-8px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-header h3 {
            font-size: 1rem;
            color: #64748b;
            font-weight: 500;
        }
        
        .card-header i {
            font-size: 1.8rem;
            color: var(--primary-color);
            opacity: 0.7;
        }
        
        .card-body h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .card-body p {
            color: #64748b;
            font-size: 0.95rem;
        }
        
        /* Content sections */
        .content-section {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            animation: fadeIn 0.6s ease;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .section-header h3 {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        table tr {
            transition: all 0.2s ease;
        }
        
        table tr:hover {
            background-color: #f8fafc;
            transform: translateX(3px);
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        /* Action buttons */
        .action-btn {
            padding: 8px 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            margin-right: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .action-btn i {
            font-size: 0.9rem;
        }
        
        .action-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(67, 97, 238, 0.3);
        }
        
        .action-btn.secondary {
            background: #e2e8f0;
            color: #475569;
        }
        
        .action-btn.secondary:hover {
            background: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(203, 213, 225, 0.3);
        }
        
        /* Form elements */
        select, input[type="date"], input[type="text"], input[type="email"], input[type="password"], textarea {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(76, 201, 240, 0.2);
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 25px;
            border-radius: 12px;
            width: 50%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.4s ease;
        }
        
        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.2s ease;
        }
        
        .close-btn:hover {
            color: var(--danger-color);
            transform: rotate(90deg);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #475569;
        }
        
        .form-actions {
            text-align: right;
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        /* Profile section styles */
        #profile-view {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
        }
        
        #profile-pic {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        #profile-pic:hover {
            transform: scale(1.05);
        }
        
        #profile-pic-preview {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        /* Animations */
        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        /* Responsive styles */
        @media (max-width: 1024px) {
            .sidebar {
                width: 0;
                overflow: hidden;
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                width: var(--sidebar-width);
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .modal-content {
                width: 80%;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            #profile-view {
                flex-direction: column;
            }
            
            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
        }
        
        /* Floating action button */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s ease;
            border: none;
        }
        
        .fab:hover {
            background-color: var(--secondary-color);
            transform: translateY(-5px) scale(1.1);
        }
        
        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        @media (max-width: 1024px) {
            .menu-toggle {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Mobile Menu Toggle -->
        <!-- <div class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </div> -->
        
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo !empty($teacher['profile_pic']) ? htmlspecialchars($teacher['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($teacher['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($teacher['email']); ?></p>
                <p><em>Teacher</em></p>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="#" class="active" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#" data-section="profile"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="#" data-section="my-classes"><i class="fas fa-chalkboard-teacher"></i> My Classes</a></li>
                    <li><a href="#" data-section="mark-attendance"><i class="fas fa-clipboard-check"></i> Mark Attendance</a></li>
                    <li><a href="#" data-section="upload-marks"><i class="fas fa-edit"></i> Upload Marks</a></li>
                    <li><a href="#" data-section="study-materials"><i class="fas fa-book"></i> Study Materials</a></li>
                    <li><a href="#" data-section="student-list"><i class="fas fa-users"></i> Student List</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>Teacher Dashboard</h2>
                <div class="user-menu">
                    <img src="<?php echo !empty($teacher['profile_pic']) ? htmlspecialchars($teacher['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="User Image">
                    <button class="logout-btn" onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </div>
            </div>
            
            <!-- Dashboard Overview -->
            <div id="dashboard-section" class="content-section">
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-header">
                            <h3>My Classes</h3>
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="class-count">0</h1>
                            <p>Total classes you're teaching</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Today's Classes</h3>
                            <i class="fas fa-calendar-day pulse" style="color: var(--accent-color);"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="todays-classes-count">0</h1>
                            <p>Classes scheduled for today</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Students</h3>
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="student-count">0</h1>
                            <p>Total students in your classes</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Materials</h3>
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="materials-count">0</h1>
                            <p>Study materials uploaded</p>
                        </div>
                    </div>
                </div>
                
                <div class="content-section">
                    <div class="section-header">
                        <h3>Today's Schedule</h3>
                        <span class="badge badge-success" id="current-date"></span>
                    </div>
                    <div id="todays-schedule">
                        <p>Loading today's schedule...</p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Section -->
            <div id="profile-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Profile Information</h3>
                    <button id="edit-profile-btn" class="action-btn"><i class="fas fa-edit"></i> Edit Profile</button>
                </div>
                <div id="profile-view">
                    <div style="margin-right: 30px;">
                        <img id="profile-pic" src="<?php echo !empty($teacher['profile_pic']) ? htmlspecialchars($teacher['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture">
                    </div>
                    <div style="flex-grow: 1;">
                        <table>
                            <tr>
                                <td style="width: 150px; font-weight: bold;">Full Name:</td>
                                <td id="profile-fullname"><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Username:</td>
                                <td id="profile-username"><?php echo htmlspecialchars($teacher['username']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Email:</td>
                                <td id="profile-email"><?php echo htmlspecialchars($teacher['email']); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Phone:</td>
                                <td id="profile-phone"><?php echo htmlspecialchars($teacher['phone'] ?? 'Not provided'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div id="profile-edit" style="display: none;">
                    <form id="profile-form" enctype="multipart/form-data">
                        <div style="display: flex; gap: 30px; margin-bottom: 20px;">
                            <div style="margin-right: 30px;">
                                <img id="profile-pic-preview" src="<?php echo !empty($teacher['profile_pic']) ? htmlspecialchars($teacher['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture">
                                <input type="file" id="profile-pic-upload" name="profile_pic" accept="image/*" style="margin-top: 15px;">
                            </div>
                            <div style="flex-grow: 1;">
                                <div class="form-group">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($teacher['full_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="password">New Password (leave blank to keep current)</label>
                                    <input type="password" id="password" name="password">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="cancel-edit-btn" class="action-btn secondary"><i class="fas fa-times"></i> Cancel</button>
                            <button type="submit" class="action-btn"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- My Classes Section -->
            <div id="my-classes-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>My Classes</h3>
                </div>
                <div id="my-classes-content">
                    <p>Loading classes...</p>
                </div>
            </div>
            
            <!-- Mark Attendance Section -->
            <div id="mark-attendance-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Mark Attendance</h3>
                    <div>
                        <select id="attendance-class-select" class="action-btn secondary">
                            <option value="">Select Class</option>
                        </select>
                        <input type="date" id="attendance-date" class="action-btn secondary">
                    </div>
                </div>
                <div id="mark-attendance-content">
                    <p>Please select a class and date to mark attendance.</p>
                </div>
            </div>
            
            <!-- Upload Marks Section -->
            <div id="upload-marks-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Upload Marks</h3>
                    <button id="add-assessment-btn" class="action-btn"><i class="fas fa-plus"></i> Add Assessment</button>
                </div>
                <div id="upload-marks-content">
                    <p>Loading assessments...</p>
                </div>
                
                <!-- Assessment Modal -->
                <div id="assessment-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h3 id="modal-title">Add New Assessment</h3>
                        <form id="assessment-form">
                            <input type="hidden" id="assessment-id" name="assessment_id">
                            <div class="form-group">
                                <label for="assessment-class">Class</label>
                                <select id="assessment-class" name="class_id" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="assessment-name">Assessment Name</label>
                                <input type="text" id="assessment-name" name="assessment_name" required>
                            </div>
                            <div class="form-group">
                                <label for="assessment-date">Date</label>
                                <input type="date" id="assessment-date" name="assessment_date" required>
                            </div>
                            <div class="form-group">
                                <label for="total-marks">Total Marks</label>
                                <input type="number" id="total-marks" name="total_marks" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="assessment-comments">Comments (Optional)</label>
                                <textarea id="assessment-comments" name="comments" rows="3"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="action-btn secondary" id="cancel-assessment"><i class="fas fa-times"></i> Cancel</button>
                                <button type="submit" class="action-btn"><i class="fas fa-save"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Marks Entry Modal -->
                <div id="marks-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h3 id="marks-modal-title">Enter Marks for <span id="assessment-title"></span></h3>
                        <div id="marks-modal-content">
                            <p>Loading students...</p>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="action-btn secondary" id="cancel-marks"><i class="fas fa-times"></i> Cancel</button>
                            <button type="button" class="action-btn" id="save-marks"><i class="fas fa-save"></i> Save Marks</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Study Materials Section -->
            <div id="study-materials-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Study Materials</h3>
                    <button id="add-material-btn" class="action-btn"><i class="fas fa-plus"></i> Add Material</button>
                </div>
                <div id="study-materials-content">
                    <p>Loading study materials...</p>
                </div>
                
                <!-- Material Modal -->
                <div id="material-modal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn">&times;</span>
                        <h3 id="material-modal-title">Add New Study Material</h3>
                        <form id="material-form" enctype="multipart/form-data">
                            <input type="hidden" id="material-id" name="material_id">
                            <div class="form-group">
                                <label for="material-class">Class</label>
                                <select id="material-class" name="class_id" required>
                                    <option value="">Select Class</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="material-title">Title</label>
                                <input type="text" id="material-title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="material-description">Description (Optional)</label>
                                <textarea id="material-description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="material-file">File</label>
                                <input type="file" id="material-file" name="file" required>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="action-btn secondary" id="cancel-material"><i class="fas fa-times"></i> Cancel</button>
                                <button type="submit" class="action-btn"><i class="fas fa-upload"></i> Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Student List Section -->
            <div id="student-list-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Student List</h3>
                    <select id="student-list-class-select" class="action-btn secondary">
                        <option value="">All Classes</option>
                    </select>
                </div>
                <div id="student-list-content">
                    <p>Loading students...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" id="fabButton">
        <i class="fas fa-question"></i>
    </button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // CSRF token for AJAX requests
            const csrfToken = "<?php echo generateCsrfToken(); ?>";
            
            // Set current date in dashboard
            const today = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            $('#current-date').text(today.toLocaleDateString('en-US', options));
            
            // Mobile menu toggle
            $('#menuToggle').click(function() {
                $('#sidebar').toggleClass('active');
                $(this).toggleClass('active');
            });
            
            // Navigation between sections
            $('.sidebar-menu a').click(function(e) {
                e.preventDefault();
                $('.sidebar-menu a').removeClass('active');
                $(this).addClass('active');
                
                const section = $(this).data('section');
                $('.content-section').hide();
                $(`#${section}-section`).show().addClass('animated-section');
                
                // Load section-specific data
                switch(section) {
                    case 'dashboard':
                        loadTeacherDashboard();
                        break;
                    case 'profile':
                        // Already loaded with page
                        break;
                    case 'my-classes':
                        loadTeacherClasses();
                        break;
                    case 'mark-attendance':
                        loadAttendanceClasses();
                        break;
                    case 'upload-marks':
                        loadAssessments();
                        break;
                    case 'study-materials':
                        loadStudyMaterials();
                        break;
                    case 'student-list':
                        loadStudentList();
                        break;
                }
                
                // Close sidebar on mobile after selection
                if ($(window).width() <= 1024) {
                    $('#sidebar').removeClass('active');
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
            
            // Load teacher dashboard data
            function loadTeacherDashboard() {
                $.ajax({
                    url: 'teacher_api.php?action=dashboard',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        // Animate counting up the numbers
                        animateValue('class-count', 0, data.class_count, 1000);
                        animateValue('todays-classes-count', 0, data.todays_classes_count, 1000);
                        animateValue('student-count', 0, data.student_count, 1000);
                        animateValue('materials-count', 0, data.materials_count, 1000);
                        
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
                    },
                    error: function(xhr) {
                        console.error("Error loading teacher dashboard:", xhr.responseText);
                        $('#todays-schedule').html('<p class="error">Error loading schedule data</p>');
                    }
                });
            }
            
            // Animation function for counting up numbers
            function animateValue(id, start, end, duration) {
                const obj = document.getElementById(id);
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.innerHTML = Math.floor(progress * (end - start) + start);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }
            
            // Load teacher's classes
            function loadTeacherClasses() {
                $.ajax({
                    url: 'teacher_api.php?action=classes',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = '<table><tr><th>Class</th><th>Students</th><th>Schedule</th><th>Actions</th></tr>';
                        if (data.length > 0) {
                            data.forEach(function(cls) {
                                html += `<tr>
                                    <td>${cls.class_name}</td>
                                    <td>${cls.student_count}</td>
                                    <td>${cls.schedule || 'Not scheduled'}</td>
                                    <td>
                                        <button class="action-btn view-students" data-class-id="${cls.class_id}"><i class="fas fa-users"></i> Students</button>
                                    </td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="4" style="text-align: center;">No classes assigned</td></tr>';
                        }
                        html += '</table>';
                        $('#my-classes-content').html(html);
                    },
                    error: function(xhr) {
                        console.error("Error loading teacher classes:", xhr.responseText);
                        $('#my-classes-content').html('<p class="error">Error loading classes</p>');
                    }
                });
            }
            
            // Load classes for attendance marking
            function loadAttendanceClasses() {
                $.ajax({
                    url: 'teacher_api.php?action=classes',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        const classSelect = $('#attendance-class-select');
                        classSelect.empty().append('<option value="">Select Class</option>');
                        
                        if (data.length > 0) {
                            data.forEach(function(cls) {
                                classSelect.append(`<option value="${cls.class_id}">${cls.class_name}</option>`);
                            });
                        }
                        
                        // Set today's date as default
                        $('#attendance-date').val(new Date().toISOString().split('T')[0]);
                        
                        // Handle class selection change
                        classSelect.change(function() {
                            const classId = $(this).val();
                            const date = $('#attendance-date').val();
                            
                            if (classId && date) {
                                loadAttendanceForm(classId, date);
                            }
                        });
                        
                        // Handle date change
                        $('#attendance-date').change(function() {
                            const classId = classSelect.val();
                            const date = $(this).val();
                            
                            if (classId && date) {
                                loadAttendanceForm(classId, date);
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading attendance classes:", xhr.responseText);
                        $('#mark-attendance-content').html('<p class="error">Error loading classes</p>');
                    }
                });
            }
            
            // Load attendance form for a specific class and date
            function loadAttendanceForm(classId, date) {
                $.ajax({
                    url: `teacher_api.php?action=attendance_form&class_id=${classId}&date=${date}`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = `<h4 style="margin-bottom: 20px;">${data.class_name} - ${date}</h4>`;
                        html += '<form id="attendance-form">';
                        html += '<input type="hidden" name="class_id" value="' + classId + '">';
                        html += '<input type="hidden" name="date" value="' + date + '">';
                        html += '<table><tr><th>Student</th><th>Status</th></tr>';
                        
                        data.students.forEach(function(student) {
                            html += `<tr>
                                <td>${student.full_name}</td>
                                <td>
                                    <select name="attendance[${student.student_id}]" class="action-btn secondary" style="padding: 8px 12px;">
                                        <option value="Present" ${student.attendance && student.attendance.status === 'Present' ? 'selected' : ''}>Present</option>
                                        <option value="Absent" ${student.attendance && student.attendance.status === 'Absent' ? 'selected' : ''}>Absent</option>
                                        <option value="Late" ${student.attendance && student.attendance.status === 'Late' ? 'selected' : ''}>Late</option>
                                    </select>
                                </td>
                            </tr>`;
                        });
                        
                        html += '</table>';
                        html += '<div style="margin-top: 20px; text-align: right;">';
                        html += '<button type="submit" class="action-btn"><i class="fas fa-save"></i> Save Attendance</button>';
                        html += '</div>';
                        html += '</form>';
                        
                        $('#mark-attendance-content').html(html);
                        
                        // Handle form submission
                        $('#attendance-form').submit(function(e) {
                            e.preventDefault();
                            const formData = $(this).serialize() + '&csrf_token=' + csrfToken;
                            
                            $.ajax({
                                url: 'teacher_api.php?action=save_attendance',
                                type: 'POST',
                                data: formData,
                                headers: {
                                    'X-CSRF-Token': csrfToken
                                },
                                success: function(response) {
                                    try {
                                        const data = JSON.parse(response);
                                        if (data.success) {
                                            alert('Attendance saved successfully!');
                                        } else {
                                            alert('Error: ' + (data.message || 'Failed to save attendance'));
                                        }
                                    } catch (e) {
                                        alert('Error parsing server response');
                                    }
                                },
                                error: function(xhr) {
                                    alert('Error saving attendance. Please try again.');
                                    console.error(xhr.responseText);
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading attendance form:", xhr.responseText);
                        $('#mark-attendance-content').html('<p class="error">Error loading attendance form</p>');
                    }
                });
            }
            
            // Load assessments
            function loadAssessments() {
                $.ajax({
                    url: 'teacher_api.php?action=assessments',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = '<table><tr><th>Assessment</th><th>Class</th><th>Date</th><th>Total Marks</th><th>Actions</th></tr>';
                        if (data.length > 0) {
                            data.forEach(function(assessment) {
                                html += `<tr>
                                    <td>${assessment.assessment_name}</td>
                                    <td>${assessment.class_name}</td>
                                    <td>${assessment.assessment_date}</td>
                                    <td>${assessment.total_marks}</td>
                                    <td>
                                        <button class="action-btn edit-assessment" data-id="${assessment.mark_id}"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="action-btn enter-marks" data-id="${assessment.mark_id}"><i class="fas fa-pen"></i> Enter Marks</button>
                                    </td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="5" style="text-align: center;">No assessments created yet</td></tr>';
                        }
                        html += '</table>';
                        $('#upload-marks-content').html(html);
                        
                        // Set up event handlers for buttons
                        $('.edit-assessment').click(function() {
                            const assessmentId = $(this).data('id');
                            openAssessmentModal(assessmentId);
                        });
                        
                        $('.enter-marks').click(function() {
                            const assessmentId = $(this).data('id');
                            openMarksModal(assessmentId);
                        });
                        
                        // Add assessment button
                        $('#add-assessment-btn').click(function() {
                            openAssessmentModal();
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading assessments:", xhr.responseText);
                        $('#upload-marks-content').html('<p class="error">Error loading assessments</p>');
                    }
                });
            }
            
            // Open assessment modal
            function openAssessmentModal(assessmentId = null) {
                const modal = $('#assessment-modal');
                const form = $('#assessment-form');
                
                if (assessmentId) {
                    // Edit existing assessment
                    $('#modal-title').text('Edit Assessment');
                    $('#assessment-id').val(assessmentId);
                    
                    // Load assessment data
                    $.ajax({
                        url: `teacher_api.php?action=get_assessment&id=${assessmentId}`,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        success: function(data) {
                            // Load classes first
                            loadClassDropdown('assessment-class', data.class_id);
                            
                            // Set other values
                            $('#assessment-name').val(data.assessment_name);
                            $('#assessment-date').val(data.assessment_date);
                            $('#total-marks').val(data.total_marks);
                            $('#assessment-comments').val(data.comments);
                            
                            modal.show();
                        },
                        error: function(xhr) {
                            console.error("Error loading assessment:", xhr.responseText);
                            alert('Error loading assessment data');
                        }
                    });
                } else {
                    // Add new assessment
                    $('#modal-title').text('Add New Assessment');
                    $('#assessment-id').val('');
                    form.trigger('reset');
                    
                    // Load classes
                    loadClassDropdown('assessment-class');
                    
                    modal.show();
                }
                
                // Set up form submission
                form.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    saveAssessment();
                });
            }
            
            // Save assessment
            function saveAssessment() {
                const formData = $('#assessment-form').serialize() + '&csrf_token=' + csrfToken;
                
                $.ajax({
                    url: 'teacher_api.php?action=save_assessment',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Assessment saved successfully!');
                                $('#assessment-modal').hide();
                                loadAssessments();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to save assessment'));
                            }
                        } catch (e) {
                            alert('Error parsing server response');
                        }
                    },
                    error: function(xhr) {
                        alert('Error saving assessment. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }
            
            // Open marks entry modal
            function openMarksModal(assessmentId) {
                const modal = $('#marks-modal');
                
                // Load assessment info
                $.ajax({
                    url: `teacher_api.php?action=get_assessment&id=${assessmentId}`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(assessment) {
                        $('#assessment-title').text(assessment.assessment_name);
                        
                        // Load students and their marks
                        $.ajax({
                            url: `teacher_api.php?action=assessment_students&class_id=${assessment.class_id}&assessment_id=${assessmentId}`,
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-Token': csrfToken
                            },
                            success: function(students) {
                                let html = '<table><tr><th>Student</th><th>Marks</th></tr>';
                                
                                students.forEach(function(student) {
                                    html += `<tr>
                                        <td>${student.full_name}</td>
                                        <td>
                                            <input type="number" class="student-mark" data-student-id="${student.student_id}" 
                                                value="${student.mark || ''}" min="0" max="${assessment.total_marks}" 
                                                style="padding: 8px; width: 80px; border: 1px solid #e2e8f0; border-radius: 4px;">
                                        </td>
                                    </tr>`;
                                });
                                
                                html += '</table>';
                                $('#marks-modal-content').html(html);
                                modal.show();
                                
                                // Set up save button
                                $('#save-marks').off('click').on('click', function() {
                                    saveMarks(assessmentId, assessment.class_id);
                                });
                            },
                            error: function(xhr) {
                                console.error("Error loading students:", xhr.responseText);
                                $('#marks-modal-content').html('<p class="error">Error loading students</p>');
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading assessment:", xhr.responseText);
                        alert('Error loading assessment data');
                    }
                });
            }
            
            // Save marks for assessment
            function saveMarks(assessmentId, classId) {
                const marks = {};
                $('.student-mark').each(function() {
                    const studentId = $(this).data('student-id');
                    const mark = $(this).val();
                    marks[studentId] = mark;
                });
                
                $.ajax({
                    url: 'teacher_api.php?action=save_marks',
                    type: 'POST',
                    data: {
                        assessment_id: assessmentId,
                        class_id: classId,
                        marks: JSON.stringify(marks),
                        csrf_token: csrfToken
                    },
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Marks saved successfully!');
                                $('#marks-modal').hide();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to save marks'));
                            }
                        } catch (e) {
                            alert('Error parsing server response');
                        }
                    },
                    error: function(xhr) {
                        alert('Error saving marks. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }
            
            // Load study materials
            function loadStudyMaterials() {
                $.ajax({
                    url: 'teacher_api.php?action=materials',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = '<table><tr><th>Title</th><th>Class</th><th>Upload Date</th><th>Actions</th></tr>';
                        if (data.length > 0) {
                            data.forEach(function(material) {
                                html += `<tr>
                                    <td>${material.title}</td>
                                    <td>${material.class_name}</td>
                                    <td>${material.upload_date}</td>
                                    <td>
                                        <a href="${material.file_path}" target="_blank" class="action-btn"><i class="fas fa-download"></i> Download</a>
                                        <button class="action-btn edit-material" data-id="${material.material_id}"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="action-btn delete-material" data-id="${material.material_id}"><i class="fas fa-trash"></i> Delete</button>
                                    </td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="4" style="text-align: center;">No study materials uploaded yet</td></tr>';
                        }
                        html += '</table>';
                        $('#study-materials-content').html(html);
                        
                        // Set up event handlers for buttons
                        $('.edit-material').click(function() {
                            const materialId = $(this).data('id');
                            openMaterialModal(materialId);
                        });
                        
                        $('.delete-material').click(function() {
                            const materialId = $(this).data('id');
                            if (confirm('Are you sure you want to delete this material?')) {
                                deleteMaterial(materialId);
                            }
                        });
                        
                        // Add material button
                        $('#add-material-btn').click(function() {
                            openMaterialModal();
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading study materials:", xhr.responseText);
                        $('#study-materials-content').html('<p class="error">Error loading study materials</p>');
                    }
                });
            }
            
            // Open material modal
            function openMaterialModal(materialId = null) {
                const modal = $('#material-modal');
                const form = $('#material-form');
                
                if (materialId) {
                    // Edit existing material
                    $('#material-modal-title').text('Edit Study Material');
                    $('#material-id').val(materialId);
                    
                    // Load material data
                    $.ajax({
                        url: `teacher_api.php?action=get_material&id=${materialId}`,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        },
                        success: function(data) {
                            // Load classes first
                            loadClassDropdown('material-class', data.class_id);
                            
                            // Set other values
                            $('#material-title').val(data.title);
                            $('#material-description').val(data.description);
                            $('#material-file').removeAttr('required'); // File not required for edit
                            
                            modal.show();
                        },
                        error: function(xhr) {
                            console.error("Error loading material:", xhr.responseText);
                            alert('Error loading material data');
                        }
                    });
                } else {
                    // Add new material
                    $('#material-modal-title').text('Add New Study Material');
                    $('#material-id').val('');
                    form.trigger('reset');
                    $('#material-file').attr('required', true); // File required for new
                    
                    // Load classes
                    loadClassDropdown('material-class');
                    
                    modal.show();
                }
                
                // Set up form submission
                form.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    saveMaterial();
                });
            }
            
            // Save material
            function saveMaterial() {
                const formData = new FormData($('#material-form')[0]);
                formData.append('csrf_token', csrfToken);
                
                $.ajax({
                    url: 'teacher_api.php?action=save_material',
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
                                alert('Material saved successfully!');
                                $('#material-modal').hide();
                                loadStudyMaterials();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to save material'));
                            }
                        } catch (e) {
                            alert('Error parsing server response');
                        }
                    },
                    error: function(xhr) {
                        alert('Error saving material. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }
            
            // Delete material
            function deleteMaterial(materialId) {
                $.ajax({
                    url: 'teacher_api.php?action=delete_material',
                    type: 'POST',
                    data: {
                        material_id: materialId,
                        csrf_token: csrfToken
                    },
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.success) {
                                alert('Material deleted successfully!');
                                loadStudyMaterials();
                            } else {
                                alert('Error: ' + (data.message || 'Failed to delete material'));
                            }
                        } catch (e) {
                            alert('Error parsing server response');
                        }
                    },
                    error: function(xhr) {
                        alert('Error deleting material. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }
            
            // Load student list
            function loadStudentList() {
                $.ajax({
                    url: 'teacher_api.php?action=students',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        // Populate class filter dropdown
                        const classSelect = $('#student-list-class-select');
                        const classes = {};
                        
                        data.forEach(function(student) {
                            student.classes.forEach(function(cls) {
                                if (!classes[cls]) {
                                    classes[cls] = true;
                                    classSelect.append(`<option value="${cls}">${cls}</option>`);
                                }
                            });
                        });
                        
                        // Display all students initially
                        displayStudents(data);
                        
                        // Handle class filter change
                        classSelect.change(function() {
                            const selectedClass = $(this).val();
                            if (selectedClass) {
                                const filtered = data.filter(student => 
                                    student.classes.includes(selectedClass)
                                );
                                displayStudents(filtered);
                            } else {
                                displayStudents(data);
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error("Error loading student list:", xhr.responseText);
                        $('#student-list-content').html('<p class="error">Error loading student list</p>');
                    }
                });
            }
            
            // Display students in the student list
            function displayStudents(students) {
                let html = '<table><tr><th>Name</th><th>Email</th><th>Phone</th><th>Classes</th></tr>';
                if (students.length > 0) {
                    students.forEach(function(student) {
                        html += `<tr>
                            <td>${student.full_name}</td>
                            <td>${student.email}</td>
                            <td>${student.phone || '-'}</td>
                            <td>${student.classes.join(', ')}</td>
                        </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="4" style="text-align: center;">No students found</td></tr>';
                }
                html += '</table>';
                $('#student-list-content').html(html);
            }
            
            // Load class dropdown
            function loadClassDropdown(dropdownId, selectedId = null) {
                $.ajax({
                    url: 'teacher_api.php?action=classes',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        const dropdown = $(`#${dropdownId}`);
                        dropdown.empty().append('<option value="">Select Class</option>');
                        
                        if (data.length > 0) {
                            data.forEach(function(cls) {
                                const option = $(`<option value="${cls.class_id}">${cls.class_name}</option>`);
                                if (selectedId && cls.class_id == selectedId) {
                                    option.attr('selected', true);
                                }
                                dropdown.append(option);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error("Error loading classes:", xhr.responseText);
                    }
                });
            }
            
            // Modal close handlers
            $('.close-btn, #cancel-assessment, #cancel-marks, #cancel-material').click(function() {
                $(this).closest('.modal').hide();
            });
            
            // Close modal when clicking outside
            $(window).click(function(e) {
                if ($(e.target).hasClass('modal')) {
                    $('.modal').hide();
                }
            });
            
            // Floating action button
            $('#fabButton').click(function() {
                alert('Need help? Contact support at support@schoolsystem.com');
            });
            
            // Initialize teacher dashboard on page load
            loadTeacherDashboard();
        });
    </script>
</body>
</html>