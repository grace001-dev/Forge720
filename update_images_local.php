<?php
require_once 'config.php';

$conn = getDBConnection();

$updates = [
    ['Custom Metal Gate', 'gate.jpg'],
    ['Steel Staircase', 'staircase.jpg'],
    ['Aluminum Fence', 'aluminum-fence.jpg'],
    ['Brass Handrail', 'handrail.jpg'],
    ['Iron Balcony', 'balcony.jpg'],
];

foreach ($updates as $update) {
    $stmt = $conn->prepare("UPDATE products SET image = ? WHERE name = ?");
    $stmt->bind_param("ss", $update[1], $update[0]);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
echo "Local product image paths have been updated in the database.\n";
?>