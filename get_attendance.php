<?php
require_once 'config.php';
checkLogin();

$student_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT a.*, c.class_name 
        FROM attendance a
        JOIN classes c ON a.class_id = c.class_id
        WHERE a.student_id = ?
        ORDER BY c.class_name, a.date DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    
    $attendance = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($attendance);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}
?>