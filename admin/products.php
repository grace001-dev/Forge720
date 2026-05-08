<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$products = getProducts();
$productCount = count($products);
$categories = getCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imageInput = sanitizeInput($_POST['image']);
    if (!filter_var($imageInput, FILTER_VALIDATE_URL)) {
        $imageInput = str_replace('\\', '/', $imageInput);
        if (stripos($imageInput, 'images') !== false) {
            $imageInput = preg_replace('#.*images[\\/]*#i', '', $imageInput);
        }
        $imageInput = basename($imageInput);
    }

    $productData = [
        'name' => sanitizeInput($_POST['name']),
        'description' => sanitizeInput($_POST['description']),
        
        'image' => $imageInput,
        'category_id' => intval($_POST['category_id']),
        'material' => sanitizeInput($_POST['material']),
        'dimensions' => sanitizeInput($_POST['dimensions']),
        'finish' => sanitizeInput($_POST['finish']),
        'stock_quantity' => intval($_POST['stock_quantity']),
        'is_customizable' => isset($_POST['is_customizable']) ? 1 : 0
    ];

    if (isset($_POST['product_id']) && $_POST['product_id']) {
        $productData['id'] = intval($_POST['product_id']);
    }

    $result = saveProduct($productData);
    if ($result) {
        $message = isset($_POST['product_id']) ? 'Product updated successfully!' : 'Product created successfully!';
        $products = getProducts(); // Refresh the list
        $productCount = count($products);
    } else {
        $message = 'Error saving product.';
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteProduct(intval($_GET['delete']))) {
        $message = 'Product deleted successfully!';
        $products = getProducts();
        $productCount = count($products);
    } else {
        $message = 'Error deleting product.';
    }
}

// Get product for editing
$editProduct = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editProduct = getProductById(intval($_GET['edit']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin - Forge 720</title>
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

        .product-form, .products-list {
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table th,
        .products-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .products-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .product-image {
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
                <li><a href="products.php" class="active">Products</a></li>
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
                <h1>Manage Products</h1>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <button type="button" class="btn-submit btn-count" onclick="alert('Total products: <?php echo $productCount; ?>')">Show Product Count</button>
                    <a href="index.php" class="btn-submit" style="background: #6c757d;">Back to Dashboard</a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="product-form">
                <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
                <form method="POST" action="">
                    <?php if ($editProduct): ?>
                        <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Product Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo $editProduct ? htmlspecialchars($editProduct['name']) : ''; ?>" required>
                        </div>

                        

                        <div class="form-group">
                            <label for="category_id">Category:</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $editProduct && $editProduct['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="stock_quantity">Stock Quantity:</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo $editProduct ? $editProduct['stock_quantity'] : '100'; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="material">Material:</label>
                            <input type="text" id="material" name="material" value="<?php echo $editProduct ? htmlspecialchars($editProduct['material']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="dimensions">Dimensions:</label>
                            <input type="text" id="dimensions" name="dimensions" value="<?php echo $editProduct ? htmlspecialchars($editProduct['dimensions']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="finish">Finish:</label>
                            <input type="text" id="finish" name="finish" value="<?php echo $editProduct ? htmlspecialchars($editProduct['finish']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="image">Image filename or URL:</label>
                            <input type="text" id="image" name="image" value="<?php echo $editProduct ? htmlspecialchars($editProduct['image']) : ''; ?>">
                            <small>Use a local image filename like <code>Structural.jpg</code> or a full external URL.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description"><?php echo $editProduct ? htmlspecialchars($editProduct['description']) : ''; ?></textarea>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="is_customizable" name="is_customizable" <?php echo $editProduct && $editProduct['is_customizable'] ? 'checked' : ''; ?>>
                        <label for="is_customizable">Customizable Product</label>
                    </div>

                    <button type="submit" class="btn-submit"><?php echo $editProduct ? 'Update Product' : 'Create Product'; ?></button>
                </form>
            </div>

            <div class="products-list">
                <h2>All Products</h2>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
            
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo getImageUrl($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="?delete=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
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