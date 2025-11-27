<?php
include 'db.php';

// Read JSON data
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? '';
$phone = $data['phone'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($name) || empty($phone) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields required']);
    exit;
}

// Check if user exists
$check = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone' OR email = '$email'");
if (mysqli_num_rows($check) > 0) {
    echo json_encode(['success' => false, 'message' => 'Phone or email already registered']);
    exit;
}

// Insert user
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (name, phone, email, password) VALUES ('$name', '$phone', '$email', '$hashed_password')";

if (mysqli_query($conn, $sql)) {
    $user_id = mysqli_insert_id($conn);
    echo json_encode([
        'success' => true,
        'user' => ['id' => $user_id, 'name' => $name, 'phone' => $phone, 'email' => $email]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
?>
