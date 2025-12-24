<?php
require_once 'config.php';
checkLogin();

// Redirect non-parent users
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'parent') {
    header("Location: login.php");
    exit();
}

// Get parent data
$parent = getUserData();
if (!$parent) {
    header("Location: logout.php");
    exit();
}

// Get linked student (in a real system, you'd have a parent-student relationship table)
$student_id = $_SESSION['student_id'] ?? null;
if (!$student_id) {
    // For demo purposes, we'll use student_id 1
    $student_id = 1;
    $_SESSION['student_id'] = $student_id;
}

// Get student details
$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Get student's classes
$stmt = $conn->prepare("SELECT c.class_id, c.class_name 
                       FROM classes c
                       JOIN student_classes sc ON c.class_id = sc.class_id
                       WHERE sc.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Set default timezone
date_default_timezone_set('America/New_York');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --accent-color: #fd79a8;
            --dark-color: #2d3436;
            --light-color: #f5f6fa;
            --success-color: #00b894;
            --warning-color: #fdcb6e;
            --danger-color: #d63031;
            --sidebar-width: 280px;
            --header-height: 80px;
            --card-radius: 12px;
            --transition-speed: 0.3s;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Arial', sans-serif;
        }
        
        body {
            background-color: var(--light-color);
            color: var(--dark-color);
            overflow-x: hidden;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInLeft {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            position: fixed;
            height: 100vh;
            transition: all var(--transition-speed);
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 25px;
            background-color: rgba(0,0,0,0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid rgba(255,255,255,0.2);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all var(--transition-speed);
        }
        
        .sidebar-header img:hover {
            transform: rotate(5deg) scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .sidebar-header p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.8);
        }
        
        .sidebar-menu {
            padding: 25px 0;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            position: relative;
        }
        
        .sidebar-menu li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: var(--accent-color);
            transform: scaleY(0);
            transition: transform 0.2s, width 0.4s cubic-bezier(1,0,0,1) 0.2s;
        }
        
        .sidebar-menu li:hover::before {
            transform: scaleY(1);
            width: 100%;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all var(--transition-speed);
            position: relative;
            z-index: 1;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 25px;
            animation: fadeIn 0.5s ease-out;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border-radius: var(--card-radius);
            animation: slideInLeft 0.4s ease-out;
        }
        
        .header h2 {
            color: var(--dark-color);
            font-size: 1.8rem;
            font-weight: 600;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
        }
        
        .user-menu img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid var(--primary-color);
            transition: all var(--transition-speed);
        }
        
        .user-menu img:hover {
            transform: rotate(10deg) scale(1.1);
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: var(--danger-color);
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all var(--transition-speed);
            display: flex;
            align-items: center;
        }
        
        .logout-btn i {
            margin-right: 5px;
        }
        
        .logout-btn:hover {
            color: #ff0000;
            transform: translateX(3px);
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
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all var(--transition-speed);
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card:hover .card-header i {
            animation: pulse 1s infinite;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .card-header h3 {
            font-size: 1.1rem;
            color: #555;
            font-weight: 500;
        }
        
        .card-header i {
            font-size: 1.8rem;
            color: var(--primary-color);
            transition: all var(--transition-speed);
        }
        
        .card-body h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .card-body p {
            color: #777;
            font-size: 0.95rem;
        }
        
        /* Content sections */
        .content-section {
            background-color: white;
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            animation: fadeIn 0.7s ease-out;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .section-header h3 {
            color: var(--dark-color);
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
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border-radius: 3px;
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
            border-bottom: 1px solid #eee;
        }
        
        table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        table tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background-color: rgba(0, 184, 148, 0.1);
            color: var(--success-color);
        }
        
        .badge-warning {
            background-color: rgba(253, 203, 110, 0.1);
            color: #e17055;
        }
        
        .badge-danger {
            background-color: rgba(214, 48, 49, 0.1);
            color: var(--danger-color);
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .dashboard-cards {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        /* Student info panel */
        .student-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            border-radius: var(--card-radius);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: all var(--transition-speed);
            animation: fadeIn 0.5s ease-out;
        }
        
        .student-info:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .student-info img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 25px;
            border: 4px solid rgba(108, 92, 231, 0.2);
            transition: all var(--transition-speed);
        }
        
        .student-info:hover img {
            transform: rotate(5deg) scale(1.05);
            border-color: rgba(108, 92, 231, 0.4);
        }
        
        .student-details h3 {
            margin-bottom: 8px;
            color: var(--dark-color);
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .student-details p {
            color: #666;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }
        
        .student-details p strong {
            color: var(--dark-color);
            font-weight: 500;
        }
        
        /* Form elements */
        select, input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: all var(--transition-speed);
        }
        
        select:focus, input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
        }
        
        button, .action-btn {
            padding: 10px 20px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all var(--transition-speed);
            box-shadow: 0 4px 10px rgba(108, 92, 231, 0.3);
        }
        
        button:hover, .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
        }
        
        /* Profile section specific */
        #profile-pic, #profile-pic-preview {
            transition: all var(--transition-speed);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        #profile-pic:hover, #profile-pic-preview:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        #profile-pic-upload {
            cursor: pointer;
        }
        
        /* Loading animations */
        @keyframes shimmer {
            0% { background-position: -468px 0 }
            100% { background-position: 468px 0 }
        }
        
        .loading-shimmer {
            animation-duration: 1.5s;
            animation-fill-mode: forwards;
            animation-iteration-count: infinite;
            animation-name: shimmer;
            animation-timing-function: linear;
            background: #f6f7f8;
            background: linear-gradient(to right, #f6f7f8 8%, #e8e8e8 18%, #f6f7f8 33%);
            background-size: 800px 104px;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo !empty($parent['profile_pic']) ? htmlspecialchars($parent['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($parent['full_name']); ?></h3>
                <p><?php echo htmlspecialchars($parent['email']); ?></p>
                <p><em>Parent</em></p>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="#" class="active" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#" data-section="attendance"><i class="fas fa-clipboard-check"></i> Attendance</a></li>
                    <li><a href="#" data-section="marks"><i class="fas fa-chart-bar"></i> Marks</a></li>
                    <li><a href="#" data-section="teachers"><i class="fas fa-chalkboard-teacher"></i> Teachers</a></li>
                    <li><a href="#" data-section="profile"><i class="fas fa-user"></i> Profile</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h2>Parent Dashboard</h2>
                <div class="user-menu">
                    <img src="<?php echo !empty($parent['profile_pic']) ? htmlspecialchars($parent['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="User Image">
                    <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
                </div>
            </div>
            
            <!-- Student Information -->
            <div class="student-info">
                <img src="<?php echo !empty($student['profile_pic']) ? htmlspecialchars($student['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Student Photo">
                <div class="student-details">
                    <h3><?php echo htmlspecialchars($student['full_name']); ?></h3>
                    <p><strong>Grade:</strong> 10th Grade</p>
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                </div>
            </div>
            
            <!-- Dashboard Overview -->
            <div id="dashboard-section" class="content-section">
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-header">
                            <h3>Enrolled Classes</h3>
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="card-body">
                            <h1><?php echo count($classes); ?></h1>
                            <p>Total classes enrolled</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Attendance Rate</h3>
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="attendance-rate">0%</h1>
                            <p>Overall attendance percentage</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3>Average Marks</h3>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="card-body">
                            <h1 id="average-marks">0%</h1>
                            <p>Average score across all classes</p>
                        </div>
                    </div>
                </div>
                
                <div class="content-section">
                    <div class="section-header">
                        <h3>Recent Marks</h3>
                    </div>
                    <div id="recent-marks">
                        <p>Loading recent marks...</p>
                    </div>
                </div>
            </div>
            
            <!-- Attendance Section -->
            <div id="attendance-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Attendance Record</h3>
                    <select id="attendance-class-filter" style="padding: 5px;">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['class_id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="attendance-content">
                    <p>Loading attendance records...</p>
                </div>
            </div>
            
            <!-- Marks Section -->
            <div id="marks-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Marks and Assessments</h3>
                    <select id="marks-class-filter" style="padding: 5px;">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['class_id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="marks-content">
                    <p>Loading marks and assessments...</p>
                </div>
            </div>
            
            <!-- Teachers Section -->
            <div id="teachers-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Teachers</h3>
                </div>
                <div id="teachers-content">
                    <p>Loading teacher information...</p>
                </div>
            </div>
            
            <!-- Profile Section -->
            <div id="profile-section" class="content-section" style="display: none;">
                <div class="section-header">
                    <h3>Profile Information</h3>
                    <button id="edit-profile-btn" class="action-btn">Edit Profile</button>
                </div>
                <div id="profile-view">
                    <div style="display: flex; margin-bottom: 20px;">
                        <div style="margin-right: 30px;">
                            <img id="profile-pic" src="<?php echo !empty($parent['profile_pic']) ? htmlspecialchars($parent['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                        </div>
                        <div style="flex-grow: 1;">
                            <table>
                                <tr>
                                    <td style="width: 150px; font-weight: bold;">Full Name:</td>
                                    <td id="profile-fullname"><?php echo htmlspecialchars($parent['full_name']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Username:</td>
                                    <td id="profile-username"><?php echo htmlspecialchars($parent['username']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Email:</td>
                                    <td id="profile-email"><?php echo htmlspecialchars($parent['email']); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Phone:</td>
                                    <td id="profile-phone"><?php echo htmlspecialchars($parent['phone'] ?? 'Not provided'); ?></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Linked Student:</td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?> (ID: <?php echo htmlspecialchars($student['student_id']); ?>)</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="profile-edit" style="display: none;">
                    <form id="profile-form" enctype="multipart/form-data">
                        <div style="display: flex; margin-bottom: 20px;">
                            <div style="margin-right: 30px;">
                                <img id="profile-pic-preview" src="<?php echo !empty($parent['profile_pic']) ? htmlspecialchars($parent['profile_pic']) : 'https://via.placeholder.com/150'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                                <input type="file" id="profile-pic-upload" name="profile_pic" accept="image/*" style="margin-top: 10px;">
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="margin-bottom: 15px;">
                                    <label for="fullname" style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($parent['full_name']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($parent['email']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="phone" style="display: block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($parent['phone'] ?? ''); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">New Password (leave blank to keep current)</label>
                                    <input type="password" id="password" name="password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" id="cancel-edit-btn" style="padding: 8px 15px; margin-right: 10px; background:rgb(90, 76, 213); border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Cancel</button>
                            <button type="submit" style="padding: 8px 15px; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // CSRF token for AJAX requests
            const csrfToken = "<?php echo generateCsrfToken(); ?>";
            
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
                    case 'attendance':
                        loadAttendance();
                        break;
                    case 'marks':
                        loadMarks();
                        break;
                    case 'teachers':
                        loadTeachers();
                        break;
                    case 'profile':
                        // Already loaded with page
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
                    url: 'parent_api.php?action=dashboard&student_id=<?php echo $student_id; ?>',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        $('#attendance-rate').text(data.attendance_rate + '%');
                        $('#average-marks').text(data.average_marks + '%');
                        
                        // Display recent marks
                        let html = '<table><tr><th>Class</th><th>Assessment</th><th>Date</th><th>Marks</th><th>Comments</th></tr>';
                        if (data.recent_marks.length > 0) {
                            data.recent_marks.forEach(function(mark) {
                                const percentage = Math.round((mark.marks_obtained / mark.total_marks) * 100);
                                html += `<tr>
                                    <td>${mark.class_name}</td>
                                    <td>${mark.assessment_name}</td>
                                    <td>${mark.assessment_date}</td>
                                    <td>${mark.marks_obtained}/${mark.total_marks} (${percentage}%)</td>
                                    <td>${mark.comments || '-'}</td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="5" style="text-align: center;">No recent marks found</td></tr>';
                        }
                        html += '</table>';
                        $('#recent-marks').html(html);
                    },
                    error: function(xhr) {
                        console.error("Error loading dashboard data:", xhr.responseText);
                        $('#recent-marks').html('<p class="error">Error loading dashboard data</p>');
                    }
                });
            }
            
            // Load attendance data
            function loadAttendance() {
                const classId = $('#attendance-class-filter').val();
                let url = `parent_api.php?action=attendance&student_id=<?php echo $student_id; ?>`;
                if (classId) {
                    url += `&class_id=${classId}`;
                }
                
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = '<table><tr><th>Date</th><th>Class</th><th>Status</th></tr>';
                        if (data.length > 0) {
                            data.forEach(function(record) {
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
                                    <td>${record.class_name}</td>
                                    <td><span class="badge ${badgeClass}">${record.status}</span></td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="3" style="text-align: center;">No attendance records found</td></tr>';
                        }
                        html += '</table>';
                        $('#attendance-content').html(html);
                    },
                    error: function(xhr) {
                        console.error("Error loading attendance data:", xhr.responseText);
                        $('#attendance-content').html('<p class="error">Error loading attendance records</p>');
                    }
                });
            }
            
            // Handle attendance class filter change
            $('#attendance-class-filter').change(function() {
                loadAttendance();
            });
            
            // Load marks data
            function loadMarks() {
                const classId = $('#marks-class-filter').val();
                let url = `parent_api.php?action=marks&student_id=<?php echo $student_id; ?>`;
                if (classId) {
                    url += `&class_id=${classId}`;
                }
                
                $.ajax({
                    url: url,
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
                    },
                    error: function(xhr) {
                        console.error("Error loading marks:", xhr.responseText);
                        $('#marks-content').html('<p class="error">Error loading marks</p>');
                    }
                });
            }
            
            // Handle marks class filter change
            $('#marks-class-filter').change(function() {
                loadMarks();
            });
            
            // Load teachers data
            function loadTeachers() {
                $.ajax({
                    url: `parent_api.php?action=teachers&student_id=<?php echo $student_id; ?>`,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    success: function(data) {
                        let html = '<table><tr><th>Name</th><th>Class</th><th>Email</th><th>Phone</th></tr>';
                        if (data.length > 0) {
                            data.forEach(function(teacher) {
                                html += `<tr>
                                    <td>${teacher.teacher_name}</td>
                                    <td>${teacher.class_name}</td>
                                    <td><a href="mailto:${teacher.email}">${teacher.email}</a></td>
                                    <td>${teacher.phone || '-'}</td>
                                </tr>`;
                            });
                        } else {
                            html += '<tr><td colspan="4" style="text-align: center;">No teachers found</td></tr>';
                        }
                        html += '</table>';
                        $('#teachers-content').html(html);
                    },
                    error: function(xhr) {
                        console.error("Error loading teachers:", xhr.responseText);
                        $('#teachers-content').html('<p class="error">Error loading teacher information</p>');
                    }
                });
            }
            
            // Initialize dashboard on page load
            loadDashboardData();
        });
    </script>
</body>
</html>