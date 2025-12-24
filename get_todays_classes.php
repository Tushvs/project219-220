<?php
require_once 'config.php';
checkLogin();

$student_id = $_SESSION['user_id'];
$day = date('l');

try {
    $stmt = $conn->prepare("SELECT c.class_name, cs.start_time, cs.end_time, cs.location 
        FROM class_schedule cs
        JOIN classes c ON cs.class_id = c.class_id
        JOIN student_classes sc ON cs.class_id = sc.class_id
        WHERE sc.student_id = ? AND cs.day_of_week = ?
        ORDER BY cs.start_time");
    $stmt->bind_param("is", $student_id, $day);
    $stmt->execute();
    
    $classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($classes);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}
?>