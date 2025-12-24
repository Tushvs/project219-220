<?php
require_once 'config.php';
checkLogin();

$student_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT m.*, c.class_name 
        FROM marks m
        JOIN classes c ON m.class_id = c.class_id
        WHERE m.student_id = ?
        ORDER BY c.class_name, m.assessment_date DESC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    
    $marks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($marks);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}
?>