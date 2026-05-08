<?php
require_once 'functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$orderDetails = getOrderDetails($orderId, $_SESSION['user_id']);

if (!$orderDetails) {
    header('Location: orders.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo htmlspecialchars($orderDetails['order_number']); ?> - Forge720</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Forge720</div>
            <button class="menu-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="orders.php" class="active">Orders</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li>
                    <a href="view_cart.php" class="cart-link">
                        Cart (<span id="cart-count">0</span>)
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main style="padding-top: 100px;">
        <section class="section">
            <div style="max-width: 1000px; margin: 0 auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2>Order #<?php echo htmlspecialchars($orderDetails['order_number']); ?></h2>
                    <a href="orders.php" class="btn btn-secondary">← Back to Orders</a>
                </div>

                <!-- Order Status -->
                <div class="order-details-card" style="margin-bottom: 2rem;">
                    <h3>Order Status</h3>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p><strong>Status:</strong>
                                <span class="status-badge status-<?php echo strtolower($orderDetails['status']); ?>">
                                    <?php echo ucfirst($orderDetails['status']); ?>
                                </span>
                            </p>
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($orderDetails['created_at'])); ?></p>
                            <p><strong>Payment:</strong> <?php echo ucfirst(str_replace('_', ' ', $orderDetails['payment_method'])); ?>
                                <span class="status-badge status-<?php echo strtolower($orderDetails['payment_status']); ?>">
                                    <?php echo ucfirst($orderDetails['payment_status']); ?>
                                </span>
                            </p>
                        </div>
                        <?php if ($orderDetails['tracking_number']): ?>
                            <div>
                                <p><strong>Tracking Number:</strong></p>
                                <p style="font-family: monospace; background: #f5f5f5; padding: 0.5rem; border-radius: 4px;">
                                    <?php echo htmlspecialchars($orderDetails['tracking_number']); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="order-items-card" style="margin-bottom: 2rem;">
                    <h3>Order Items</h3>
                    <div class="order-items-list">
                        <?php foreach ($orderDetails['items'] as $item): ?>
                            <div class="order-item">
                                <div class="order-item-image">
                                    <img src="<?php echo htmlspecialchars(getImageUrl($item['image'])); ?>"
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                </div>
                                <div class="order-item-details">
                                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['unit_price'], 2); ?></p>

                                    <?php if ($item['customization_options']): ?>
                                        <div class="customization-options">
                                            <h4>Customizations:</h4>
                                            <?php
                                            $customizations = json_decode($item['customization_options'], true);
                                            foreach ($customizations as $key => $value):
                                            ?>
                                                <p><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-total">
                                    $<?php echo number_format($item['subtotal'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="order-totals-card" style="margin-bottom: 2rem;">
                    <h3>Order Summary</h3>
                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($orderDetails['total_amount'] - $orderDetails['shipping_cost'], 2); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Shipping (<?php echo ucfirst($orderDetails['shipping_method']); ?>):</span>
                            <span>$<?php echo number_format($orderDetails['shipping_cost'], 2); ?></span>
                        </div>
                        <div class="total-row final-total">
                            <span><strong>Total:</strong></span>
                            <span><strong>$<?php echo number_format($orderDetails['total_amount'], 2); ?></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Shipping & Billing Info -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="shipping-info-card">
                        <h3>Shipping Address</h3>
                        <address style="font-style: normal;">
                            <?php echo htmlspecialchars($orderDetails['customer_name']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['shipping_address']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['shipping_city']); ?>, <?php echo htmlspecialchars($orderDetails['shipping_state']); ?> <?php echo htmlspecialchars($orderDetails['shipping_zip']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['shipping_country']); ?>
                        </address>
                        <?php if ($orderDetails['customer_phone']): ?>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($orderDetails['customer_phone']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="shipping-info-card">
                        <h3>Billing Address</h3>
                        <address style="font-style: normal;">
                            <?php echo htmlspecialchars($orderDetails['customer_name']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['billing_address'] ?: $orderDetails['shipping_address']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['billing_city'] ?: $orderDetails['shipping_city']); ?>, <?php echo htmlspecialchars($orderDetails['billing_state'] ?: $orderDetails['shipping_state']); ?> <?php echo htmlspecialchars($orderDetails['billing_zip'] ?: $orderDetails['shipping_zip']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['billing_country'] ?: $orderDetails['shipping_country']); ?>
                        </address>
                    </div>
                </div>

                <?php if ($orderDetails['special_instructions']): ?>
                    <div class="shipping-info-card" style="margin-top: 2rem;">
                        <h3>Special Instructions</h3>
                        <p><?php echo htmlspecialchars($orderDetails['special_instructions']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>