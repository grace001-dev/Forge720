<?php
require_once 'functions.php';
require_once 'cart_functions.php';
// session_start() is handled in cart_functions.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Forge720 Custom Fabrication</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .services-header {
            background: linear-gradient(135deg, var(--jungle-green) 0%, var(--dark-green) 100%);
            color: var(--white);
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .services-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--white);
        }

        .services-header p {
            font-size: 1.1rem;
            color: var(--light-gold);
        }

        /* Tabs Styling */
        .services-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .service-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 3rem;
            border-bottom: 2px solid var(--light-green);
            padding-bottom: 1rem;
        }

        .service-tab-btn {
            background-color: var(--gray);
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px 4px 0 0;
            transition: all 0.3s ease;
            color: var(--dark-gray);
        }

        .service-tab-btn:hover {
            background-color: var(--light-green);
            color: var(--white);
            transform: translateY(-2px);
        }

        .service-tab-btn.active {
            background-color: var(--jungle-green);
            color: var(--white);
        }

        .service-tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .service-tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .service-card {
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px var(--shadow);
            transition: all 0.3s ease;
            border-left: 4px solid var(--jungle-green);
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px var(--shadow-dark);
        }

        .service-card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--gray) 0%, var(--light-gray) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--light-green);
            font-weight: bold;
            overflow: hidden;
        }

        .service-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .service-card-content {
            padding: 1.5rem;
        }

        .service-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-green);
            margin-bottom: 0.5rem;
        }

        .service-card-description {
            font-size: 0.9rem;
            color: var(--text-gray);
            line-height: 1.5;
        }

        .service-badge {
            display: inline-block;
            background-color: var(--light-gold);
            color: var(--dark-gray);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 1rem;
        }

        /* Category Description */
        .category-description {
            background: var(--off-white);
            padding: 2rem;
            border-radius: 8px;
            border-left: 4px solid var(--jungle-green);
            margin-bottom: 2rem;
            font-size: 1rem;
            color: var(--text-gray);
            line-height: 1.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .service-tabs {
                justify-content: center;
                flex-wrap: wrap;
            }

            .service-tab-btn {
                font-size: 0.85rem;
                padding: 0.6rem 1rem;
            }

            .services-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1.5rem;
            }

            .services-header h1 {
                font-size: 2rem;
            }
        }

        .search-section {
            margin-bottom: 3rem;
        }

        .search-section input {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            border: 2px solid var(--light-green);
            border-radius: 4px;
            max-width: 500px;
        }

        .search-section input:focus {
            outline: none;
            border-color: var(--jungle-green);
            box-shadow: 0 0 0 3px rgba(75, 139, 62, 0.1);
        }
    </style>
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
                <li><a href="services.php" style="color: var(--jungle-green); font-weight: bold;">Services</a></li>
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
                $searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
                $serviceCategories = getServiceCategories();
                $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

                if ($searchQuery) {
                    echo 'Search Results for "' . htmlspecialchars($searchQuery) . '"';
                } elseif ($categoryId) {
                    foreach ($serviceCategories as $cat) {
                        if ($cat['id'] == $categoryId) {
                            echo htmlspecialchars($cat['category_name']);
                            break;
                        }
                    }
                } else {
                    echo 'Our Services';
                }
                ?>
            </h2>

            <div class="filters-section">
                <div class="category-filters">
                    <a href="services.php" class="category-filter <?php echo !$categoryId ? 'active' : ''; ?>">All Services</a>
                    <?php foreach($serviceCategories as $cat): ?>
                        <a href="?category=<?php echo $cat['id']; ?>" class="category-filter <?php echo $categoryId == $cat['id'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="search-form">
                    <form method="GET" action="services.php">
                        <div class="search-inputs">
                            <input type="text" name="search" placeholder="Search services..." value="<?php echo htmlspecialchars($searchQuery); ?>" />
                            <select name="category">
                                <option value="">All Categories</option>
                                <?php foreach($serviceCategories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $categoryId == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // Get services based on filters
            if ($searchQuery) {
                $services = searchServices($searchQuery, $categoryId);
            } elseif ($categoryId) {
                $services = getServicesByCategory($categoryId);
            } else {
                // Get all services
                $allServices = [];
                foreach ($serviceCategories as $cat) {
                    $catServices = getServicesByCategory($cat['id']);
                    $allServices = array_merge($allServices, $catServices);
                }
                $services = $allServices;
            }
            ?>

            <div class="products">
                <?php
                if (empty($services)):
                ?>
                    <p>No services available at the moment.</p>
                <?php
                else:
                    foreach ($services as $service):
                ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars(getImageUrl($service['image'])); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>" loading="lazy">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <a href="contact.php?product=<?php echo urlencode($service['service_name']); ?>" class="btn">Inquire Now</a>
                    </div>
                </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>About Forge720</h4>
                <p>Custom metal fabrication and architectural solutions for over 20 years.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Info</h4>
                <p>Email: info@forge720.com</p>
                <p>Phone: (254) 792-204330</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Forge720. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.service-tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active from all buttons and content
                document.querySelectorAll('.service-tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('.service-tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Add active to clicked button and corresponding content
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Mobile menu toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('nav ul');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
        }

        // Update cart count (AJAX call)
        function updateCartCount() {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                fetch('cart_functions.php?action=get_count')
                    .then(response => response.text())
                    .then(count => {
                        cartCount.textContent = count;
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // Initial cart count update
        updateCartCount();
    </script>
</body>
</html>
