<?php
require_once 'functions.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Forge720</title>
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
                <li><a href="contact.php" class="active">Contact</a></li>
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
                        Cart (<span id="cart-count">0</span>)
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="page-main">
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2>Contact Us</h2>
                    <p class="center-text">When you submit this form, it will open WhatsApp so the message is sent directly to our business number.</p>
                </div>
                <div class="contact-grid">
                    <div class="contact-card">
                        <h3>Get In Touch</h3>
                        <p>Ready to start your custom fabrication project? Contact us today!</p>
                        <ul class="contact-list">
                            <li><strong>Address:</strong> rugurugrace75@gmail.com</li>
                            <li><strong>Phone:</strong> (+254) 792204330</li>
                            <li><strong>Email:</strong> info@forge720.com</li>
                            <li><strong>Hours:</strong> Mon-Fri 8AM-6PM, Sat 9AM-4PM</li>
                        </ul>
                        <div class="content-card">
                            <h4>Need a Quote?</h4>
                            <p>For detailed custom project quotes with file uploads, use our dedicated quote request form.</p>
                            <a href="quote_request.php" class="btn btn-secondary">Request a Quote</a>
                        </div>
                    </div>
                    <div class="form-card">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $name = sanitizeInput($_POST['name']);
                            $email = sanitizeInput($_POST['email']);
                            $message = sanitizeInput($_POST['message']);
                            $product = isset($_GET['product']) ? sanitizeInput($_GET['product']) : '';

                            $whatsappText = "New contact request from Forge720 website:%0A";
                            $whatsappText .= "Name: " . rawurlencode($name) . "%0A";
                            $whatsappText .= "Email: " . rawurlencode($email) . "%0A";
                            if ($product) {
                                $whatsappText .= "Product: " . rawurlencode($product) . "%0A";
                            }
                            $whatsappText .= "Message: " . rawurlencode($message);

                            $whatsappUrl = "https://api.whatsapp.com/send?phone=" . WHATSAPP_PHONE . "&text=" . $whatsappText;
                            header("Location: " . $whatsappUrl);
                            exit();
                        }
                        ?>

                        <form id="contactForm" method="POST" action="">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input class="form-control" type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input class="form-control" type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message:</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php
                                    if (isset($_GET['product'])) {
                                        echo "I'm interested in: " . htmlspecialchars($_GET['product']);
                                    }
                                ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-full">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>