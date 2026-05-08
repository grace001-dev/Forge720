<?php
require_once 'functions.php';
require_once 'cart_functions.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout');
    exit();
}

// Redirect if cart is empty
if (getCartItemCount() == 0) {
    header('Location: view_cart.php');
    exit();
}

$cartItems = getCartItems();
$cartTotal = getCartTotal();
$shippingCost = 0; // Will be calculated based on method
$user = null;

// Get user info if logged in
if (isset($_SESSION['user_id'])) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Forge720</title>
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
                <li><a href="wishlist.php">Wishlist</a></li>
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
            <h2>Checkout</h2>

            <div class="checkout-container">
                <form id="checkoutForm" method="POST" action="process_order.php">
                    <div class="checkout-grid">
                        <!-- Customer Information -->
                        <div class="checkout-section">
                            <h3>Customer Information</h3>
                            <div class="form-group">
                                <label for="customer_name">Full Name *</label>
                                <input type="text" id="customer_name" name="customer_name"
                                       value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="customer_email">Email Address *</label>
                                <input type="email" id="customer_email" name="customer_email"
                                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="customer_phone">Phone Number</label>
                                <input type="tel" id="customer_phone" name="customer_phone">
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="checkout-section">
                            <h3>Shipping Address</h3>
                            <div class="form-group">
                                <label for="shipping_address">Street Address *</label>
                                <input type="text" id="shipping_address" name="shipping_address" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_city">City *</label>
                                    <input type="text" id="shipping_city" name="shipping_city" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_state">State/Province *</label>
                                    <input type="text" id="shipping_state" name="shipping_state" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="shipping_zip">ZIP/Postal Code *</label>
                                    <input type="text" id="shipping_zip" name="shipping_zip" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_country">Country *</label>
                                    <select id="shipping_country" name="shipping_country" required>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="checkout-section">
                            <h3>Billing Address</h3>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="same_as_shipping" checked>
                                    Same as shipping address
                                </label>
                            </div>
                            <div id="billing_fields" style="display: none;">
                                <div class="form-group">
                                    <label for="billing_address">Street Address *</label>
                                    <input type="text" id="billing_address" name="billing_address">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="billing_city">City *</label>
                                        <input type="text" id="billing_city" name="billing_city">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_state">State/Province *</label>
                                        <input type="text" id="billing_state" name="billing_state">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="billing_zip">ZIP/Postal Code *</label>
                                        <input type="text" id="billing_zip" name="billing_zip">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_country">Country *</label>
                                        <select id="billing_country" name="billing_country">
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="GB">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="checkout-section">
                            <h3>Shipping Method</h3>
                            <div class="shipping-options">
                                <div class="shipping-option">
                                    <input type="radio" id="standard" name="shipping_method" value="standard" checked>
                                    <label for="standard">
                                        <div>
                                            <strong>Standard Shipping</strong>
                                            <p>5-7 business days</p>
                                        </div>
                                        <span>$25.00</span>
                                    </label>
                                </div>
                                <div class="shipping-option">
                                    <input type="radio" id="express" name="shipping_method" value="express">
                                    <label for="express">
                                        <div>
                                            <strong>Express Shipping</strong>
                                            <p>2-3 business days</p>
                                        </div>
                                        <span>$50.00</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section">
                            <h3>Payment Method</h3>
                            <div class="payment-options">
                                <div class="payment-option">
                                    <input type="radio" id="cod" name="payment_method" value="cod" checked>
                                    <label for="cod">
                                        <strong>Cash on Delivery</strong>
                                        <p>Pay when you receive your order</p>
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                                    <label for="bank_transfer">
                                        <strong>Bank Transfer</strong>
                                        <p>Direct bank transfer (details provided after order)</p>
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="paypal" name="payment_method" value="paypal">
                                    <label for="paypal">
                                        <strong>PayPal</strong>
                                        <p>Pay securely using PayPal. Funds will be sent to our PayPal email.</p>
                                    </label>
                                </div>
                            </div>
                            <p style="margin-top: 0.75rem; font-size: 0.95rem; color: #555;">PayPal email: <strong>payments@forge720.com</strong></p>
                        </div>

                        <!-- Special Instructions -->
                        <div class="checkout-section">
                            <h3>Special Instructions</h3>
                            <div class="form-group">
                                <textarea id="special_instructions" name="special_instructions"
                                          rows="3" placeholder="Any special delivery instructions..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary-sidebar">
                        <h3>Order Summary</h3>
                        <div class="order-items">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="order-item">
                                    <div class="order-item-info">
                                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                        <?php if ($item['customization_options']): ?>
                                            <p><small>Customizations included</small></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="order-item-price">
                                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-totals">
                            <div class="total-row">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($cartTotal, 2); ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping:</span>
                                <span id="shipping-cost">$25.00</span>
                            </div>
                            <div class="total-row final-total">
                                <span><strong>Total:</strong></span>
                                <span id="final-total"><strong>$<?php echo number_format($cartTotal + 25, 2); ?></strong></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Place Order
                        </button>

                        <div class="checkout-notice">
                            <p><small>By placing your order, you agree to our terms and conditions.</small></p>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        // Handle same as shipping checkbox
        document.getElementById('same_as_shipping').addEventListener('change', function() {
            const billingFields = document.getElementById('billing_fields');
            if (this.checked) {
                billingFields.style.display = 'none';
            } else {
                billingFields.style.display = 'block';
            }
        });

        // Handle shipping method change
        document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateTotals();
            });
        });

        function updateTotals() {
            const shippingMethod = document.querySelector('input[name="shipping_method"]:checked').value;
            const subtotal = <?php echo $cartTotal; ?>;
            let shippingCost = 25.00;

            if (shippingMethod === 'express') {
                shippingCost = 50.00;
            }

            const total = subtotal + shippingCost;

            document.getElementById('shipping-cost').textContent = '$' + shippingCost.toFixed(2);
            document.getElementById('final-total').innerHTML = '<strong>$' + total.toFixed(2) + '</strong>';
        }

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            // Basic validation
            const requiredFields = ['customer_name', 'customer_email', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];
            let isValid = true;

            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.style.borderColor = 'red';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>