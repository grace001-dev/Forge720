<?php
require_once 'config.php';

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Safe session start function
function safeSessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Function to register user
function registerUser($username, $email, $password) {
    $conn = getDBConnection();

    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return "Username or email already exists.";
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return "Error registering user.";
    }
}

// Function to login user
function loginUser($username, $password) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            safeSessionStart();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            $stmt->close();
            $conn->close();
            return true;
        }
    }

    $stmt->close();
    $conn->close();
    return "Invalid username or password.";
}

// Function to check if user is logged in
function isLoggedIn() {
    safeSessionStart();
    return isset($_SESSION['user_id']);
}

// Function to logout
function logout() {
    safeSessionStart();
    session_destroy();
}

// Function to get products
function getProducts() {
    $conn = getDBConnection();

    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = $conn->query($sql);

    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    $conn->close();
    return $products;
}

// Normalize and build image URLs for local and remote images
function getImageUrl($imagePath) {
    $imagePath = trim($imagePath);
    if (empty($imagePath)) {
        return 'images/default.png';
    }

    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
        return $imagePath;
    }

    $imagePath = str_replace('\\', '/', $imagePath);
    if (stripos($imagePath, 'images') !== false) {
        $imagePath = preg_replace('#.*images[\\/]*#i', '', $imagePath);
    }

    return 'images/' . rawurlencode($imagePath);
}

// Function to get categories
function getCategories() {
    $conn = getDBConnection();

    $sql = "SELECT * FROM categories ORDER BY name";
    $result = $conn->query($sql);

    $categories = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    $conn->close();
    return $categories;
}

// Function to get products by category
function getProductsByCategory($categoryId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $products;
}

// Function to get product by ID
function getProductById($productId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    $product = null;
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }

    $stmt->close();
    $conn->close();
    return $product;
}

// Function to get customization options for a product
function getCustomizationOptions($productId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM customization_options WHERE product_id = ? ORDER BY display_order");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = [];
    while($row = $result->fetch_assoc()) {
        $options[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $options;
}

// Function to create order
function createOrder($orderData) {
    $conn = getDBConnection();

    // Generate order number
    $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

    // Calculate shipping cost (simple logic - can be enhanced)
    $shippingCost = 0;
    if ($orderData['shipping_method'] == 'express') {
        $shippingCost = 50.00;
    } elseif ($orderData['shipping_method'] == 'standard') {
        $shippingCost = 25.00;
    }

    $totalAmount = $orderData['subtotal'] + $shippingCost;

    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, order_number, total_amount, status, payment_status, payment_method,
            customer_name, customer_email, customer_phone,
            shipping_address, shipping_city, shipping_state, shipping_zip, shipping_country,
            billing_address, billing_city, billing_state, billing_zip, billing_country,
            shipping_method, shipping_cost, special_instructions
        ) VALUES (?, ?, ?, 'pending', 'pending', ?,
            ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?
        )
    ");

    $stmt->bind_param("isdsssssssssssssssssd",
        $orderData['user_id'], $orderNumber, $totalAmount, $orderData['payment_method'],
        $orderData['customer_name'], $orderData['customer_email'], $orderData['customer_phone'],
        $orderData['shipping_address'], $orderData['shipping_city'], $orderData['shipping_state'],
        $orderData['shipping_zip'], $orderData['shipping_country'],
        $orderData['billing_address'], $orderData['billing_city'], $orderData['billing_state'],
        $orderData['billing_zip'], $orderData['billing_country'],
        $orderData['shipping_method'], $shippingCost, $orderData['special_instructions']
    );

    if ($stmt->execute()) {
        $orderId = $conn->insert_id;
        $stmt->close();

        // Add order items
        foreach ($orderData['items'] as $item) {
            $itemStmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, customization_options, subtotal)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $customizationJson = isset($item['customization_options']) ? json_encode($item['customization_options']) : null;
            $subtotal = $item['unit_price'] * $item['quantity'];

            $itemStmt->bind_param("iisidssd", $orderId, $item['product_id'], $item['product_name'],
                $item['quantity'], $item['unit_price'], $customizationJson, $subtotal);
            $itemStmt->execute();
            $itemStmt->close();

            // Update product stock
            $stockStmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stockStmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stockStmt->execute();
            $stockStmt->close();
        }

        $conn->close();
        return ['order_id' => $orderId, 'order_number' => $orderNumber];
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

// Function to get user's orders
function getUserOrders($userId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("
        SELECT o.*, COUNT(oi.id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $orders;
}

// Function to get order details
function getOrderDetails($orderId, $userId = null) {
    $conn = getDBConnection();

    // Get order info
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    if ($userId) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $orderId, $userId);
    } else {
        $stmt->bind_param("i", $orderId);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        $conn->close();
        return null;
    }

    $order = $result->fetch_assoc();
    $stmt->close();

    // Get order items
    $itemStmt = $conn->prepare("
        SELECT oi.*, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $itemStmt->bind_param("i", $orderId);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();

    $order['items'] = [];
    while($row = $itemResult->fetch_assoc()) {
        $order['items'][] = $row;
    }

    $itemStmt->close();
    $conn->close();
    return $order;
}

// Function to create quote request
function createQuoteRequest($quoteData) {
    $conn = getDBConnection();

    // Generate quote number
    $quoteNumber = 'QTE-' . date('Ymd') . '-' . rand(1000, 9999);

    $stmt = $conn->prepare("
        INSERT INTO quotes (
            quote_number, user_id, customer_name, customer_email, customer_phone,
            description, required_by_date, attachment_path, attachment_filename
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sisssssss",
        $quoteNumber,
        $quoteData['user_id'] ?? null,
        $quoteData['customer_name'],
        $quoteData['customer_email'],
        $quoteData['customer_phone'] ?? null,
        $quoteData['description'],
        $quoteData['required_by_date'] ?? null,
        $quoteData['attachment_path'] ?? null,
        $quoteData['attachment_filename'] ?? null
    );

    if ($stmt->execute()) {
        $quoteId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return ['quote_id' => $quoteId, 'quote_number' => $quoteNumber];
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

// Function to get user's quotes
function getUserQuotes($userId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM quotes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $quotes = [];
    while($row = $result->fetch_assoc()) {
        $quotes[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $quotes;
}

// Function to search products
function searchProducts($query, $categoryId = null) {
    $conn = getDBConnection();

    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE (p.name LIKE ? OR p.description LIKE ?)";
    $params = ["%{$query}%", "%{$query}%"];
    $types = "ss";

    if ($categoryId) {
        $sql .= " AND p.category_id = ?";
        $params[] = $categoryId;
        $types .= "i";
    }

    $sql .= " ORDER BY p.name";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $products;
}

// Admin Functions

// Function to check if user is admin
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }

    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row['role'] === 'admin';
    }

    $stmt->close();
    $conn->close();
    return false;
}

// Function to get admin stats
function getAdminStats() {
    $conn = getDBConnection();

    $stats = [];

    // Total products
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = $result->fetch_assoc()['total'];

    // Total orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['total'];

    // Total users
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    $stats['total_users'] = $result->fetch_assoc()['total'];

    // Total quotes
    $result = $conn->query("SELECT COUNT(*) as total FROM quotes");
    $stats['total_quotes'] = $result->fetch_assoc()['total'];

    // Recent orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $result->fetch_assoc()['total'];

    // Recent quotes
    $result = $conn->query("SELECT COUNT(*) as total FROM quotes WHERE status = 'pending'");
    $stats['pending_quotes'] = $result->fetch_assoc()['total'];

    $conn->close();
    return $stats;
}

// Function to get all orders (admin)
function getAllOrders() {
    $conn = getDBConnection();

    $sql = "
        SELECT o.*, u.username,
               COUNT(oi.id) as item_count,
               SUM(oi.subtotal) as items_total
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ";
    $result = $conn->query($sql);

    $orders = [];
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    $conn->close();
    return $orders;
}

// Function to get all users (admin)
function getAllUsers() {
    $conn = getDBConnection();

    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $conn->close();
    return $users;
}

// Function to get all quotes (admin)
function getAllQuotes() {
    $conn = getDBConnection();

    $sql = "
        SELECT q.*, u.username
        FROM quotes q
        LEFT JOIN users u ON q.user_id = u.id
        ORDER BY q.created_at DESC
    ";
    $result = $conn->query($sql);

    $quotes = [];
    while($row = $result->fetch_assoc()) {
        $quotes[] = $row;
    }

    $conn->close();
    return $quotes;
}

// Function to update order status
function updateOrderStatus($orderId, $status) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// Function to update quote status
function updateQuoteStatus($quoteId, $status, $estimatedCost = null, $notes = null) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("UPDATE quotes SET status = ?, estimated_cost = ?, admin_notes = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $status, $estimatedCost, $notes, $quoteId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// Function to create/update product
function saveProduct($productData) {
    $conn = getDBConnection();

    if (isset($productData['id']) && $productData['id']) {
        // Update existing product
        $stmt = $conn->prepare("
            UPDATE products SET
                name = ?, description = ?, price = ?, image = ?, category_id = ?,
                material = ?, dimensions = ?, finish = ?, stock_quantity = ?, is_customizable = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssdsssssiii",
            $productData['name'], $productData['description'], $productData['price'],
            $productData['image'], $productData['category_id'], $productData['material'],
            $productData['dimensions'], $productData['finish'], $productData['stock_quantity'],
            $productData['is_customizable'], $productData['id']
        );
    } else {
        // Create new product
        $stmt = $conn->prepare("
            INSERT INTO products (name, description, price, image, category_id, material, dimensions, finish, stock_quantity, is_customizable)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssdsssssii",
            $productData['name'], $productData['description'], $productData['price'],
            $productData['image'], $productData['category_id'], $productData['material'],
            $productData['dimensions'], $productData['finish'], $productData['stock_quantity'],
            $productData['is_customizable']
        );
    }

    $result = $stmt->execute();
    $productId = isset($productData['id']) ? $productData['id'] : $conn->insert_id;

    $stmt->close();
    $conn->close();
    return $result ? $productId : false;
}

// Function to delete product
function deleteProduct($productId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// Function to create/update category
function saveCategory($categoryData) {
    $conn = getDBConnection();

    if (isset($categoryData['id']) && $categoryData['id']) {
        // Update existing category
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $categoryData['name'], $categoryData['description'], $categoryData['image'], $categoryData['id']);
    } else {
        // Create new category
        $stmt = $conn->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $categoryData['name'], $categoryData['description'], $categoryData['image']);
    }

    $result = $stmt->execute();
    $categoryId = isset($categoryData['id']) ? $categoryData['id'] : $conn->insert_id;

    $stmt->close();
    $conn->close();
    return $result ? $categoryId : false;
}

// Function to delete category
function deleteCategory($categoryId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// Function to update user role
function updateUserRole($userId, $role) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $userId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// ==================== SERVICE FUNCTIONS ====================

// Function to get all service categories
function getServiceCategories() {
    $conn = getDBConnection();

    $sql = "SELECT * FROM service_categories ORDER BY display_order ASC";
    $result = $conn->query($sql);

    $categories = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    $conn->close();
    return $categories;
}

// Function to get services by category ID
function getServicesByCategory($categoryId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM services WHERE service_category_id = ? AND is_active = TRUE ORDER BY display_order ASC");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $services = [];
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $services;
}

// Function to get all active services with their categories
function getAllServices() {
    $conn = getDBConnection();

    $sql = "
        SELECT s.*, sc.category_name
        FROM services s
        JOIN service_categories sc ON s.service_category_id = sc.id
        WHERE s.is_active = TRUE
        ORDER BY sc.display_order ASC, s.display_order ASC
    ";
    $result = $conn->query($sql);

    $services = [];
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    $conn->close();
    return $services;
}

// Function to get service by ID
function getServiceById($serviceId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT s.*, sc.category_name FROM services s JOIN service_categories sc ON s.service_category_id = sc.id WHERE s.id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();

    $service = null;
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    }

    $stmt->close();
    $conn->close();
    return $service;
}

// Function to create/update service category (admin)
function saveServiceCategory($categoryData) {
    $conn = getDBConnection();

    if (isset($categoryData['id']) && $categoryData['id']) {
        // Update existing category
        $stmt = $conn->prepare("UPDATE service_categories SET category_name = ?, description = ?, display_order = ? WHERE id = ?");
        $stmt->bind_param("ssii", $categoryData['category_name'], $categoryData['description'], $categoryData['display_order'], $categoryData['id']);
    } else {
        // Create new category
        $stmt = $conn->prepare("INSERT INTO service_categories (category_name, description, display_order) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $categoryData['category_name'], $categoryData['description'], $categoryData['display_order']);
    }

    $result = $stmt->execute();
    $categoryId = isset($categoryData['id']) ? $categoryData['id'] : $conn->insert_id;

    $stmt->close();
    $conn->close();
    return $result ? $categoryId : false;
}

// Function to create/update service (admin)
function saveService($serviceData) {
    $conn = getDBConnection();

    if (isset($serviceData['id']) && $serviceData['id']) {
        // Update existing service
        $stmt = $conn->prepare("
            UPDATE services SET
                service_category_id = ?, service_name = ?, description = ?, image = ?, display_order = ?, is_active = ?
            WHERE id = ?
        ");
        $isActive = isset($serviceData['is_active']) ? 1 : 0;
        $stmt->bind_param("isssiii", $serviceData['service_category_id'], $serviceData['service_name'], 
            $serviceData['description'], $serviceData['image'], $serviceData['display_order'], $isActive, $serviceData['id']);
    } else {
        // Create new service
        $stmt = $conn->prepare("
            INSERT INTO services (service_category_id, service_name, description, image, display_order, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $isActive = isset($serviceData['is_active']) ? 1 : 0;
        $stmt->bind_param("isssii", $serviceData['service_category_id'], $serviceData['service_name'], 
            $serviceData['description'], $serviceData['image'], $serviceData['display_order'], $isActive);
    }

    $result = $stmt->execute();
    $serviceId = isset($serviceData['id']) ? $serviceData['id'] : $conn->insert_id;

    $stmt->close();
    $conn->close();
    return $result ? $serviceId : false;
}

// Function to delete service
function deleteService($serviceId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// Function to delete service category
function deleteServiceCategory($categoryId) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("DELETE FROM service_categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $result;
}

// ==================== PASSWORD RESET FUNCTIONS ====================

// Function to generate password reset token
function generatePasswordResetToken($email) {
    $conn = getDBConnection();

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        $conn->close();
        return "Email address not found.";
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];
    $stmt->close();

    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Update user with reset token
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->bind_param("ssi", $resetToken, $resetExpires, $userId);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ['token' => $resetToken, 'user_id' => $userId];
    } else {
        $stmt->close();
        $conn->close();
        return "Error generating reset token.";
    }
}

// Function to verify password reset token
function verifyPasswordResetToken($token) {
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    }

    $stmt->close();
    $conn->close();
    return false;
}

// Function to reset password
function resetPassword($token, $newPassword) {
    $conn = getDBConnection();

    // Verify token first
    $user = verifyPasswordResetToken($token);
    if (!$user) {
        $conn->close();
        return "Invalid or expired reset token.";
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password and clear reset token
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $user['id']);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return "Error updating password.";
    }
}

// Function to send password reset email (basic implementation)
function sendPasswordResetEmail($email, $resetToken) {
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $resetToken;

    $subject = "Password Reset - Forge 720";
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
    </head>
    <body>
        <h2>Password Reset Request</h2>
        <p>You have requested to reset your password for your Forge 720 account.</p>
        <p>Click the link below to reset your password:</p>
        <p><a href=\"$resetLink\">Reset Password</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this password reset, please ignore this email.</p>
        <br>
        <p>Best regards,<br>Forge 720 Team</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@forge720.com" . "\r\n";

    // For development, we'll just return the email content instead of sending
    // In production, use mail() function or a proper email service
    return [
        'to' => $email,
        'subject' => $subject,
        'message' => $message,
        'headers' => $headers
    ];
}

?>