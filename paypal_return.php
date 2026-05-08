<?php
require_once 'functions.php';
require_once 'cart_functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$orderId = intval($_GET['order_id']);
$orderDetails = getOrderDetails($orderId, $_SESSION['user_id']);

if (!$orderDetails) {
    header('Location: orders.php');
    exit();
}

$conn = getDBConnection();
$stmt = $conn->prepare("UPDATE orders SET payment_status = 'completed', status = 'processing' WHERE id = ? AND payment_method = 'paypal'");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$stmt->close();
$conn->close();

$_SESSION['last_order'] = ['order_id' => $orderDetails['id'], 'order_number' => $orderDetails['order_number']];
clearCart();

header('Location: order_confirmation.php');
exit();
?>