<?php
include 'db.php';

$sql = "SELECT * FROM medicines";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

$medicines = array();
while ($row = mysqli_fetch_assoc($result)) {
    $medicines[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'medicines' => $medicines]);

mysqli_close($conn);
?>
