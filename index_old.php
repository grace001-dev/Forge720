<?php
require_once 'functions.php';
require_once 'cart_functions.php';

// Get featured products for slideshow (first 4 products)
$allProducts = getProducts();
$featuredProducts = array_slice($allProducts, 0, 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forge720 - Custom Fabrication</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="navbar">

    <div class="logo-container">
        <a href="index.php">
            <img src="images/logo.png" alt="Forge720 Logo" class="logo">
        </a>
        <h1 class="logo-text">Forge720</h1>
    </div>

    <nav>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/index.php" style="color:#ff6b35;font-weight:bold;">Admin</a></li>
                <?php endif; ?>

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
<!-- ================= END NAVBAR ================= -->


<!-- ================= PRODUCT SLIDESHOW ================= -->
<section class="product-showcase">
    <div class="slideshow-container">
        <?php foreach ($featuredProducts as $index => $product): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="<?php echo getImageUrl($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="slide-text">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p><?php echo htmlspecialchars(substr($product['description'], 0, 150)); ?>...</p>
                </div>
            </div>
        <?php endforeach; ?>
        <a class="prev" onclick="prevSlide()">&#10094;</a>
        <a class="next" onclick="nextSlide()">&#10095;</a>
        <div class="dots">
            <?php for ($i = 0; $i < count($featuredProducts); $i++): ?>
                <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></span>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- ================= WELCOME SECTION ================= -->
<section class="welcome-section">
    <div class="welcome-content">
        <h1>Welcome to Forge720</h1>
        <h2 class="subtitle">Forging Precision from Vision</h2>
        <p class="tagline">We specialize in custom metal fabrication, precision welding, laser cutting, and industrial solutions tailored to your exact specifications.</p>
        <a href="products.php" class="btn-primary">View Our Products</a>
    </div>
</section>


<!-- ================= WHY CHOOSE FORGEE720 ================= -->
<section class="why-choose">
    <h2>Why Choose Forge720</h2>
    <div class="reasons-container">
        <div class="reason-card">
            <div class="reason-icon">⚙️</div>
            <h3>Expert Craftsmanship</h3>
            <p>Decades of experience in precision metal fabrication with skilled professionals.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">🎯</div>
            <h3>Quality Assurance</h3>
            <p>Rigorous quality control ensures every product meets the highest standards.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">⚡</div>
            <h3>Fast Turnaround</h3>
            <p>Quick project completion without compromising on quality or precision.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">💡</div>
            <h3>Custom Solutions</h3>
            <p>Tailored designs and fabrication to match your unique project requirements.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">🤝</div>
            <h3>Dedicated Support</h3>
            <p>Professional team ready to assist from concept to completion and beyond.</p>
        </div>
    </div>
</section>

<!-- ================= WELCOME SECTION ================= -->
<section class="welcome-section">
    <div class="welcome-content">
        <h1>Welcome to Forge720</h1>
        <h2 class="subtitle">Forging Precision from Vision</h2>
        <p class="tagline">We specialize in custom metal fabrication, precision welding, laser cutting, and industrial solutions tailored to your exact specifications.</p>
        <a href="products.php" class="btn-primary">View Our Products</a>
    </div>
</section>


<!-- ================= WHY CHOOSE FORGE720 ================= -->
<section class="why-choose">
    <h2>Why Choose Forge720</h2>
    <div class="reasons-container">
        <div class="reason-card">
            <div class="reason-icon">⚙️</div>
            <h3>Expert Craftsmanship</h3>
            <p>Decades of experience in precision metal fabrication with skilled professionals.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">🎯</div>
            <h3>Quality Assurance</h3>
            <p>Rigorous quality control ensures every product meets the highest standards.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">⚡</div>
            <h3>Fast Turnaround</h3>
            <p>Quick project completion without compromising on quality or precision.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">💡</div>
            <h3>Custom Solutions</h3>
            <p>Tailored designs and fabrication to match your unique project requirements.</p>
        </div>

        <div class="reason-card">
            <div class="reason-icon">🤝</div>
            <h3>Dedicated Support</h3>
            <p>Professional team ready to assist from concept to completion and beyond.</p>
        </div>
    </div>
</section>


<!-- ================= HERO SECTION ================= -->
<section class="hero">
    <h1>Welcome to Forge720</h1>
    <p>Precision Metal Fabrication & Industrial Engineering Solutions</p>
</section>


<!-- ================= SERVICES PREVIEW ================= -->
<section class="services">
    <h2>Our Services</h2>

    <div class="services-container">

        <div class="service-card">
            <img src="images/welding.jpg" alt="Welding">
            <h3>Welding & Fabrication</h3>
            <p>High-quality MIG, TIG, and structural welding services.</p>
        </div>

        <div class="service-card">
            <img src="images/laser.jpg" alt="Laser Cutting">
            <h3>Laser Cutting</h3>
            <p>Precision laser cutting for metal sheets and designs.</p>
        </div>

        <div class="service-card">
            <img src="images/gates.jpg" alt="Gates">
            <h3>Security Gates</h3>
            <p>Strong and modern reinforced security gates.</p>
        </div>

        <div class="service-card">
            <img src="images/furniture.jpg" alt="Furniture">
            <h3>Industrial Furniture</h3>
            <p>Custom metal furniture for homes and industries.</p>
        </div>

    </div>
</section>


<!-- ================= FOOTER ================= -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Forge720. All Rights Reserved.</p>
</footer>

<script src="script.js"></script>
</body>
</html>

