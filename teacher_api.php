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
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM teacher_classes WHERE teacher_id = ?");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $response['class_count'] = $stmt->get_result()->fetch_assoc()['count'];
 
        // Get today's classes count
        $day = date('l');
        $stmt = $conn->prepare("SELECT COUNT(*) as count 
                FROM class_schedule cs
                JOIN teacher_classes tc ON cs.class_id = tc.class_id
                WHERE tc.teacher_id = ? AND cs.day_of_week = ?");
        $stmt->bind_param("is", $teacher_id, $day);
        $stmt->execute();
        $response['todays_classes_count'] = $stmt->get_result()->fetch_assoc()['count'];
 
        // Get student count
        $stmt = $conn->prepare("SELECT COUNT(DISTINCT sc.student_id) as count
                FROM student_classes sc
                JOIN teacher_classes tc ON sc.class_id = tc.class_id
                WHERE tc.teacher_id = ?");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $response['student_count'] = $stmt->get_result()->fetch_assoc()['count'];
 
        // Get materials count
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM study_materials WHERE teacher_id = ?");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $response['materials_count'] = $stmt->get_result()->fetch_assoc()['count'];
 
        // Get today's schedule
        $stmt = $conn->prepare("SELECT c.class_name, cs.start_time, cs.end_time, cs.location 
                FROM class_schedule cs
                JOIN classes c ON cs.class_id = c.class_id
                JOIN teacher_classes tc ON cs.class_id = tc.class_id
                WHERE tc.teacher_id = ? AND cs.day_of_week = ?
                ORDER BY cs.start_time");
        $stmt->bind_param("is", $teacher_id, $day);
        $stmt->execute();
        $response['todays_schedule'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        break;
 
    case 'classes':
        $teacher_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT c.*, 
                (SELECT COUNT(*) FROM student_classes WHERE class_id = c.class_id) as student_count
                FROM classes c
                JOIN teacher_classes tc ON c.class_id = tc.class_id
                WHERE tc.teacher_id = ?
                ORDER BY c.class_name");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        break;
 
    case 'attendance_form':
        $class_id = $_GET['class_id'];
        $date = $_GET['date'];
 
        // Get class info
        $stmt = $conn->prepare("SELECT class_name FROM classes WHERE class_id = ?");
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $response['class_name'] = $stmt->get_result()->fetch_assoc()['class_name'];
 
        // Get students in class
        $stmt = $conn->prepare("SELECT s.student_id, s.full_name 
                FROM students s
                JOIN student_classes sc ON s.student_id = sc.student_id
                WHERE sc.class_id = ?
                ORDER BY s.full_name");
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
        // PERFORMANCE FIX (was N+1): previously this ran one attendance query PER STUDENT
        // inside the loop below. Instead, fetch attendance for every student in this
        // class+date in a single query, then map it in PHP - turns N+1 queries into 2.
        $stmt = $conn->prepare("SELECT student_id, status FROM attendance 
                WHERE class_id = ? AND date = ?");
        $stmt->bind_param("is", $class_id, $date);
        $stmt->execute();
        $attendanceRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
        $attendanceByStudent = [];
        foreach ($attendanceRows as $row) {
            $attendanceByStudent[$row['student_id']] = ['status' => $row['status']];
        }
 
        $response['students'] = [];
        foreach ($students as $student) {
            $student['attendance'] = $attendanceByStudent[$student['student_id']] ?? null;
            $response['students'][] = $student;
        }
        break;
 
    case 'save_attendance':
        $class_id = $_POST['class_id'];
        $date = $_POST['date'];
        $attendance = $_POST['attendance'];
 
        // Prepared once, executed per student - fixes SQL injection on student_id/status
        // and avoids re-parsing the query on every iteration (was a fresh raw query each time).
        $checkStmt = $conn->prepare("SELECT attendance_id FROM attendance 
                WHERE student_id = ? AND class_id = ? AND date = ?");
        $updateStmt = $conn->prepare("UPDATE attendance SET status = ? WHERE attendance_id = ?");
        $insertStmt = $conn->prepare("INSERT INTO attendance (student_id, class_id, date, status)
                VALUES (?, ?, ?, ?)");
 
        foreach ($attendance as $student_id => $status) {
            $checkStmt->bind_param("iis", $student_id, $class_id, $date);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
 
            if ($existing) {
                $attendance_id = $existing['attendance_id'];
                $updateStmt->bind_param("si", $status, $attendance_id);
                $updateStmt->execute();
            } else {
                $insertStmt->bind_param("iiss", $student_id, $class_id, $date, $status);
                $insertStmt->execute();
            }
        }
 
        $response['success'] = true;
        break;
 
    case 'assessments':
        $teacher_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT a.*, c.class_name
                FROM marks a
                JOIN classes c ON a.class_id = c.class_id
                JOIN teacher_classes tc ON a.class_id = tc.class_id
                WHERE tc.teacher_id = ?
                GROUP BY a.assessment_name, a.class_id, a.assessment_date
                ORDER BY a.assessment_date DESC");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        break;
 
    case 'get_assessment':
        $assessment_id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM marks WHERE mark_id = ?");
        $stmt->bind_param("i", $assessment_id);
        $stmt->execute();
        $response = $stmt->get_result()->fetch_assoc();
        break;
 
    case 'add_assessment':
    case 'update_assessment':
        $class_id = $_POST['class_id'];
        $assessment_name = $_POST['assessment_name'];
        $assessment_date = $_POST['assessment_date'];
        $total_marks = $_POST['total_marks'];
        $comments = $_POST['comments'] ?? '';
        $teacher_id = $_SESSION['user_id'];
 
        if ($action === 'add_assessment') {
            $stmt = $conn->prepare("INSERT INTO marks 
                    (class_id, teacher_id, assessment_name, assessment_date, total_marks, comments)
                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissis", $class_id, $teacher_id, $assessment_name, $assessment_date, $total_marks, $comments);
            $stmt->execute();
            $response['success'] = true;
        } else {
            $assessment_id = $_POST['assessment_id'];
            $stmt = $conn->prepare("UPDATE marks SET 
                    class_id = ?, assessment_name = ?, assessment_date = ?,
                    total_marks = ?, comments = ?
                    WHERE mark_id = ?");
            $stmt->bind_param("issisi", $class_id, $assessment_name, $assessment_date, $total_marks, $comments, $assessment_id);
            $stmt->execute();
            $response['success'] = true;
        }
        break;
 
    case 'marks_form':
        $assessment_id = $_GET['assessment_id'];
 
        // Get assessment details
        $stmt = $conn->prepare("SELECT m.*, c.class_name
                FROM marks m
                JOIN classes c ON m.class_id = c.class_id
                WHERE m.mark_id = ?");
        $stmt->bind_param("i", $assessment_id);
        $stmt->execute();
        $response['assessment'] = $stmt->get_result()->fetch_assoc();
 
        // Get students in the class
        $class_id = $response['assessment']['class_id'];
        $stmt = $conn->prepare("SELECT s.student_id, s.full_name, m.marks_obtained, m.comments
                FROM students s
                JOIN student_classes sc ON s.student_id = sc.student_id
                LEFT JOIN marks m ON s.student_id = m.student_id AND m.mark_id = ?
                WHERE sc.class_id = ?
                ORDER BY s.full_name");
        $stmt->bind_param("ii", $assessment_id, $class_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
 
        $response['students'] = [];
        foreach ($rows as $row) {
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
        $stmt = $conn->prepare("SELECT class_id, teacher_id, assessment_name, assessment_date, total_marks 
                FROM marks WHERE mark_id = ?");
        $stmt->bind_param("i", $assessment_id);
        $stmt->execute();
        $assessment = $stmt->get_result()->fetch_assoc();
 
        $checkStmt = $conn->prepare("SELECT mark_id FROM marks 
                WHERE student_id = ? AND assessment_name = ? AND class_id = ?");
        $updateStmt = $conn->prepare("UPDATE marks SET marks_obtained = ?, comments = ? WHERE mark_id = ?");
        $insertStmt = $conn->prepare("INSERT INTO marks 
                (student_id, class_id, teacher_id, assessment_name, assessment_date, marks_obtained, total_marks, comments)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
 
        foreach ($marks as $mark) {
            $student_id = $mark['student_id'];
            $marks_obtained = $mark['marks_obtained'];
            $comments = $mark['comments'];
 
            $checkStmt->bind_param("isi", $student_id, $assessment['assessment_name'], $assessment['class_id']);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
 
            if ($existing) {
                $mark_id = $existing['mark_id'];
                $updateStmt->bind_param("dsi", $marks_obtained, $comments, $mark_id);
                $updateStmt->execute();
            } else {
                $insertStmt->bind_param(
                    "iiissdds",
                    $student_id, $assessment['class_id'], $assessment['teacher_id'],
                    $assessment['assessment_name'], $assessment['assessment_date'],
                    $marks_obtained, $assessment['total_marks'], $comments
                );
                $insertStmt->execute();
            }
        }
 
        $response['success'] = true;
        break;
 
    case 'materials':
        $teacher_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT sm.*, c.class_name
                FROM study_materials sm
                JOIN classes c ON sm.class_id = c.class_id
                WHERE sm.teacher_id = ?
                ORDER BY sm.upload_date DESC");
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        break;
 
    case 'save_material':
        $teacher_id = $_SESSION['user_id'];
        $class_id = $_POST['class_id'];
        $title = $_POST['title'];
        $description = $_POST['description'] ?? '';
        $material_id = $_POST['material_id'] ?? null;
 
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
                    $stmt = $conn->prepare("SELECT file_path FROM study_materials WHERE material_id = ?");
                    $stmt->bind_param("i", $material_id);
                    $stmt->execute();
                    $old_file = $stmt->get_result()->fetch_assoc()['file_path'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
 
                    $stmt = $conn->prepare("UPDATE study_materials SET 
                            class_id = ?, title = ?, description = ?, file_path = ?
                            WHERE material_id = ?");
                    $stmt->bind_param("isssi", $class_id, $title, $description, $destination, $material_id);
                    $stmt->execute();
                } else {
                    $stmt = $conn->prepare("INSERT INTO study_materials 
                            (class_id, teacher_id, title, description, file_path)
                            VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iisss", $class_id, $teacher_id, $title, $description, $destination);
                    $stmt->execute();
                }
                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['message'] = "Error uploading file";
            }
        } elseif ($material_id) {
            $stmt = $conn->prepare("UPDATE study_materials SET 
                    class_id = ?, title = ?, description = ?
                    WHERE material_id = ?");
            $stmt->bind_param("issi", $class_id, $title, $description, $material_id);
            $stmt->execute();
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = "No file uploaded";
        }
        break;
 
    case 'delete_material':
        $material_id = $_POST['material_id'];
 
        $stmt = $conn->prepare("SELECT file_path FROM study_materials WHERE material_id = ?");
        $stmt->bind_param("i", $material_id);
        $stmt->execute();
        $file_path = $stmt->get_result()->fetch_assoc()['file_path'];
 
        if (file_exists($file_path)) {
            unlink($file_path);
        }
 
        $stmt = $conn->prepare("DELETE FROM study_materials WHERE material_id = ?");
        $stmt->bind_param("i", $material_id);
        $stmt->execute();
 
        $response['success'] = true;
        break;
 
    case 'students':
        $teacher_id = $_SESSION['user_id'];
        $class_id = $_GET['class_id'] ?? null;
 
        if ($class_id) {
            $stmt = $conn->prepare("SELECT s.student_id, s.full_name, s.email, s.phone
                    FROM students s
                    JOIN student_classes sc ON s.student_id = sc.student_id
                    JOIN teacher_classes tc ON sc.class_id = tc.class_id
                    WHERE tc.teacher_id = ? AND sc.class_id = ?
                    ORDER BY s.full_name");
            $stmt->bind_param("ii", $teacher_id, $class_id);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($rows as $row) {
                $row['classes'] = [$row['class_name'] ?? null];
                $response[] = $row;
            }
        } else {
            $stmt = $conn->prepare("SELECT s.student_id, s.full_name, s.email, s.phone,
                    GROUP_CONCAT(c.class_name SEPARATOR ', ') as classes
                    FROM students s
                    JOIN student_classes sc ON s.student_id = sc.student_id
                    JOIN classes c ON sc.class_id = c.class_id
                    JOIN teacher_classes tc ON sc.class_id = tc.class_id
                    WHERE tc.teacher_id = ?
                    GROUP BY s.student_id
                    ORDER BY s.full_name");
            $stmt->bind_param("i", $teacher_id);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($rows as $row) {
                $row['classes'] = explode(', ', $row['classes']);
                $response[] = $row;
            }
        }
        break;
 
    default:
        header("HTTP/1.1 400 Bad Request");
        die("Invalid action");
}
 
header('Content-Type: application/json');
echo json_encode($response);
?>
