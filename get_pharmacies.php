<?php
include 'db.php';

$sql = "SELECT * FROM pharmacies ORDER BY name";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        'success' => false, 
        'message' => 'Query failed: ' . mysqli_error($conn)
    ]);
    exit;
}

$pharmacies = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Add distance if it doesn't exist
    if (!isset($row['distance'])) {
        $row['distance'] = '0.5'; // Default distance
    }
    // Add status if it doesn't exist
    if (!isset($row['status'])) {
        $row['status'] = $row['is_open'] ? 'Open now' : 'Closed';
    }
    $pharmacies[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'pharmacies' => $pharmacies
]);

mysqli_close($conn);
?>
