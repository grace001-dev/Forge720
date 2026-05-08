<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Forge720</title>
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
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-section">
        <section class="section">
            <div class="auth-card">
                <h2>Login to Your Account</h2>
                <?php
                session_start();
                require_once 'functions.php';

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $username = sanitizeInput($_POST['username']);
                    $password = sanitizeInput($_POST['password']);

                    $result = loginUser($username, $password);

                    if ($result === true) {
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "<p class=\"form-error\">$result</p>";
                    }
                }
                ?>

                <form id="loginForm" method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username or Email:</label>
                        <input class="form-control" type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input class="form-control" type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-full">Login</button>
                </form>
                <p class="center-text link-row">
                    <a href="forgot_password.php">Forgot Password?</a>
                </p>
                <p class="center-text link-row">
                    Don't have an account? <a href="register.php">Register here</a>
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