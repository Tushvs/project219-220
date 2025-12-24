<?php
require_once 'config.php';
checkLogin();

if (!isTeacher()) {
    header("HTTP/1.1 403 Forbidden");
    die("Access denied");
}

$action = $_GET['action'] ?? '';
$response = [];

switch ($action) {
    case 'dashboard':
        $teacher_id = $_SESSION['user_id'];
        
        // Get class count
        $sql = "SELECT COUNT(*) as count FROM teacher_classes WHERE teacher_id = $teacher_id";
        $result = $conn->query($sql);
        $response['class_count'] = $result->fetch_assoc()['count'];
        
        // Get today's classes count
        $day = date('l');
        $sql = "SELECT COUNT(*) as count 
                FROM class_schedule cs
                JOIN teacher_classes tc ON cs.class_id = tc.class_id
                WHERE tc.teacher_id = $teacher_id AND cs.day_of_week = '$day'";
        $result = $conn->query($sql);
        $response['todays_classes_count'] = $result->fetch_assoc()['count'];
        
        // Get student count
        $sql = "SELECT COUNT(DISTINCT sc.student_id) as count
                FROM student_classes sc
                JOIN teacher_classes tc ON sc.class_id = tc.class_id
                WHERE tc.teacher_id = $teacher_id";
        $result = $conn->query($sql);
        $response['student_count'] = $result->fetch_assoc()['count'];
        
        // Get materials count
        $sql = "SELECT COUNT(*) as count FROM study_materials WHERE teacher_id = $teacher_id";
        $result = $conn->query($sql);
        $response['materials_count'] = $result->fetch_assoc()['count'];
        
        // Get today's schedule
        $sql = "SELECT c.class_name, cs.start_time, cs.end_time, cs.location 
                FROM class_schedule cs
                JOIN classes c ON cs.class_id = c.class_id
                JOIN teacher_classes tc ON cs.class_id = tc.class_id
                WHERE tc.teacher_id = $teacher_id AND cs.day_of_week = '$day'
                ORDER BY cs.start_time";
        $result = $conn->query($sql);
        $response['todays_schedule'] = [];
        while ($row = $result->fetch_assoc()) {
            $response['todays_schedule'][] = $row;
        }
        break;
        
    case 'classes':
        $teacher_id = $_SESSION['user_id'];
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM student_classes WHERE class_id = c.class_id) as student_count
                FROM classes c
                JOIN teacher_classes tc ON c.class_id = tc.class_id
                WHERE tc.teacher_id = $teacher_id
                ORDER BY c.class_name";
        $result = $conn->query($sql);
        $response = [];
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        break;
        
    case 'attendance_form':
        $class_id = $_GET['class_id'];
        $date = $_GET['date'];
        
        // Get class info
        $sql = "SELECT class_name FROM classes WHERE class_id = $class_id";
        $result = $conn->query($sql);
        $response['class_name'] = $result->fetch_assoc()['class_name'];
        
        // Get students in class
        $sql = "SELECT s.student_id, s.full_name 
                FROM students s
                JOIN student_classes sc ON s.student_id = sc.student_id
                WHERE sc.class_id = $class_id
                ORDER BY s.full_name";
        $result = $conn->query($sql);
        $response['students'] = [];
        
        while ($student = $result->fetch_assoc()) {
            // Check if attendance already exists
            $sql = "SELECT status FROM attendance 
                    WHERE student_id = {$student['student_id']} 
                    AND class_id = $class_id 
                    AND date = '$date'";
            $attendance_result = $conn->query($sql);
            $student['attendance'] = $attendance_result->num_rows > 0 ? $attendance_result->fetch_assoc() : null;
            $response['students'][] = $student;
        }
        break;
        
    case 'save_attendance':
        $class_id = $_POST['class_id'];
        $date = $_POST['date'];
        $attendance = $_POST['attendance'];
        
        foreach ($attendance as $student_id => $status) {
            // Check if record exists
            $sql = "SELECT attendance_id FROM attendance 
                    WHERE student_id = $student_id 
                    AND class_id = $class_id 
                    AND date = '$date'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                // Update existing record
                $attendance_id = $result->fetch_assoc()['attendance_id'];
                $sql = "UPDATE attendance SET status = '$status' 
                        WHERE attendance_id = $attendance_id";
            } else {
                // Insert new record
                $sql = "INSERT INTO attendance (student_id, class_id, date, status)
                        VALUES ($student_id, $class_id, '$date', '$status')";
            }
            $conn->query($sql);
        }
        
        $response['success'] = true;
        break;
        
    case 'assessments':
        $teacher_id = $_SESSION['user_id'];
        $sql = "SELECT a.*, c.class_name
                FROM marks a
                JOIN classes c ON a.class_id = c.class_id
                JOIN teacher_classes tc ON a.class_id = tc.class_id
                WHERE tc.teacher_id = $teacher_id
                GROUP BY a.assessment_name, a.class_id, a.assessment_date
                ORDER BY a.assessment_date DESC";
        $result = $conn->query($sql);
        $response = [];
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        break;
        
    case 'get_assessment':
        $assessment_id = $_GET['id'];
        $sql = "SELECT * FROM marks WHERE mark_id = $assessment_id";
        $result = $conn->query($sql);
        $response = $result->fetch_assoc();
        break;
        
    case 'add_assessment':
    case 'update_assessment':
        $class_id = $_POST['class_id'];
        $assessment_name = $conn->real_escape_string($_POST['assessment_name']);
        $assessment_date = $_POST['assessment_date'];
        $total_marks = $_POST['total_marks'];
        $comments = isset($_POST['comments']) ? $conn->real_escape_string($_POST['comments']) : '';
        
        if ($action === 'add_assessment') {
            // Create a new assessment entry
            $sql = "INSERT INTO marks (class_id, teacher_id, assessment_name, assessment_date, total_marks, comments)
                    VALUES ($class_id, {$_SESSION['user_id']}, '$assessment_name', '$assessment_date', $total_marks, '$comments')";
            $conn->query($sql);
            $response['success'] = true;
        } else {
            // Update existing assessment
            $assessment_id = $_POST['assessment_id'];
            $sql = "UPDATE marks SET 
                    class_id = $class_id,
                    assessment_name = '$assessment_name',
                    assessment_date = '$assessment_date',
                    total_marks = $total_marks,
                    comments = '$comments'
                    WHERE mark_id = $assessment_id";
            $conn->query($sql);
            $response['success'] = true;
        }
        break;
        
    case 'marks_form':
        $assessment_id = $_GET['assessment_id'];
        
        // Get assessment details
        $sql = "SELECT m.*, c.class_name
                FROM marks m
                JOIN classes c ON m.class_id = c.class_id
                WHERE m.mark_id = $assessment_id";
        $result = $conn->query($sql);
        $response['assessment'] = $result->fetch_assoc();
        
        // Get students in the class
        $class_id = $response['assessment']['class_id'];
        $sql = "SELECT s.student_id, s.full_name, m.marks_obtained, m.comments
                FROM students s
                JOIN student_classes sc ON s.student_id = sc.student_id
                LEFT JOIN marks m ON s.student_id = m.student_id AND m.mark_id = $assessment_id
                WHERE sc.class_id = $class_id
                ORDER BY s.full_name";
        $result = $conn->query($sql);
        $response['students'] = [];
        
        while ($row = $result->fetch_assoc()) {
            $response['students'][] = [
                'student_id' => $row['student_id'],
                'full_name' => $row['full_name'],
                'mark' => [
                    'marks_obtained' => $row['marks_obtained'],
                    'comments' => $row['comments']
                ]
            ];
        }
        break;
        
    case 'save_marks':
        $assessment_id = $_POST['assessment_id'];
        $marks = $_POST['marks'];
        
        // First get assessment details
        $sql = "SELECT class_id, total_marks FROM marks WHERE mark_id = $assessment_id";
        $result = $conn->query($sql);
        $assessment = $result->fetch_assoc();
        
        foreach ($marks as $mark) {
            $student_id = $mark['student_id'];
            $marks_obtained = $mark['marks_obtained'];
            $comments = $conn->real_escape_string($mark['comments']);
            
            // Check if record exists
            $sql = "SELECT mark_id FROM marks 
                    WHERE student_id = $student_id 
                    AND assessment_name = (
                        SELECT assessment_name FROM marks WHERE mark_id = $assessment_id
                    )
                    AND class_id = {$assessment['class_id']}";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                // Update existing record
                $mark_id = $result->fetch_assoc()['mark_id'];
                $sql = "UPDATE marks SET 
                        marks_obtained = $marks_obtained,
                        comments = '$comments'
                        WHERE mark_id = $mark_id";
            } else {
                // Insert new record
                $sql = "INSERT INTO marks (
                        student_id, class_id, teacher_id, assessment_name, 
                        assessment_date, marks_obtained, total_marks, comments
                    ) SELECT 
                        $student_id, class_id, teacher_id, assessment_name,
                        assessment_date, $marks_obtained, total_marks, '$comments'
                    FROM marks WHERE mark_id = $assessment_id";
            }
            $conn->query($sql);
        }
        
        $response['success'] = true;
        break;
        
    case 'materials':
        $teacher_id = $_SESSION['user_id'];
        $sql = "SELECT sm.*, c.class_name
                FROM study_materials sm
                JOIN classes c ON sm.class_id = c.class_id
                WHERE sm.teacher_id = $teacher_id
                ORDER BY sm.upload_date DESC";
        $result = $conn->query($sql);
        $response = [];
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        break;
        
    case 'save_material':
        $teacher_id = $_SESSION['user_id'];
        $class_id = $_POST['class_id'];
        $title = $conn->real_escape_string($_POST['title']);
        $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
        $material_id = isset($_POST['material_id']) ? $_POST['material_id'] : null;
        
        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'materials/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = 'material_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
                if ($material_id) {
                    // Delete old file if updating
                    $sql = "SELECT file_path FROM study_materials WHERE material_id = $material_id";
                    $result = $conn->query($sql);
                    $old_file = $result->fetch_assoc()['file_path'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }
                
                if ($material_id) {
                    // Update existing material
                    $sql = "UPDATE study_materials SET 
                            class_id = $class_id,
                            title = '$title',
                            description = '$description',
                            file_path = '$destination'
                            WHERE material_id = $material_id";
                } else {
                    // Insert new material
                    $sql = "INSERT INTO study_materials (
                            class_id, teacher_id, title, description, file_path
                        ) VALUES (
                            $class_id, $teacher_id, '$title', '$description', '$destination'
                        )";
                }
                $conn->query($sql);
                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['message'] = "Error uploading file";
            }
        } elseif ($material_id) {
            // Update without changing file
            $sql = "UPDATE study_materials SET 
                    class_id = $class_id,
                    title = '$title',
                    description = '$description'
                    WHERE material_id = $material_id";
            $conn->query($sql);
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = "No file uploaded";
        }
        break;
        
    case 'delete_material':
        $material_id = $_POST['material_id'];
        
        // First get file path to delete the file
        $sql = "SELECT file_path FROM study_materials WHERE material_id = $material_id";
        $result = $conn->query($sql);
        $file_path = $result->fetch_assoc()['file_path'];
        
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete record from database
        $sql = "DELETE FROM study_materials WHERE material_id = $material_id";
        $conn->query($sql);
        
        $response['success'] = true;
        break;
        
    case 'students':
        $teacher_id = $_SESSION['user_id'];
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
        
        if ($class_id) {
            // Students in a specific class
            $sql = "SELECT s.student_id, s.full_name, s.email, s.phone
                    FROM students s
                    JOIN student_classes sc ON s.student_id = sc.student_id
                    JOIN teacher_classes tc ON sc.class_id = tc.class_id
                    WHERE tc.teacher_id = $teacher_id AND sc.class_id = $class_id
                    ORDER BY s.full_name";
        } else {
            // All students across teacher's classes
            $sql = "SELECT s.student_id, s.full_name, s.email, s.phone,
                    GROUP_CONCAT(c.class_name SEPARATOR ', ') as classes
                    FROM students s
                    JOIN student_classes sc ON s.student_id = sc.student_id
                    JOIN classes c ON sc.class_id = c.class_id
                    JOIN teacher_classes tc ON sc.class_id = tc.class_id
                    WHERE tc.teacher_id = $teacher_id
                    GROUP BY s.student_id
                    ORDER BY s.full_name";
        }
        
        $result = $conn->query($sql);
        $response = [];
        while ($row = $result->fetch_assoc()) {
            if ($class_id) {
                $row['classes'] = [$row['class_name']];
            } else {
                $row['classes'] = explode(', ', $row['classes']);
            }
            $response[] = $row;
        }
        break;
        
    default:
        header("HTTP/1.1 400 Bad Request");
        die("Invalid action");
}

header('Content-Type: application/json');
echo json_encode($response);
?>