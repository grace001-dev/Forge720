<?php
require_once 'functions.php';
require_once 'cart_functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'failed', status = 'cancelled' WHERE id = ? AND payment_method = 'paypal'");
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

$_SESSION['checkout_error'] = 'PayPal payment was cancelled. You can try again or choose another payment method.';
header('Location: checkout.php');
exit();
?>