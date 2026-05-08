<?php
require_once '../functions.php';
require_once '../cart_functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$message = '';

// Handle content updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contentType = sanitizeInput($_POST['content_type']);
    $content = sanitizeInput($_POST['content']);

    // For now, we'll just show a message since we don't have a content table
    // In a real application, you'd save this to a database
    $message = ucfirst($contentType) . ' content updated successfully! (Note: This is a demo - content is not actually saved to database)';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Admin - Forge 720</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .admin-sidebar {
            background: #343a40;
            color: white;
            padding: 1rem;
        }

        .admin-sidebar h3 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #495057;
        }

        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .admin-sidebar li {
            margin-bottom: 0.5rem;
        }

        .admin-sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: #495057;
            color: white;
        }

        .admin-main {
            padding: 2rem;
            background: #f8f9fa;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.info {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #b3d7ff;
        }

        .content-sections {
            display: grid;
            gap: 2rem;
        }

        .content-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .content-section h2 {
            margin-bottom: 1rem;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            padding: 0.75rem 1.5rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #0056b3;
        }

        .content-preview {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            border: 1px solid #dee2e6;
        }

        .content-preview h3 {
            margin-bottom: 0.5rem;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="quotes.php">Quotes</a></li>
                <li><a href="content.php" class="active">Content</a></li>
            </ul>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>Content Management</h1>
                <a href="index.php" class="btn-submit" style="background: #6c757d;">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'info'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="content-sections">
                <div class="content-section">
                    <h2>About Page Content</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="content_type" value="about">
                        <div class="form-group">
                            <label for="about_content">About Page Content (HTML allowed):</label>
                            <textarea id="about_content" name="content" placeholder="Enter the about page content here..."><?php
                                // In a real application, you'd load this from the database
                                echo htmlspecialchars('<h2>About Forge 720</h2>
<p>Forge 720 is a premier custom metal fabrication company specializing in high-quality metalwork for residential, commercial, and industrial applications.</p>

<h3>Our Services</h3>
<ul>
<li>Custom Gates and Fencing</li>
<li>Staircases and Railings</li>
<li>Structural Steel Work</li>
<li>Architectural Metal Elements</li>
</ul>

<h3>Why Choose Us?</h3>
<p>With over 20 years of experience, we combine traditional craftsmanship with modern techniques to deliver exceptional results.</p>');
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Update About Content</button>
                    </form>
                </div>

                <div class="content-section">
                    <h2>Contact Page Content</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="content_type" value="contact">
                        <div class="form-group">
                            <label for="contact_content">Contact Page Content (HTML allowed):</label>
                            <textarea id="contact_content" name="content" placeholder="Enter the contact page content here..."><?php
                                echo htmlspecialchars('<h2>Contact Forge 720</h2>

<div class="contact-info">
<h3>Get In Touch</h3>
<p>Ready to start your custom metal fabrication project? Contact us today!</p>

<div class="contact-details">
<p><strong>Phone:</strong> (254) 792204330</p>
<p><strong>Email:</strong> info@forge720.com</p>
<p><strong>Address:</strong> 123 Metal Works Drive, Industrial Park, ST 12345</p>
<p><strong>Hours:</strong> Monday-Friday 8AM-6PM, Saturday 9AM-3PM</p>
</div>
</div>

<div class="contact-form-note">
<p>Fill out our quote request form or call us directly to discuss your project requirements.</p>
</div>');
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Update Contact Content</button>
                    </form>
                </div>

                <div class="content-section">
                    <h2>Home Page Hero Content</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="content_type" value="hero">
                        <div class="form-group">
                            <label for="hero_content">Hero Section Content (HTML allowed):</label>
                            <textarea id="hero_content" name="content" placeholder="Enter the hero section content here..."><?php
                                echo htmlspecialchars('<h1>Custom Metal Fabrication Excellence</h1>
<p>Transforming ideas into reality with precision craftsmanship and premium materials. From custom gates to architectural elements, we bring your vision to life.</p>
<a href="products.php" class="btn-primary">View Our Work</a>
<a href="quote_request.php" class="btn-secondary">Get a Quote</a>');
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Update Hero Content</button>
                    </form>
                </div>

                <div class="content-section">
                    <h2>Footer Content</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="content_type" value="footer">
                        <div class="form-group">
                            <label for="footer_content">Footer Content (HTML allowed):</label>
                            <textarea id="footer_content" name="content" placeholder="Enter the footer content here..."><?php
                                echo htmlspecialchars('<div class="footer-content">
<p>&copy; 2024 Forge 720. All rights reserved.</p>
<p>Specializing in custom metal fabrication for over 20 years.</p>
</div>');
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Update Footer Content</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>