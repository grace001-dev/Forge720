<?php
require_once 'config.php';

$conn = getDBConnection();

// Update product images to use placeholder URLs
$updates = [
    ['Custom Metal Gate', 'https://via.placeholder.com/400x300/4B8B3E/FFD700?text=Metal+Gate'],
    ['Steel Staircase', 'https://via.placeholder.com/400x300/4B8B3E/FFD700?text=Steel+Staircase'],
    ['Aluminum Fence', 'https://via.placeholder.com/400x300/4B8B3E/FFD700?text=Aluminum+Fence'],
    ['Brass Handrail', 'https://via.placeholder.com/400x300/4B8B3E/FFD700?text=Brass+Handrail'],
    ['Iron Balcony', 'https://via.placeholder.com/400x300/4B8B3E/FFD700?text=Iron+Balcony']
];

foreach ($updates as $update) {
    $stmt = $conn->prepare("UPDATE products SET image = ? WHERE name = ?");
    $stmt->bind_param("ss", $update[1], $update[0]);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo "Product images updated successfully!";
?>