<?php
require_once 'functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userOrders = getUserOrders($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Forge720</title>
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
            <h2>My Orders</h2>

            <?php if (empty($userOrders)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <h3>No orders yet</h3>
                    <p>You haven't placed any orders yet. Browse our products and place your first order!</p>
                    <a href="products.php" class="btn">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="orders-container">
                    <?php foreach ($userOrders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                                    <p class="order-date"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-summary">
                                    <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                                    <p><strong>Items:</strong> <?php echo $order['item_count']; ?> item(s)</p>
                                    <p><strong>Payment:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                                </div>

                                <div class="order-actions">
                                    <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn btn-secondary">View Details</a>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-outline" onclick="cancelOrder(<?php echo $order['id']; ?>)">Cancel Order</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                return;
            }

            // In a real application, you'd make an AJAX call to cancel the order
            alert('Order cancellation is not yet implemented. Please contact customer service.');
        }
    </script>
</body>
</html>