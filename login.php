<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Relantara</title>
    </head>
<body>
    <h1>Login</h1>
    
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p style='color:red; background: #fee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
        unset($_SESSION['message']);
    }
    ?>

    <form action="proses/proses_login.php" method="POST">
        <div>
            <label for="email_username">Email atau Username:</label><br>
            <input type="text" id="email_username" name="email_username" required>
        </div>
        <div>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</body>
</html>