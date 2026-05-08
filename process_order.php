<?php
require_once 'functions.php';
require_once 'cart_functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit();
}

// Get cart items
$cartItems = getCartItems();
if (empty($cartItems)) {
    header('Location: view_cart.php');
    exit();
}

// Validate form data
$requiredFields = ['customer_name', 'customer_email', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['checkout_error'] = 'Please fill in all required fields.';
        header('Location: checkout.php');
        exit();
    }
}

// Prepare order data
$cartTotal = getCartTotal();
$shippingCost = ($_POST['shipping_method'] === 'express') ? 50.00 : 25.00;

$orderData = [
    'user_id' => $_SESSION['user_id'],
    'subtotal' => $cartTotal,
    'payment_method' => $_POST['payment_method'],
    'customer_name' => sanitizeInput($_POST['customer_name']),
    'customer_email' => sanitizeInput($_POST['customer_email']),
    'customer_phone' => sanitizeInput($_POST['customer_phone'] ?? ''),
    'shipping_address' => sanitizeInput($_POST['shipping_address']),
    'shipping_city' => sanitizeInput($_POST['shipping_city']),
    'shipping_state' => sanitizeInput($_POST['shipping_state']),
    'shipping_zip' => sanitizeInput($_POST['shipping_zip']),
    'shipping_country' => sanitizeInput($_POST['shipping_country']),
    'shipping_method' => $_POST['shipping_method'],
    'special_instructions' => sanitizeInput($_POST['special_instructions'] ?? ''),
    'items' => []
];

// Handle billing address
if (isset($_POST['same_as_shipping']) && $_POST['same_as_shipping'] == 'on') {
    $orderData['billing_address'] = $orderData['shipping_address'];
    $orderData['billing_city'] = $orderData['shipping_city'];
    $orderData['billing_state'] = $orderData['shipping_state'];
    $orderData['billing_zip'] = $orderData['shipping_zip'];
    $orderData['billing_country'] = $orderData['shipping_country'];
} else {
    $orderData['billing_address'] = sanitizeInput($_POST['billing_address'] ?? '');
    $orderData['billing_city'] = sanitizeInput($_POST['billing_city'] ?? '');
    $orderData['billing_state'] = sanitizeInput($_POST['billing_state'] ?? '');
    $orderData['billing_zip'] = sanitizeInput($_POST['billing_zip'] ?? '');
    $orderData['billing_country'] = sanitizeInput($_POST['billing_country'] ?? '');
}

// Prepare order items
foreach ($cartItems as $item) {
    $orderData['items'][] = [
        'product_id' => $item['product_id'],
        'product_name' => $item['name'],
        'quantity' => $item['quantity'],
        'unit_price' => $item['price'],
        'customization_options' => $item['customization_options'] ? json_decode($item['customization_options'], true) : null
    ];
}

// Create the order
$result = createOrder($orderData);

if ($result) {
    // Store order details in session for confirmation page
    $_SESSION['last_order'] = $result;

    if ($orderData['payment_method'] === 'paypal') {
        $paypalBusinessEmail = urlencode(PAYPAL_BUSINESS_EMAIL);
        $orderName = urlencode('Forge 720 Order ' . $result['order_number']);
        $amount = number_format($orderData['subtotal'] + $shippingCost, 2, '.', '');
        $returnUrl = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paypal_return.php?order_id=' . $result['order_id']);
        $cancelUrl = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paypal_cancel.php?order_id=' . $result['order_id']);

        $paypalUrl = 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick'
            . '&business=' . $paypalBusinessEmail
            . '&item_name=' . $orderName
            . '&amount=' . $amount
            . '&currency_code=USD'
            . '&return=' . $returnUrl
            . '&cancel_return=' . $cancelUrl
            . '&custom=' . $result['order_id'];

        $_SESSION['paypal_order_id'] = $result['order_id'];
        header('Location: ' . $paypalUrl);
        exit();
    }

    // Clear the cart for non-PayPal orders
    clearCart();

    // Store order details in session for confirmation page
    $_SESSION['last_order'] = $result;

    // Redirect to order confirmation
    header('Location: order_confirmation.php');
    exit();
} else {
    $_SESSION['checkout_error'] = 'There was an error processing your order. Please try again.';
    header('Location: checkout.php');
    exit();
}
?>