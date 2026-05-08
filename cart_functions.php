<?php
require_once 'config.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to get cart items for current user/session
function getCartItems() {
    try {
        $conn = getDBConnection();
        $cartItems = [];

        if (isset($_SESSION['user_id'])) {
            // Logged in user - get from database
            $stmt = $conn->prepare("
                SELECT ci.*, p.name, p.price, p.image, p.stock_quantity
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ?
                ORDER BY ci.added_at DESC
            ");
            if ($stmt) {
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $cartItems[] = $row;
                }

                $stmt->close();
            }
        } else {
            // Guest user - get from session
            $sessionId = session_id();
            $stmt = $conn->prepare("
                SELECT ci.*, p.name, p.price, p.image, p.stock_quantity
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.session_id = ? AND ci.user_id IS NULL
                ORDER BY ci.added_at DESC
            ");
            if ($stmt) {
                $stmt->bind_param("s", $sessionId);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $cartItems[] = $row;
                }

                $stmt->close();
            }
        }

        $conn->close();
        return $cartItems;
    } catch (Exception $e) {
        // Return empty array if tables don't exist yet
        return [];
    }
}

// Function to add item to cart
function addToCart($productId, $quantity = 1, $customizationOptions = null) {
    try {
        $conn = getDBConnection();

        // Check if product exists and has stock
        $stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt->close();
            $conn->close();
            return "Product not found.";
        }

        $product = $result->fetch_assoc();
        if ($product['stock_quantity'] < $quantity) {
            $stmt->close();
            $conn->close();
            return "Insufficient stock. Available: " . $product['stock_quantity'];
        }

        $stmt->close();

        // Check if item already in cart
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $_SESSION['user_id'], $productId);
        } else {
            $sessionId = session_id();
            $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ? AND user_id IS NULL");
            $stmt->bind_param("si", $sessionId, $productId);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing item
            $existingItem = $result->fetch_assoc();
            $newQuantity = $existingItem['quantity'] + $quantity;

            if ($product['stock_quantity'] < $newQuantity) {
                $stmt->close();
                $conn->close();
                return "Insufficient stock. Available: " . $product['stock_quantity'];
            }

            $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ?, customization_options = ? WHERE id = ?");
            $customizationJson = $customizationOptions ? json_encode($customizationOptions) : null;
            $updateStmt->bind_param("isi", $newQuantity, $customizationJson, $existingItem['id']);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Add new item
            $customizationJson = $customizationOptions ? json_encode($customizationOptions) : null;

            if (isset($_SESSION['user_id'])) {
                $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity, customization_options) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiis", $_SESSION['user_id'], $productId, $quantity, $customizationJson);
            } else {
                $sessionId = session_id();
                $stmt = $conn->prepare("INSERT INTO cart_items (session_id, product_id, quantity, customization_options) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("siis", $sessionId, $productId, $quantity, $customizationJson);
            }
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();
        return true;
    } catch (Exception $e) {
        return "Error adding item to cart: " . $e->getMessage();
    }
}

// Function to update cart item quantity
function updateCartItem($cartItemId, $quantity) {
    try {
        $conn = getDBConnection();

        if ($quantity <= 0) {
            // Remove item if quantity is 0 or less
            removeFromCart($cartItemId);
            $conn->close();
            return true;
        }

        // Check stock availability
        $stmt = $conn->prepare("
            SELECT p.stock_quantity
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.id = ?
        ");
        $stmt->bind_param("i", $cartItemId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $stmt->close();
            $conn->close();
            return "Cart item not found.";
        }

        $product = $result->fetch_assoc();
        if ($product['stock_quantity'] < $quantity) {
            $stmt->close();
            $conn->close();
            return "Insufficient stock. Available: " . $product['stock_quantity'];
        }

        $stmt->close();

        // Update quantity
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $quantity, $cartItemId);
        $updateStmt->execute();
        $updateStmt->close();

        $conn->close();
        return true;
    } catch (Exception $e) {
        return "Error updating cart item: " . $e->getMessage();
    }
}

// Function to remove item from cart
function removeFromCart($cartItemId) {
    try {
        $conn = getDBConnection();

        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
        $stmt->bind_param("i", $cartItemId);
        $stmt->execute();
        $stmt->close();

        $conn->close();
        return true;
    } catch (Exception $e) {
        return "Error removing item from cart: " . $e->getMessage();
    }
}

// Function to get cart total
function getCartTotal() {
    try {
        $cartItems = getCartItems();
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    } catch (Exception $e) {
        return 0;
    }
}

// Function to get cart item count
function getCartItemCount() {
    try {
        $cartItems = getCartItems();
        $count = 0;

        foreach ($cartItems as $item) {
            $count += $item['quantity'];
        }

        return $count;
    } catch (Exception $e) {
        return 0;
    }
}

// Function to clear cart
function clearCart() {
    try {
        $conn = getDBConnection();

        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
        } else {
            $sessionId = session_id();
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE session_id = ? AND user_id IS NULL");
            $stmt->bind_param("s", $sessionId);
        }

        $stmt->execute();
        $stmt->close();
        $conn->close();

        return true;
    } catch (Exception $e) {
        return "Error clearing cart: " . $e->getMessage();
    }
}

// Function to merge guest cart with user cart (when user logs in)
function mergeGuestCart() {
    try {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $conn = getDBConnection();
        $sessionId = session_id();

        // Get guest cart items
        $stmt = $conn->prepare("SELECT * FROM cart_items WHERE session_id = ? AND user_id IS NULL");
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($item = $result->fetch_assoc()) {
            // Check if user already has this product in cart
            $checkStmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
            $checkStmt->bind_param("ii", $_SESSION['user_id'], $item['product_id']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Update existing user cart item
                $existingItem = $checkResult->fetch_assoc();
                $newQuantity = $existingItem['quantity'] + $item['quantity'];

                $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                $updateStmt->bind_param("ii", $newQuantity, $existingItem['id']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Move guest item to user cart
                $updateStmt = $conn->prepare("UPDATE cart_items SET user_id = ?, session_id = NULL WHERE id = ?");
                $updateStmt->bind_param("ii", $_SESSION['user_id'], $item['id']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            $checkStmt->close();
        }

        $stmt->close();
        $conn->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>