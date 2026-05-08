<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';
$delayedMessage = '';
$serviceCategories = getServiceCategories();
$action = $_GET['action'] ?? 'categories';

// Handle form submission for categories
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'categories') {
    $categoryData = [
        'category_name' => sanitizeInput($_POST['category_name']),
        'description' => sanitizeInput($_POST['description']),
        'display_order' => intval($_POST['display_order'])
    ];

    if (isset($_POST['category_id']) && $_POST['category_id']) {
        $categoryData['id'] = intval($_POST['category_id']);
    }

    $result = saveServiceCategory($categoryData);
    if ($result) {
        $message = isset($_POST['category_id']) ? 'Category updated successfully!' : 'Category created successfully!';
        $serviceCategories = getServiceCategories();
    } else {
        $message = 'Error saving category.';
    }
}

// Handle form submission for services
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'services') {
    $imageInput = sanitizeInput($_POST['image'] ?? '');
    if (!empty($imageInput) && !filter_var($imageInput, FILTER_VALIDATE_URL)) {
        $imageInput = str_replace('\\', '/', $imageInput);
        if (stripos($imageInput, 'images') !== false) {
            $imageInput = preg_replace('#.*images[\\/]*#i', '', $imageInput);
        }
        $imageInput = basename($imageInput);
    }

    $serviceData = [
        'service_category_id' => intval($_POST['service_category_id']),
        'service_name' => sanitizeInput($_POST['service_name']),
        'description' => sanitizeInput($_POST['description']),
        'image' => $imageInput,
        'display_order' => intval($_POST['display_order']),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if (isset($_POST['service_id']) && $_POST['service_id']) {
        $serviceData['id'] = intval($_POST['service_id']);
    }

    $result = saveService($serviceData);
    if ($result) {
        $message = isset($_POST['service_id']) ? 'Service updated successfully!' : 'Service created successfully!';
    } else {
        $message = 'Error saving service.';
    }
}

// Handle delete category
if (isset($_GET['delete_category']) && is_numeric($_GET['delete_category'])) {
    if (deleteServiceCategory(intval($_GET['delete_category']))) {
        $delayedMessage = 'Category deleted successfully!';
        $serviceCategories = getServiceCategories();
    } else {
        $message = 'Error deleting category.';
    }
}

// Handle delete service
if (isset($_GET['delete_service']) && is_numeric($_GET['delete_service'])) {
    if (deleteService(intval($_GET['delete_service']))) {
        $message = 'Service deleted successfully!';
    } else {
        $message = 'Error deleting service.';
    }
}

// Get category for editing
$editCategory = null;
if (isset($_GET['edit_category']) && is_numeric($_GET['edit_category'])) {
    $categoryId = intval($_GET['edit_category']);
    foreach ($serviceCategories as $cat) {
        if ($cat['id'] == $categoryId) {
            $editCategory = $cat;
            break;
        }
    }
}

// Get service for editing
$editService = null;
if (isset($_GET['edit_service']) && is_numeric($_GET['edit_service'])) {
    $editService = getServiceById(intval($_GET['edit_service']));
}

// Get services for the selected category
$services = [];
if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $services = getServicesByCategory(intval($_GET['category']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin - Forge 720</title>
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
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
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
            transition: all 0.3s ease;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background-color: #495057;
            color: white;
        }

        .admin-content {
            padding: 2rem;
            background-color: #f8f9fa;
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

        .admin-header a {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .admin-header a:hover {
            background-color: #218838;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            border-left: 4px solid #28a745;
            background-color: #d4edda;
            color: #155724;
        }

        .alert.error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4B8B3E;
            box-shadow: 0 0 0 3px rgba(75, 139, 62, 0.1);
        }

        .form-inline {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-inline .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #4B8B3E;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2F5D2A;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #333;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }

        .table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 0.5rem;
        }

        .checkbox-group label {
            margin-bottom: 0;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons a,
        .action-buttons button {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #dee2e6;
            background: white;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 0.75rem 1rem;
            cursor: pointer;
            font-size: 1rem;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: #4B8B3E;
            border-bottom-color: #4B8B3E;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .admin-dashboard {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: relative;
                height: auto;
            }

            .form-inline {
                flex-direction: column;
            }

            .btn-group {
                flex-direction: column;
            }

            .table {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h3>Admin Menu</h3>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="services.php" class="active">Services</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="quotes.php">Quotes</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header">
                <h1>Manage Services</h1>
                <a href="../services.php" target="_blank">View Services</a>
            </div>

            <?php if ($message): ?>
                <div class="alert <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($delayedMessage): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($delayedMessage); ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn <?php echo $action === 'categories' ? 'active' : ''; ?>" onclick="switchTab('categories')">
                    Manage Categories
                </button>
                <button class="tab-btn <?php echo $action === 'services' ? 'active' : ''; ?>" onclick="switchTab('services')">
                    Manage Services
                </button>
            </div>

            <!-- Manage Categories Tab -->
            <div id="categories-tab" class="tab-content <?php echo $action === 'categories' ? 'active' : ''; ?>">
                <!-- Add/Edit Category Form -->
                <div class="form-container">
                    <h2><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="categories">
                        <?php if ($editCategory): ?>
                            <input type="hidden" name="category_id" value="<?php echo $editCategory['id']; ?>">
                        <?php endif; ?>

                        <div class="form-inline">
                            <div class="form-group">
                                <label for="category_name">Category Name *</label>
                                <input type="text" id="category_name" name="category_name" required 
                                       value="<?php echo $editCategory ? htmlspecialchars($editCategory['category_name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="display_order">Display Order *</label>
                                <input type="number" id="display_order" name="display_order" required 
                                       value="<?php echo $editCategory ? $editCategory['display_order'] : '1'; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" placeholder="Enter category description"><?php echo $editCategory ? htmlspecialchars($editCategory['description']) : ''; ?></textarea>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $editCategory ? 'Update Category' : 'Add Category'; ?>
                            </button>
                            <?php if ($editCategory): ?>
                                <a href="services.php?action=categories" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Categories Table -->
                <div class="form-container">
                    <h2>Service Categories</h2>
                    <?php if (empty($serviceCategories)): ?>
                        <p style="color: #666;">No categories found. Add your first category above.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($serviceCategories as $category): ?>
                                    <tr>
                                        <td><?php echo $category['id']; ?></td>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 50)) . (strlen($category['description'] ?? '') > 50 ? '...' : ''); ?></td>
                                        <td><?php echo $category['display_order']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="services.php?action=categories&edit_category=<?php echo $category['id']; ?>" class="btn btn-warning">Edit</a>
                                                <a href="services.php?action=categories&delete_category=<?php echo $category['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Manage Services Tab -->
            <div id="services-tab" class="tab-content <?php echo $action === 'services' ? 'active' : ''; ?>">
                <!-- Add/Edit Service Form -->
                <div class="form-container">
                    <h2><?php echo $editService ? 'Edit Service' : 'Add New Service'; ?></h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="services">
                        <?php if ($editService): ?>
                            <input type="hidden" name="service_id" value="<?php echo $editService['id']; ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="service_category_id">Service Category *</label>
                            <select id="service_category_id" name="service_category_id" required>
                                <option value="">Select a category...</option>
                                <?php foreach ($serviceCategories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($editService && $editService['service_category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-inline">
                            <div class="form-group">
                                <label for="service_name">Service Name *</label>
                                <input type="text" id="service_name" name="service_name" required 
                                       value="<?php echo $editService ? htmlspecialchars($editService['service_name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="display_order_service">Display Order *</label>
                                <input type="number" id="display_order_service" name="display_order" required 
                                       value="<?php echo $editService ? $editService['display_order'] : '1'; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="service_description">Description</label>
                            <textarea id="service_description" name="description" placeholder="Enter service description"><?php echo $editService ? htmlspecialchars($editService['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-inline">
                            <div class="form-group">
                                <label for="image">Service Image (filename)</label>
                                <input type="text" id="image" name="image" placeholder="e.g., service-photo.jpg"
                                       value="<?php echo $editService ? htmlspecialchars($editService['image']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="is_active">Status</label>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="is_active" name="is_active" 
                                        <?php echo (!$editService || $editService['is_active']) ? 'checked' : ''; ?>>
                                    <label for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $editService ? 'Update Service' : 'Add Service'; ?>
                            </button>
                            <?php if ($editService): ?>
                                <a href="services.php?action=services" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Services Table -->
                <div class="form-container">
                    <h2>All Services</h2>
                    <?php 
                    $allServices = getAllServices();
                    if (empty($allServices)): 
                    ?>
                        <p style="color: #666;">No services found. Add your first service above.</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allServices as $service): ?>
                                    <tr>
                                        <td><?php echo $service['id']; ?></td>
                                        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                        <td><?php echo htmlspecialchars($service['category_name']); ?></td>
                                        <td>
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem; <?php echo $service['is_active'] ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                                                <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $service['display_order']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="services.php?action=services&edit_service=<?php echo $service['id']; ?>" class="btn btn-warning">Edit</a>
                                                <a href="services.php?action=services&delete_service=<?php echo $service['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');

            // Update URL
            window.history.pushState({}, '', `services.php?action=${tabName}`);
        }
    </script>
</body>
</html>
