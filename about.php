<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Forge720</title>
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
                <li><a href="about.php" class="active">About</a></li>
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
                        Cart (<span id="cart-count">0</span>)
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="page-main">
        <section class="section">
            <div class="container">
                <div class="content-card intro-card">
                    <h2>About Forge720</h2>
                    <p>Forge720 is a premier metal fabrication company specializing in custom metalwork for residential and commercial projects. With over 20 years of experience, we combine traditional craftsmanship with modern techniques to create exceptional pieces.</p>
                </div>
                <div class="info-grid">
                    <div class="content-card">
                        <h3>Our Mission</h3>
                        <p>To provide high-quality, custom metal fabrication services that exceed our clients' expectations and bring their visions to life.</p>
                    </div>
                    <div class="content-card">
                        <h3>Our Vision</h3>
                        <p>To be the leading metal fabrication company known for innovation, quality, and customer satisfaction.</p>
                    </div>
                    <div class="content-card">
                        <h3>Our Values</h3>
                        <p>Quality craftsmanship, attention to detail, customer satisfaction, and environmental responsibility.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section section-alt">
            <div class="container">
                <div class="content-card">
                    <h3>Ready to Explore More?</h3>
                    <p>Discover the full range of services Forge720 provides, from custom metalwork to precision fabrication solutions designed for your project.</p>
                    <a href="services.php" class="btn-primary">View Our Services</a>
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