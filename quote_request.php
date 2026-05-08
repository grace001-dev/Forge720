<?php
require_once 'functions.php';
session_start();

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = sanitizeInput($_POST['description']);
    $requiredByDate = sanitizeInput($_POST['required_by_date'] ?? '');
    $customerName = sanitizeInput($_POST['customer_name']);
    $customerEmail = sanitizeInput($_POST['customer_email']);
    $customerPhone = sanitizeInput($_POST['customer_phone'] ?? '');

    // Handle file upload
    $attachmentPath = null;
    $attachmentFilename = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/quotes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $safeFilename = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $_FILES['attachment']['name']);
        $uniqueFilename = time() . '_' . $safeFilename;

        $targetPath = $uploadDir . $uniqueFilename;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
            $attachmentPath = $targetPath;
            $attachmentFilename = $_FILES['attachment']['name'];
        }
    }

    $quoteData = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'customer_phone' => $customerPhone,
        'description' => $description,
        'required_by_date' => $requiredByDate,
        'attachment_path' => $attachmentPath,
        'attachment_filename' => $attachmentFilename
    ];

    $result = createQuoteRequest($quoteData);

    if ($result) {
        $message = '<div class="success-message">Quote request submitted successfully! Your reference number is: <strong>' . $result['quote_number'] . '</strong></div>';
    } else {
        $message = '<div class="error-message">There was an error submitting your quote request. Please try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Quote - Forge 720</title>
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
                        Cart (<span id="cart-count">0</span>)
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="auth-section">
        <section class="section">
            <div class="auth-card">
                <h2>Request a Custom Quote</h2>
                <p class="quote-subtitle">Tell us about your custom metal fabrication project and we'll provide a detailed quote.</p>

                <?php echo $message; ?>

                <form method="POST" enctype="multipart/form-data" id="quoteForm">
                    <!-- Project Details Section -->
                    <div class="quote-form-section">
                        <h3 class="section-title">Project Details</h3>
                        <div class="form-group">
                            <label for="description">Project Description *</label>
                            <textarea id="description" name="description" class="form-control" rows="5" required
                                placeholder="Describe your custom metal fabrication project. Include dimensions, materials, finish preferences, and any specific requirements."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="required_by_date">Required By Date</label>
                            <input type="date" id="required_by_date" name="required_by_date" class="form-control"
                                   min="<?php echo date('Y-m-d', strtotime('+1 week')); ?>">
                            <small class="form-hint">Leave blank if no specific deadline</small>
                        </div>

                        <div class="form-group">
                            <label for="attachment">Attach Files (Optional)</label>
                            <input type="file" id="attachment" name="attachment" class="form-control"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg,.skp">
                            <small class="form-hint">Upload sketches, plans, or reference images (Max 10MB)</small>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="quote-form-section">
                        <h3 class="section-title">Contact Information</h3>
                        <div class="form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control"
                                   value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="customer_email">Email Address *</label>
                            <input type="email" id="customer_email" name="customer_email" class="form-control"
                                   value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="customer_phone">Phone Number</label>
                            <input type="tel" id="customer_phone" name="customer_phone" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-full">Submit Quote Request</button>
                    <p class="quote-notice">
                        ✓ We'll review your request and get back to you within 1-2 business days with a detailed quote.
                    </p>
                </form>

                <p class="quote-divider"></p>

                <!-- What We Need Section -->
                <div class="quote-info-section">
                    <h3 class="info-title">What We Need to Provide an Accurate Quote</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">📏</div>
                            <h4>Dimensions</h4>
                            <p>Length, width, height, and any specific measurements</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">🔨</div>
                            <h4>Material</h4>
                            <p>Type of metal and any specific material requirements</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">✨</div>
                            <h4>Finish</h4>
                            <p>Desired finish, color, and surface treatment</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">📦</div>
                            <h4>Quantity</h4>
                            <p>How many pieces you need</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">⏱️</div>
                            <h4>Timeline</h4>
                            <p>When you need the project completed</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">🔧</div>
                            <h4>Installation</h4>
                            <p>Whether you need installation services</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
    <script>
        // Form validation
        document.getElementById('quoteForm').addEventListener('submit', function(e) {
            const description = document.getElementById('description').value.trim();
            const customerName = document.getElementById('customer_name').value.trim();
            const customerEmail = document.getElementById('customer_email').value.trim();

            if (!description || !customerName || !customerEmail) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(customerEmail)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }

            // File size validation
            const fileInput = document.getElementById('attachment');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 10) {
                    e.preventDefault();
                    alert('File size must be less than 10MB.');
                    return;
                }
            }
        });

        // File input feedback
        document.getElementById('attachment').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                console.log(`Selected file: ${file.name} (${fileSize} MB)`);
            }
        });
    </script>
</body>
</html>