<?php
require_once 'config.php';

echo "Testing database connection...<br>";

$conn = getDBConnection();
if ($conn) {
    echo "✓ Database connection successful<br>";

    // Check if tables exist
    $tables = ['users', 'products', 'categories', 'cart_items', 'orders', 'order_items', 'quotes', 'wishlists'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "✓ Table '$table' exists<br>";
        } else {
            echo "✗ Table '$table' missing<br>";
        }
    }

    $conn->close();
} else {
    echo "✗ Database connection failed<br>";
}
?>