<?php
require_once 'config.php';
checkLogin();

if (isTeacher()) {
    header("Location: teacher_dashboard.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$data = [];

try {
    // Get enrolled classes count
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_classes WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $data['enrolled_classes'] = $stmt->get_result()->fetch_assoc()['count'];

    // Get today's classes count and schedule
    $day = date('l');
    $today = date('Y-m-d');

    $stmt = $conn->prepare("SELECT COUNT(*) as count 
        FROM class_schedule cs
        JOIN student_classes sc ON cs.class_id = sc.class_id
        WHERE sc.student_id = ? AND cs.day_of_week = ?");
    $stmt->bind_param("is", $student_id, $day);
    $stmt->execute();
    $data['todays_classes'] = $stmt->get_result()->fetch_assoc()['count'];

    // Get today's schedule
    $stmt = $conn->prepare("SELECT c.class_name, cs.start_time, cs.end_time, cs.location 
        FROM class_schedule cs
        JOIN classes c ON cs.class_id = c.class_id
        JOIN student_classes sc ON cs.class_id = sc.class_id
        WHERE sc.student_id = ? AND cs.day_of_week = ?
        ORDER BY cs.start_time");
    $stmt->bind_param("is", $student_id, $day);
    $stmt->execute();
    $data['todays_schedule'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get attendance rate
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present
        FROM attendance 
        WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $attendance = $stmt->get_result()->fetch_assoc();
    $data['attendance_rate'] = $attendance['total'] > 0 ? round(($attendance['present'] / $attendance['total']) * 100) : 0;

    // Get average marks
    $stmt = $conn->prepare("SELECT AVG((marks_obtained / total_marks) * 100) as average 
        FROM marks 
        WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $data['average_marks'] = round($stmt->get_result()->fetch_assoc()['average'] ?? 0);

    header('Content-Type: application/json');
    echo json_encode($data);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}
?>