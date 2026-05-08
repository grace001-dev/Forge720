<?php
require_once 'functions.php';
session_start();

// Redirect if not logged in or no order confirmation
if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_order'])) {
    header('Location: index.php');
    exit();
}

$orderInfo = $_SESSION['last_order'];
$orderDetails = getOrderDetails($orderInfo['order_id'], $_SESSION['user_id']);

// Clear the session order info
unset($_SESSION['last_order']);

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
    <title>Order Confirmation - Forge 720</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Forge 720</div>
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
                <li><a href="orders.php">Orders</a></li>
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
            <div class="confirmation-container">
                <div class="confirmation-header">
                    <div class="success-icon">✓</div>
                    <h2>Order Confirmed!</h2>
                    <p>Thank you for your order. We've received your order and will process it shortly.</p>
                </div>

                <div class="order-details-card">
                    <h3>Order Details</h3>
                    <div class="order-info-grid">
                        <div>
                            <strong>Order Number:</strong> <?php echo htmlspecialchars($orderDetails['order_number']); ?>
                        </div>
                        <div>
                            <strong>Order Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($orderDetails['created_at'])); ?>
                        </div>
                        <div>
                            <strong>Status:</strong>
                            <span class="status-badge status-<?php echo strtolower($orderDetails['status']); ?>">
                                <?php echo ucfirst($orderDetails['status']); ?>
                            </span>
                        </div>
                        <div>
                            <strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $orderDetails['payment_method'])); ?>
                        </div>
                    </div>
                </div>

                <div class="order-items-card">
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
                                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    <p>Unit Price: $<?php echo number_format($item['unit_price'], 2); ?></p>

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

                <div class="order-totals-card">
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

                <div class="shipping-info-card">
                    <h3>Shipping Information</h3>
                    <div class="address-info">
                        <div>
                            <strong>Shipping Address:</strong><br>
                            <?php echo htmlspecialchars($orderDetails['customer_name']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['shipping_address']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['shipping_city']); ?>, <?php echo htmlspecialchars($orderDetails['shipping_state']); ?> <?php echo htmlspecialchars($orderDetails['shipping_zip']); ?><br>
                            <?php echo htmlspecialchars($orderDetails['shipping_country']); ?>
                        </div>
                        <?php if ($orderDetails['customer_phone']): ?>
                            <div>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($orderDetails['customer_phone']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($orderDetails['special_instructions']): ?>
                        <div class="special-instructions">
                            <strong>Special Instructions:</strong><br>
                            <?php echo htmlspecialchars($orderDetails['special_instructions']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="next-steps">
                    <h3>What Happens Next?</h3>
                    <div class="steps-grid">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Order Processing</h4>
                                <p>We'll review your order and prepare your custom metalwork pieces.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Production</h4>
                                <p>Our skilled craftsmen will create your items with precision and care.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Shipping</h4>
                                <p>We'll ship your order and provide tracking information.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Delivery</h4>
                                <p>You'll receive your beautiful custom metalwork pieces.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="confirmation-actions">
                    <a href="orders.php" class="btn">View All Orders</a>
                    <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                </div>

                <div class="contact-info">
                    <p>Questions about your order? <a href="contact.php">Contact us</a> or call (+254) 792204330</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>