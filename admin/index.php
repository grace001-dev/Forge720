<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$stats = getAdminStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Forge 720</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .recent-activity {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .recent-activity h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
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

        .admin-header h1 {
            margin: 0;
            color: #333;
        }

        .admin-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-admin {
            padding: 0.5rem 1rem;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .btn-admin:hover {
            background: #218838;
        }

        .btn-logout {
            background: #dc3545;
        }

        .btn-logout:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="quotes.php">Quotes</a></li>
                <li><a href="content.php">Content</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-actions">
                    <a href="../index.php" class="btn-admin">View Site</a>
                    <a href="logout.php" class="btn-admin btn-logout">Logout</a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_products']; ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_orders']; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_quotes']; ?></div>
                    <div class="stat-label">Total Quotes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['pending_orders']; ?></div>
                    <div class="stat-label">Pending Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['pending_quotes']; ?></div>
                    <div class="stat-label">Pending Quotes</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Quick Actions</h3>
                <div class="activity-item">
                    <a href="products.php?action=add" class="btn-admin">Add New Product</a>
                </div>
                <div class="activity-item">
                    <a href="categories.php?action=add" class="btn-admin">Add New Category</a>
                </div>
                <div class="activity-item">
                    <a href="orders.php" class="btn-admin">View Recent Orders</a>
                </div>
                <div class="activity-item">
                    <a href="quotes.php" class="btn-admin">Review Quote Requests</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>