<?php
include 'db.php';

// Read the JSON request body
$data = json_decode(file_get_contents("php://input"), true);

$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';

if (empty($phone) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Phone and password required']);
    exit;
}

// Check if user exists
$query = "SELECT * FROM users WHERE phone = '$phone'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = mysqli_fetch_assoc($result);

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    exit;
}

// Success
echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'phone' => $user['phone'],
        'email' => $user['email']
    ]
]);
?>

