<?php
require_once 'functions.php';
require_once 'cart_functions.php';
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

<header>
    <nav>
        <div class="logo">Forge720</div>
        <button class="menu-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul>
            <li><a href="index.php" class="active">Home</a></li>
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




<!-- ================= WELCOME SECTION ================= -->
<section class="welcome-section">
    <!-- Slideshow Background -->
    <div class="slideshow-container">
        <div class="slide fade">
            <img src="images/CustomMetal.jpg" alt="Custom Metal Fabrication">
        </div>
        <div class="slide fade">
            <img src="images/StructuralSteelFabrication.jpg" alt="Structural Steel">
        </div>
        <div class="slide fade">
            <img src="images/LaserCutting.jpg" alt="Laser Cutting">
        </div>
        <div class="slide fade">
            <img src="images/Welding.jpg" alt="Welding Services">
        </div>
        <div class="slide fade">
            <img src="images/RailingInstallation.jpg" alt="Railing Installation">
        </div>
    </div>

    <div class="welcome-content">
        <div class="hero-copy">
            <h1>WELCOME TO FORGE720</h1>
            <h2 class="subtitle">Forging Precision from Vision</h2>
            <span class="eyebrow">Custom Metal Fabrication</span>
            <p class="tagline">We specialize in custom metal fabrication, precision welding, laser cutting, and industrial solutions tailored to your exact specifications.</p>
            <div class="hero-actions">
                <a href="products.php" class="btn-primary">Explore Our Products</a>
                <a href="services.php" class="btn-secondary">View Our Services</a>
            </div>
        </div>
        <div class="hero-highlights">
            <div class="highlight-card">
                <h3>⚙️ Built to Last</h3>
                <p>Industry-grade fabrication designed for strength, durability, and exceptional performance.</p>
            </div>
            <div class="highlight-card">
                <h3>⚡ Fast Turnaround</h3>
                <p>Responsive project delivery with clear communication and dependable timing.</p>
            </div>
            <div class="highlight-card">
                <h3>🤝 Personalized Support</h3>
                <p>Guided service from initial design through final installation and follow-up.</p>
            </div>
        </div>
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





<!-- ================= FOOTER ================= -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Forge720. All Rights Reserved.</p>
</footer>

<script src="script.js"></script>
<script>
    // Slideshow functionality
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        const slides = document.querySelectorAll(".slide");
        if (slides.length === 0) return; // Exit if no slides
        
        slides.forEach(slide => slide.classList.remove("active"));
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }
        slides[slideIndex - 1].classList.add("active");
        
        // Change slide every 5 seconds
        setTimeout(showSlides, 5000);
    }
</script>
</body>
</html>
