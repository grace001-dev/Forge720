<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Forge720</title>
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
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="active">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-section">
        <section class="section">
            <div class="auth-card">
                <h2>Create Your Account</h2>
                <?php
                session_start();
                require_once 'functions.php';

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $username = sanitizeInput($_POST['username']);
                    $email = sanitizeInput($_POST['email']);
                    $password = sanitizeInput($_POST['password']);
                    $confirm_password = sanitizeInput($_POST['confirm_password']);

                    if ($password !== $confirm_password) {
                        echo "<p class='form-error'>Passwords do not match.</p>";
                    } elseif (strlen($password) < 6) {
                        echo "<p class='form-error'>Password must be at least 6 characters long.</p>";
                    } else {
                        $result = registerUser($username, $email, $password);

                        if ($result === true) {
                            echo "<p class='success-message'>Registration successful! <a href='login.php'>Login here</a></p>";
                        } else {
                            echo "<p class='form-error'>$result</p>";
                        }
                    }
                }
                ?>

                <form id="registerForm" method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input class="form-control" type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input class="form-control" type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input class="form-control" type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-full">Register</button>
                </form>
                <p class="center-text link-row">
                    Already have an account? <a href="login.php">Login here</a>
                </p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Forge720. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>