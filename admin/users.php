<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$users = getAllUsers();

// Handle role update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $userId = intval($_POST['user_id']);
    $role = sanitizeInput($_POST['role']);

    if (updateUserRole($userId, $role)) {
        $message = 'User role updated successfully!';
        $users = getAllUsers(); // Refresh the list
    } else {
        $message = 'Error updating user role.';
    }
}

// Handle new admin creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_admin'])) {
    $username = sanitizeInput($_POST['new_username']);
    $email = sanitizeInput($_POST['new_email']);
    $password = sanitizeInput($_POST['new_password']);

    if (empty($username) || empty($email) || empty($password)) {
        $message = 'All fields are required for creating a new admin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
    } else {
        // Create new admin user
        $result = registerUser($username, $email, $password);
        if ($result === true) {
            // Set role to admin
            $conn = getDBConnection();
            $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            $message = 'New admin user created successfully!';
            $users = getAllUsers(); // Refresh the list
        } else {
            $message = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin - Forge 720</title>
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

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .users-table th,
        .users-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .users-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .role-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .role-user {
            background: #cce5ff;
            color: #004085;
        }

        .role-admin {
            background: #d4edda;
            color: #155724;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
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

        .btn-update:hover {
            background: #218838;
        }

        .role-select {
            padding: 0.25rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .stats-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .stat-item {
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
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php" class="active">Users</a></li>
                <li><a href="quotes.php">Quotes</a></li>
                <li><a href="content.php">Content</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Manage Users</h1>
                <a href="index.php" class="btn-update" style="background: #6c757d;">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="stats-card">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($users); ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count(array_filter($users, function($user) { return $user['role'] === 'admin'; })); ?></div>
                        <div class="stat-label">Admin Users</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count(array_filter($users, function($user) { return $user['role'] === 'user'; })); ?></div>
                        <div class="stat-label">Regular Users</div>
                    </div>
                </div>
            </div>

            <!-- Create New Admin Form -->
            <div class="stats-card">
                <h3 style="margin-bottom: 1rem; color: #333;">Create New Admin User</h3>
                <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
                    <div>
                        <label for="new_username" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Username:</label>
                        <input type="text" id="new_username" name="new_username" required style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <label for="new_email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email:</label>
                        <input type="email" id="new_email" name="new_email" required style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <label for="new_password" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password:</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div>
                        <button type="submit" name="create_admin" class="btn-update" style="width: 100%; padding: 0.5rem;">Create Admin</button>
                    </div>
                </form>
            </div>

            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td class="actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" class="role-select" onchange="this.form.submit()">
                                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>