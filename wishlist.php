<?php
require_once 'functions.php';
require_once 'wishlist_functions.php';
require_once 'cart_functions.php';
session_start();

// Handle POST requests for wishlist operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'Please log in to manage your wishlist.';
        echo json_encode($response);
        exit();
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $productId = (int)($_POST['product_id'] ?? 0);
            $result = addToWishlist($productId);
            if ($result === true) {
                $response['success'] = true;
                $response['message'] = 'Product added to wishlist.';
            } else {
                $response['message'] = $result;
            }
            break;

        case 'remove':
            $productId = (int)($_POST['product_id'] ?? 0);
            $result = removeFromWishlist($productId);
            if ($result === true) {
                $response['success'] = true;
                $response['message'] = 'Product removed from wishlist.';
            } else {
                $response['message'] = $result;
            }
            break;

        case 'clear':
            // Clear all wishlist items for user
            $conn = getDBConnection();
            $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            $response['success'] = true;
            $response['message'] = 'Wishlist cleared.';
            break;

        default:
            $response['message'] = 'Invalid action.';
    }

    echo json_encode($response);
    exit();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$wishlistItems = getWishlist();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - Forge720</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Forge720</div>
            <button class="menu-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="wishlist.php" class="active">Wishlist</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li>
                    <a href="view_cart.php" class="cart-link">
                        Cart (<span id="cart-count"><?php echo getCartItemCount(); ?></span>)
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main style="padding-top: 100px;">
        <section class="section">
            <h2>My Wishlist</h2>

            <?php if (empty($wishlistItems)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <h3>Your wishlist is empty</h3>
                    <p>Save items you're interested in for later. Browse our products and add them to your wishlist!</p>
                    <a href="products.php" class="btn">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="wishlist-container">
                    <div class="wishlist-stats">
                        <p><?php echo count($wishlistItems); ?> item(s) in your wishlist</p>
                    </div>

                    <div class="wishlist-grid">
                        <?php foreach ($wishlistItems as $item): ?>
                            <div class="wishlist-item" data-product-id="<?php echo $item['product_id']; ?>">
                                <div class="wishlist-item-image">
                                    <img src="<?php echo htmlspecialchars(getImageUrl($item['image'])); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <button class="remove-from-wishlist" onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)">
                                        ×
                                    </button>
                                </div>
                                <div class="wishlist-item-info">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="wishlist-item-description">
                                        <?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <p class="wishlist-item-price">$<?php echo number_format($item['price'], 2); ?></p>

                                    <div class="wishlist-item-actions">
                                        <a href="product_detail.php?id=<?php echo $item['product_id']; ?>" class="btn btn-secondary">View Details</a>
                                        <button class="btn" onclick="addToCartFromWishlist(<?php echo $item['product_id']; ?>)">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="wishlist-actions">
                        <button class="btn btn-secondary" onclick="clearWishlist()">Clear All</button>
                        <a href="products.php" class="btn">Continue Shopping</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        function removeFromWishlist(productId) {
            if (!confirm('Remove this item from your wishlist?')) return;

            fetch('wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=remove&product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item.');
            });
        }

        function addToCartFromWishlist(productId) {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item added to cart!');
                    document.getElementById('cart-count').textContent = data.cart_count;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the item to cart.');
            });
        }

        function clearWishlist() {
            if (!confirm('Are you sure you want to clear your entire wishlist?')) return;

            fetch('wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=clear'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while clearing the wishlist.');
            });
        }
    </script>
</body>
</html>