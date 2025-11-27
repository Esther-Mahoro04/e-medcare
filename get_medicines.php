<?php
include 'db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$sql = "SELECT m.*, p.name as pharmacy_name 
        FROM medicines m 
        JOIN pharmacies p ON m.pharmacy_id = p.id 
        WHERE m.stock > 0";
$result = mysqli_query($conn, $sql);

$medicines = array();
while($row = mysqli_fetch_assoc($result)) {
    $medicines[] = $row;
}

echo json_encode(['success' => true, 'medicines' => $medicines]);
?>
