<?php
include 'db.php';

// Read JSON data
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? '';
$medicine = $data['medicine'] ?? '';
$price = $data['price'] ?? '';
$payment_method = $data['payment_method'] ?? '';
$address = $data['address'] ?? '';

if (empty($user_id) || empty($medicine) || empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$sql = "INSERT INTO orders (user_id, medicine, price, payment_method, address, status) 
        VALUES ('$user_id', '$medicine', '$price', '$payment_method', '$address', 'Confirmed')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'order_id' => mysqli_insert_id($conn)]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order failed']);
}
?>
