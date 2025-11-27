<?php
include 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (isset($_GET['user_id'])) {
    $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
    
    $sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $orders = array();
    while($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    echo json_encode(['success' => true, 'orders' => $orders]);
} else {
    echo json_encode(['success' => false, 'message' => 'No user_id provided']);
}
?>
