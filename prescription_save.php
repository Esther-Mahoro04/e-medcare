<?php
// prescription_save.php - Enhanced version with file upload handling

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

require_once 'db.php';

function jsonResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Handle both file uploads AND JSON data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Check if this is a FILE upload (camera photo)
if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] === UPLOAD_ERR_OK) {
    
    $file = $_FILES['prescription'];
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        jsonResponse(false, 'Invalid file type. Only images are allowed.');
    }
    
    // Validate file size (5MB max)
    $max_size = 5 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        jsonResponse(false, 'File is too large. Maximum size is 5MB.');
    }
    
    // Create uploads directory
    $upload_dir = 'uploads/prescriptions/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            jsonResponse(false, 'Failed to create upload directory');
        }
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        $mime_extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $extension = $mime_extensions[$file_type] ?? 'jpg';
    }
    
    $filename = 'prescription_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        jsonResponse(false, 'Failed to save uploaded file');
    }
    
    // Save to database
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    
    try {
        $stmt = $conn->prepare("INSERT INTO prescriptions (user_id, image_path, upload_date, status) VALUES (?, ?, NOW(), 'pending')");
        
        if (!$stmt) {
            unlink($filepath);
            jsonResponse(false, 'Database error: ' . $conn->error);
        }
        
        $stmt->bind_param("is", $user_id, $filename);
        
        if ($stmt->execute()) {
            $prescription_id = $stmt->insert_id;
            
            jsonResponse(true, 'Prescription photo uploaded successfully!', [
                'prescription_id' => $prescription_id,
                'filename' => $filename,
                'upload_date' => date('Y-m-d H:i:s')
            ]);
        } else {
            unlink($filepath);
            jsonResponse(false, 'Failed to save to database: ' . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        jsonResponse(false, 'Error: ' . $e->getMessage());
    }
    
} else {
    // Handle JSON data (for manual form entry)
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        jsonResponse(false, 'No data received');
    }
    
    $user_id = mysqli_real_escape_string($conn, $data['user_id']);
    $medication = mysqli_real_escape_string($conn, $data['medication']);
    $quantity = mysqli_real_escape_string($conn, $data['quantity']);
    
    $sql = "INSERT INTO prescriptions (user_id, medication, quantity, upload_date, status) 
            VALUES ('$user_id', '$medication', '$quantity', NOW(), 'pending')";
    
    if (mysqli_query($conn, $sql)) {
        $prescription_id = mysqli_insert_id($conn);
        jsonResponse(true, 'Prescription saved successfully!', [
            'prescription_id' => $prescription_id
        ]);
    } else {
        jsonResponse(false, 'Failed to save prescription');
    }
}

$conn->close();
?>
