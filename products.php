<?php
require_once 'functions.php';
require_once 'cart_functions.php';
require_once 'wishlist_functions.php';
session_start();

// Get filter parameters
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Get products based on filters
if ($searchQuery) {
    $products = searchProducts($searchQuery, $categoryId);
} elseif ($categoryId) {
    $products = getProductsByCategory($categoryId);
} else {
    $products = getProducts();
}

$categories = getCategories();
$currentCategory = null;
if ($categoryId) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $categoryId) {
            $currentCategory = $cat;
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
    <title><?php echo $currentCategory ? htmlspecialchars($currentCategory['name']) . ' - ' : ($searchQuery ? 'Search Results - ' : ''); ?>Forge 720</title>
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
                <li><a href="products.php" class="active">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="orders.php">Orders</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
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
            <h2>
                <?php
                if ($searchQuery) {
                    echo 'Search Results for "' . htmlspecialchars($searchQuery) . '"';
                } elseif ($currentCategory) {
                    echo htmlspecialchars($currentCategory['name']);
                } else {
                    echo 'Our Products';
                }
                ?>
            </h2>
            <div class="filters-section">
                <div class="category-filters">
                    <a href="products.php" class="category-filter <?php echo !$categoryId ? 'active' : ''; ?>">All Products</a>
                    <?php foreach($categories as $cat): ?>
                        <a href="?category=<?php echo $cat['id']; ?>" class="category-filter <?php echo $categoryId == $cat['id'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="search-form">
                    <form method="GET" action="products.php">
                        <div class="search-inputs">
                            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
                            <select name="category">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $categoryId == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="products">
                <?php
                if (empty($products)) {
                    echo "<p>No products available at the moment.</p>";
                } else {
                    foreach ($products as $product):
                ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars(getImageUrl($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <a href="contact.php?product=<?php echo urlencode($product['name']); ?>" class="btn">Inquire Now</a>
                    </div>
                </div>
                <?php endforeach; } ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        function addToCart(productId) {
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
                    alert('Product added to cart!');
                    document.getElementById('cart-count').textContent = data.cart_count;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the product to cart.');
            });
        }

        function toggleWishlist(productId) {
            const btn = event.target;
            const isInWishlist = btn.classList.contains('in-wishlist');
            const action = isInWishlist ? 'remove' : 'add';

            fetch('wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=' + action + '&product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isInWishlist) {
                        btn.classList.remove('in-wishlist');
                    } else {
                        btn.classList.add('in-wishlist');
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating wishlist.');
            });
        }
    </script>
</body>
</html>