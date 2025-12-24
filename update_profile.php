<?php
require_once 'config.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$response = ['success' => false, 'message' => ''];

try {
    // Verify CSRF token
    $csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verifyCsrfToken($csrf_token)) {
        throw new Exception("Invalid CSRF token");
    }

    // Validate required fields
    $required = ['fullname', 'email'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Handle file upload securely
    $profile_pic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['profile_pic']['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed.");
        }
        
        // Check file size (max 2MB)
        if ($_FILES['profile_pic']['size'] > 2097152) {
            throw new Exception("File too large. Maximum size is 2MB.");
        }
        
        // Create upload directory if not exists
        $uploadDir = 'uploads/profile_pics/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = $user_type . '_' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
            throw new Exception("Failed to upload file");
        }
        $profile_pic = $destination;
    }

    // Prepare update data
    $table = $user_type === 'teacher' ? 'teacher' : 'student';
    $id_field = $table . '_id';
    
    $fields = [
        'full_name' => sanitizeInput($_POST['fullname']),
        'email' => sanitizeInput($_POST['email']),
        'phone' => isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : null
    ];
    
    if ($profile_pic) {
        $fields['profile_pic'] = $profile_pic;
    }
    
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 8) {
            throw new Exception("Password must be at least 8 characters");
        }
        $fields['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    
    // Build dynamic update query
    $updates = [];
    $types = '';
    $values = [];
    
    foreach ($fields as $field => $value) {
        $updates[] = "$field = ?";
        $types .= is_int($value) ? 'i' : 's';
        $values[] = $value;
    }
    
    $values[] = $user_id;
    $types .= 'i';
    
    $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $id_field = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response = array_merge($response, $fields);
        if ($profile_pic) {
            $response['profile_pic'] = $profile_pic;
        }
    } else {
        throw new Exception("Error updating profile: " . $stmt->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>