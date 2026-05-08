<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Forge720</title>
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
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main style="padding-top: 100px;">
        <section class="section">
            <h2>Reset Your Password</h2>
            <div class="form-container">
                <?php
                session_start();
                require_once 'functions.php';

                $message = '';
                $emailSent = false;

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $email = sanitizeInput($_POST['email']);

                    if (empty($email)) {
                        $message = "Please enter your email address.";
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $message = "Please enter a valid email address.";
                    } else {
                        $result = generatePasswordResetToken($email);

                        if (is_array($result)) {
                            // Send email (in development, we'll display the link)
                            $emailData = sendPasswordResetEmail($email, $result['token']);

                            // For development purposes, display the reset link
                            $message = "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                                <strong>Development Mode:</strong> Password reset link generated successfully!<br><br>
                                <strong>Reset Link:</strong><br>
                                <a href='reset_password.php?token=" . $result['token'] . "' style='color: #007bff;'>reset_password.php?token=" . $result['token'] . "</a><br><br>
                                <em>In production, this link would be sent to: " . $email . "</em>
                            </div>";
                            $emailSent = true;
                        } else {
                            $message = "<p style='color: red; text-align: center;'>$result</p>";
                        }
                    }
                }
                ?>

                <?php if ($message && !$emailSent): ?>
                    <p style="color: red; text-align: center;"><?php echo $message; ?></p>
                <?php endif; ?>

                <?php if ($emailSent): ?>
                    <?php echo $message; ?>
                    <p style="text-align: center;">
                        <a href="login.php">Back to Login</a>
                    </p>
                <?php else: ?>
                    <p style="text-align: center; margin-bottom: 2rem;">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>

                    <form id="forgotPasswordForm" method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <button type="submit" class="btn" style="width: 100%;">Send Reset Link</button>
                    </form>

                    <p style="text-align: center; margin-top: 1rem;">
                        Remember your password? <a href="login.php">Back to Login</a>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>