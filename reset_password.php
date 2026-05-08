<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Forge 720</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Forge 720</div>
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

                $token = isset($_GET['token']) ? sanitizeInput($_GET['token']) : '';
                $message = '';
                $showForm = false;

                if (empty($token)) {
                    $message = "<p style='color: red; text-align: center;'>Invalid reset link. Please request a new password reset.</p>";
                } else {
                    $user = verifyPasswordResetToken($token);
                    if (!$user) {
                        $message = "<p style='color: red; text-align: center;'>This reset link is invalid or has expired. Please request a new password reset.</p>";
                    } else {
                        $showForm = true;
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && $showForm) {
                    $password = sanitizeInput($_POST['password']);
                    $confirmPassword = sanitizeInput($_POST['confirm_password']);

                    if (empty($password) || empty($confirmPassword)) {
                        $message = "<p style='color: red; text-align: center;'>Please fill in all fields.</p>";
                    } elseif (strlen($password) < 6) {
                        $message = "<p style='color: red; text-align: center;'>Password must be at least 6 characters long.</p>";
                    } elseif ($password !== $confirmPassword) {
                        $message = "<p style='color: red; text-align: center;'>Passwords do not match.</p>";
                    } else {
                        $result = resetPassword($token, $password);
                        if ($result === true) {
                            $message = "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center;'>
                                <strong>Success!</strong> Your password has been reset successfully.<br>
                                <a href='login.php'>Click here to login</a>
                            </div>";
                            $showForm = false;
                        } else {
                            $message = "<p style='color: red; text-align: center;'>$result</p>";
                        }
                    }
                }
                ?>

                <?php echo $message; ?>

                <?php if ($showForm): ?>
                    <p style="text-align: center; margin-bottom: 2rem;">
                        Enter your new password below.
                    </p>

                    <form id="resetPasswordForm" method="POST" action="">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="form-group">
                            <label for="password">New Password:</label>
                            <input type="password" id="password" name="password" required minlength="6">
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>

                        <button type="submit" class="btn" style="width: 100%;">Reset Password</button>
                    </form>

                    <p style="text-align: center; margin-top: 1rem;">
                        <a href="login.php">Back to Login</a>
                    </p>
                <?php else: ?>
                    <p style="text-align: center;">
                        <a href="forgot_password.php">Request New Password Reset</a> |
                        <a href="login.php">Back to Login</a>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge 720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>