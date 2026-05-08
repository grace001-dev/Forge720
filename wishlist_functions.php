<?php
require_once 'config.php';

// Function to add product to wishlist
function addToWishlist($productId) {
    if (!isset($_SESSION['user_id'])) {
        return "Please log in to add items to your wishlist.";
    }

    $conn = getDBConnection();

    // Check if product exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $stmt->close();
        $conn->close();
        return "Product not found.";
    }
    $stmt->close();

    // Check if already in wishlist
    $stmt = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return "Product already in wishlist.";
    }
    $stmt->close();

    // Add to wishlist
    $stmt = $conn->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $productId);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return "Error adding to wishlist.";
    }
}

// Function to remove from wishlist
function removeFromWishlist($productId) {
    if (!isset($_SESSION['user_id'])) {
        return "Please log in to manage your wishlist.";
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $productId);
    $stmt->execute();
    $stmt->close();

    $conn->close();
    return true;
}

// Function to get user's wishlist
function getWishlist() {
    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("
        SELECT w.*, p.name, p.price, p.image, p.description
        FROM wishlists w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = ?
        ORDER BY w.added_at DESC
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $wishlist = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $wishlist;
}

// Function to check if product is in wishlist
function isInWishlist($productId) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    $isInWishlist = $result->num_rows > 0;

    $stmt->close();
    $conn->close();
    return $isInWishlist;
}

// Function to get wishlist count
function getWishlistCount() {
    if (!isset($_SESSION['user_id'])) {
        return 0;
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlists WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();
    $conn->close();
    return $row['count'];
}
?>