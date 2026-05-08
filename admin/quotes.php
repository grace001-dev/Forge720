<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$quotes = getAllQuotes();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quote_id'])) {
    $quoteId = intval($_POST['quote_id']);
    $status = sanitizeInput($_POST['status']);
    $estimatedCost = isset($_POST['estimated_cost']) && $_POST['estimated_cost'] ? floatval($_POST['estimated_cost']) : null;
    $notes = sanitizeInput($_POST['admin_notes'] ?? '');

    if (updateQuoteStatus($quoteId, $status, $estimatedCost, $notes)) {
        $message = 'Quote updated successfully!';
        $quotes = getAllQuotes(); // Refresh the list
    } else {
        $message = 'Error updating quote.';
    }
}

// Get quote for editing
$editQuote = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    foreach ($quotes as $quote) {
        if ($quote['id'] == intval($_GET['edit'])) {
            $editQuote = $quote;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quotes - Admin - Forge 720</title>
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

        .quotes-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .quotes-table th,
        .quotes-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .quotes-table th {
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

        .status-reviewing {
            background: #cce5ff;
            color: #004085;
        }

        .status-quoted {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-accepted {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-expired {
            background: #e2e3e5;
            color: #383d41;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit {
            padding: 0.25rem 0.5rem;
            background: #ffc107;
            color: #212529;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .quote-form {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            padding: 0.75rem 1.5rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #0056b3;
        }

        .quote-details {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .quote-description {
            background: white;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            border-left: 4px solid #007bff;
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
                <li><a href="users.php">Users</a></li>
                <li><a href="quotes.php" class="active">Quotes</a></li>
                <li><a href="content.php">Content</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Manage Quotes</h1>
                <a href="index.php" class="btn-submit" style="background: #6c757d;">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($editQuote): ?>
                <div class="quote-form">
                    <h2>Update Quote #<?php echo htmlspecialchars($editQuote['quote_number']); ?></h2>

                    <div class="quote-details">
                        <h3>Quote Details</h3>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($editQuote['customer_name']); ?> (<?php echo htmlspecialchars($editQuote['customer_email']); ?>)</p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($editQuote['customer_phone'] ?? 'N/A'); ?></p>
                        <p><strong>Requested By:</strong> <?php echo $editQuote['required_by_date'] ? date('M j, Y', strtotime($editQuote['required_by_date'])) : 'N/A'; ?></p>
                        <?php if ($editQuote['attachment_path']): ?>
                            <p><strong>Attachment:</strong> <a href="../<?php echo htmlspecialchars($editQuote['attachment_path']); ?>" target="_blank"><?php echo htmlspecialchars($editQuote['attachment_filename']); ?></a></p>
                        <?php endif; ?>
                    </div>

                    <div class="quote-description">
                        <h4>Project Description</h4>
                        <p><?php echo nl2br(htmlspecialchars($editQuote['description'])); ?></p>
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="quote_id" value="<?php echo $editQuote['id']; ?>">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" name="status" required>
                                    <option value="pending" <?php echo $editQuote['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="reviewing" <?php echo $editQuote['status'] == 'reviewing' ? 'selected' : ''; ?>>Reviewing</option>
                                    <option value="quoted" <?php echo $editQuote['status'] == 'quoted' ? 'selected' : ''; ?>>Quoted</option>
                                    <option value="accepted" <?php echo $editQuote['status'] == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                                    <option value="rejected" <?php echo $editQuote['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="expired" <?php echo $editQuote['status'] == 'expired' ? 'selected' : ''; ?>>Expired</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="estimated_cost">Estimated Cost ($):</label>
                                <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" value="<?php echo $editQuote['estimated_cost'] ? $editQuote['estimated_cost'] : ''; ?>" placeholder="Leave empty if not yet determined">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="admin_notes">Admin Notes:</label>
                            <textarea id="admin_notes" name="admin_notes" placeholder="Internal notes about this quote..."><?php echo htmlspecialchars($editQuote['admin_notes'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Update Quote</button>
                        <a href="quotes.php" class="btn-submit" style="background: #6c757d; margin-left: 1rem;">Cancel</a>
                    </form>
                </div>
            <?php endif; ?>

            <table class="quotes-table">
                <thead>
                    <tr>
                        <th>Quote #</th>
                        <th>Customer</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Cost</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $quote): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($quote['quote_number']); ?></td>
                            <td><?php echo htmlspecialchars($quote['customer_name']); ?><br><small><?php echo htmlspecialchars($quote['customer_email']); ?></small></td>
                            <td><?php echo htmlspecialchars(substr($quote['description'], 0, 50)) . (strlen($quote['description']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($quote['status']); ?>">
                                    <?php echo ucfirst($quote['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $quote['estimated_cost'] ? '$' . number_format($quote['estimated_cost'], 2) : 'N/A'; ?></td>
                            <td><?php echo date('M j, Y', strtotime($quote['created_at'])); ?></td>
                            <td class="actions">
                                <a href="?edit=<?php echo $quote['id']; ?>" class="btn-edit">Manage</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>