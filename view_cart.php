<?php
require_once 'functions.php';
require_once 'cart_functions.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Forge720</title>
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
            <h2>Shopping Cart</h2>

            <?php
            $cartItems = getCartItems();
            $cartTotal = getCartTotal();

            if (empty($cartItems)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <h3>Your cart is empty</h3>
                    <p>Browse our products and add items to your cart.</p>
                    <a href="products.php" class="btn">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" data-cart-item-id="<?php echo $item['id']; ?>">
                                <div class="cart-item-image">
                                    <img src="<?php echo htmlspecialchars(getImageUrl($item['image'])); ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="cart-item-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="cart-item-price">$<?php echo number_format($item['price'], 2); ?> each</p>

                                    <?php if ($item['customization_options']): ?>
                                        <div class="customization-options">
                                            <h4>Customizations:</h4>
                                            <?php
                                            $customizations = json_decode($item['customization_options'], true);
                                            foreach ($customizations as $key => $value):
                                            ?>
                                                <p><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="quantity-controls">
                                        <label>Quantity:</label>
                                        <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                                        <input type="number" class="qty-input" value="<?php echo $item['quantity']; ?>"
                                               min="1" max="<?php echo $item['stock_quantity']; ?>"
                                               onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)">
                                        <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                                    </div>

                                    <p class="cart-item-subtotal">
                                        Subtotal: $<span class="subtotal"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </p>
                                </div>
                                <div class="cart-item-actions">
                                    <button class="btn btn-secondary" onclick="removeFromCart(<?php echo $item['id']; ?>)">Remove</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="summary-row total-row">
                            <span><strong>Total:</strong></span>
                            <span><strong>$<?php echo number_format($cartTotal, 2); ?></strong></span>
                        </div>

                        <div class="cart-actions">
                            <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                            <a href="checkout.php" class="btn">Proceed to Checkout</a>
                        </div>

                        <div class="cart-notice">
                            <p><small>Shipping costs will be calculated based on your location and delivery method.</small></p>
                        </div>
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
        function updateQuantity(cartItemId, quantity) {
            if (quantity < 1) return;

            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('cart_item_id', cartItemId);
            formData.append('quantity', quantity);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to show updated cart
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the cart.');
            });
        }

        function removeFromCart(cartItemId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) return;

            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('cart_item_id', cartItemId);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to show updated cart
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item.');
            });
        }
    </script>
</body>
</html>