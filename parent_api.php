<?php
require_once 'config.php';
checkLogin();

// Only allow parent users
if (!isParent()) {
    header('HTTP/1.1 403 Forbidden');
    die(json_encode(['error' => 'Access denied']));
}

// Verify CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verifyCsrfToken($csrf_token)) {
        header('HTTP/1.1 403 Forbidden');
        die(json_encode(['error' => 'Invalid CSRF token']));
    }
}

// Get student ID
$student_id = $_SESSION['student_id'] ?? null;
if (!$student_id) {
    header('HTTP/1.1 400 Bad Request');
    die(json_encode(['error' => 'No student linked']));
}

// Handle different actions
$action = $_GET['action'] ?? '';
header('Content-Type: application/json');

switch ($action) {
    case 'dashboard':
        // Get attendance rate
        $stmt = $conn->prepare("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present
            FROM attendance WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $attendance = $stmt->get_result()->fetch_assoc();
        $attendance_rate = $attendance['total'] > 0 ? 
            round(($attendance['present'] / $attendance['total']) * 100) : 0;

        // Get average marks
        $stmt = $conn->prepare("SELECT 
            AVG((marks_obtained / total_marks) * 100) as average
            FROM marks WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $marks = $stmt->get_result()->fetch_assoc();
        $average_marks = round($marks['average'] ?? 0);

        // Get recent marks
        $stmt = $conn->prepare("SELECT m.*, c.class_name 
            FROM marks m
            JOIN classes c ON m.class_id = c.class_id
            WHERE m.student_id = ?
            ORDER BY m.assessment_date DESC
            LIMIT 5");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $recent_marks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'attendance_rate' => $attendance_rate,
            'average_marks' => $average_marks,
            'recent_marks' => $recent_marks
        ]);
        break;

    case 'attendance':
        $class_id = $_GET['class_id'] ?? null;
        
        $query = "SELECT a.*, c.class_name 
                 FROM attendance a
                 JOIN classes c ON a.class_id = c.class_id
                 WHERE a.student_id = ?";
        $params = [$student_id];
        $types = "i";
        
        if ($class_id) {
            $query .= " AND a.class_id = ?";
            $params[] = $class_id;
            $types .= "i";
        }
        
        $query .= " ORDER BY a.date DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $attendance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode($attendance);
        break;

    case 'marks':
        $class_id = $_GET['class_id'] ?? null;
        
        $query = "SELECT m.*, c.class_name 
                 FROM marks m
                 JOIN classes c ON m.class_id = c.class_id
                 WHERE m.student_id = ?";
        $params = [$student_id];
        $types = "i";
        
        if ($class_id) {
            $query .= " AND m.class_id = ?";
            $params[] = $class_id;
            $types .= "i";
        }
        
        $query .= " ORDER BY m.assessment_date DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $marks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode($marks);
        break;

    case 'teacher':
        $stmt = $conn->prepare("SELECT DISTINCT t.*, c.class_name
            FROM teacher t
            JOIN classes c ON t.teacher_id = c.teacher_id
            JOIN student_classes sc ON c.class_id = sc.class_id
            WHERE sc.student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $teachers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode($teachers);
        break;

    default:
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid action']);
}
?>