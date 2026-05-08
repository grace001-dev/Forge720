<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$orders = getAllOrders();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = intval($_POST['order_id']);
    $status = sanitizeInput($_POST['status']);

    if (updateOrderStatus($orderId, $status)) {
        $message = 'Order status updated successfully!';
        $orders = getAllOrders(); // Refresh the list
    } else {
        $message = 'Error updating order status.';
    }
}

// Get order details for modal
$orderDetails = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $orderDetails = getOrderDetails(intval($_GET['view']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin - Forge 720</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: #343a40;
            color: white;
            padding: 1rem;
        }

        .admin-sidebar h3 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #495057;
        }

        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .admin-sidebar li {
            margin-bottom: 0.5rem;
        }

        .admin-sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: #495057;
            color: white;
        }

        .admin-main {
            padding: 2rem;
            background: #f8f9fa;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .orders-table th,
        .orders-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .orders-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-view {
            padding: 0.25rem 0.5rem;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .btn-update {
            padding: 0.25rem 0.5rem;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .btn-view:hover {
            background: #0056b3;
        }

        .btn-update:hover {
            background: #218838;
        }

        .status-select {
            padding: 0.25rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .order-details h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-group {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
        }

        .info-group h4 {
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .order-items {
            margin-top: 2rem;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items th,
        .order-items td {
            padding: 0.5rem;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }

        .order-items th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="orders.php" class="active">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="quotes.php">Quotes</a></li>
                <li><a href="content.php">Content</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Manage Orders</h1>
                <a href="index.php" class="btn-update" style="background: #6c757d;">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name'] ?? $order['username'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td class="actions">
                                <a href="?view=<?php echo $order['id']; ?>" class="btn-view">View</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal" <?php echo $orderDetails ? 'style="display: block;"' : ''; ?>>
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <?php if ($orderDetails): ?>
                <div class="order-details">
                    <h3>Order #<?php echo htmlspecialchars($orderDetails['order_number']); ?></h3>

                    <div class="order-info">
                        <div class="info-group">
                            <h4>Customer Information</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($orderDetails['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['customer_email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($orderDetails['customer_phone'] ?? 'N/A'); ?></p>
                        </div>

                        <div class="info-group">
                            <h4>Shipping Address</h4>
                            <p><?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?></p>
                            <p><?php echo htmlspecialchars($orderDetails['shipping_city']); ?>, <?php echo htmlspecialchars($orderDetails['shipping_state']); ?> <?php echo htmlspecialchars($orderDetails['shipping_zip']); ?></p>
                            <p><?php echo htmlspecialchars($orderDetails['shipping_country']); ?></p>
                        </div>

                        <div class="info-group">
                            <h4>Order Details</h4>
                            <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($orderDetails['status']); ?>"><?php echo ucfirst($orderDetails['status']); ?></span></p>
                            <p><strong>Payment:</strong> <?php echo htmlspecialchars($orderDetails['payment_method'] ?? 'N/A'); ?></p>
                            <p><strong>Shipping:</strong> <?php echo htmlspecialchars($orderDetails['shipping_method'] ?? 'N/A'); ?> ($<?php echo number_format($orderDetails['shipping_cost'] ?? 0, 2); ?>)</p>
                            <p><strong>Total:</strong> $<?php echo number_format($orderDetails['total_amount'], 2); ?></p>
                        </div>
                    </div>

                    <div class="order-items">
                        <h4>Order Items</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderDetails['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
            window.history.replaceState(null, null, window.location.pathname);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>