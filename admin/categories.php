<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$categories = getCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryData = [
        'name' => sanitizeInput($_POST['name']),
        'description' => sanitizeInput($_POST['description']),
        'image' => sanitizeInput($_POST['image'])
    ];

    if (isset($_POST['category_id']) && $_POST['category_id']) {
        $categoryData['id'] = intval($_POST['category_id']);
    }

    $result = saveCategory($categoryData);
    if ($result) {
        $message = isset($_POST['category_id']) ? 'Category updated successfully!' : 'Category created successfully!';
        $categories = getCategories(); // Refresh the list
    } else {
        $message = 'Error saving category.';
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteCategory(intval($_GET['delete']))) {
        $message = 'Category deleted successfully!';
        $categories = getCategories();
    } else {
        $message = 'Error deleting category.';
    }
}

// Get category for editing
$editCategory = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    foreach ($categories as $category) {
        if ($category['id'] == intval($_GET['edit'])) {
            $editCategory = $category;
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
    <title>Manage Categories - Admin - Forge 720</title>
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

        .category-form, .categories-list {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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

        .categories-table {
            width: 100%;
            border-collapse: collapse;
        }

        .categories-table th,
        .categories-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .categories-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .category-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
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

        .btn-delete {
            padding: 0.25rem 0.5rem;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .btn-delete:hover {
            background: #c82333;
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
                <li><a href="services.php">Services</a></li>
                <li><a href="categories.php" class="active">Categories</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="quotes.php">Quotes</a></li>
                <li><a href="content.php">Content</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Manage Categories</h1>
                <a href="index.php" class="btn-submit" style="background: #6c757d;">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="category-form">
                <h2><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h2>
                <form method="POST" action="">
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="category_id" value="<?php echo $editCategory['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Category Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo $editCategory ? htmlspecialchars($editCategory['name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="image">Image filename or URL:</label>
                            <input type="text" id="image" name="image" value="<?php echo $editCategory ? htmlspecialchars($editCategory['image']) : ''; ?>">
                            <small>Use a local image filename like <code>category.jpg</code> or a full external URL.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description"><?php echo $editCategory ? htmlspecialchars($editCategory['description']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit"><?php echo $editCategory ? 'Update Category' : 'Create Category'; ?></button>
                </form>
            </div>

            <div class="categories-list">
                <h2>All Categories</h2>
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <?php if ($category['image']): ?>
                                        <img src="<?php echo getImageUrl($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="category-image">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($category['description'], 0, 100)) . (strlen($category['description']) > 100 ? '...' : ''); ?></td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $category['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="?delete=<?php echo $category['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>