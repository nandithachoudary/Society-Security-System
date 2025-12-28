<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Society Security System - Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome</h1>
        <form class="login-form" action="login_process.php" method="post">
            <h2>User Login</h2>
            <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error">Invalid username or password.</p>';
                }
            ?>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>