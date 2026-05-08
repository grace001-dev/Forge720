<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'forge720');

define('WHATSAPP_PHONE', '254792204330'); // WhatsApp phone number in international format without +
define('PAYPAL_BUSINESS_EMAIL', 'rugurugrace75@gmail.com'); // Replace with your PayPal business email

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>