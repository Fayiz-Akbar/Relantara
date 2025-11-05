<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Relantara</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #FFFFFF;
            padding: 2rem 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            width: 100%;
            max-width: 400px;
        }
        .container h1 {
            color: #4A90E2;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }
        .btn-primary {
            background-color: #4A90E2;
            color: white;
        }
        .btn-primary:hover {
            background-color: #357ABD;
        }
        .text-center {
            text-align: center;
            margin-top: 1.5rem;
            color: #555;
        }
        .text-center a {
            color: #F5A623;
            text-decoration: none;
            font-weight: bold;
        }
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }
        .message-error {
            background-color: #FFEBEE;
            border: 1px solid #E57373;
            color: #C62828;
        }
        .message-success {
            background-color: #E8F5E9;
            border: 1px solid #81C784;
            color: #2E7D32;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login Relantara</h1>

        <?php
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $msg_class = (strpos(strtolower($message), 'gagal') !== false || strpos(strtolower($message), 'salah') !== false || strpos(strtolower($message), 'pending') !== false) 
                         ? 'message-error' : 'message-success';
            
            echo "<div class='message $msg_class'>" . htmlspecialchars($message) . "</div>";
            unset($_SESSION['message']);
        }
        ?>

        <form action="proses/proses_login.php" method="POST">
            <div class="form-group">
                <label for="email_username">Email atau Username</label>
                <input type="text" id="email_username" name="email_username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="text-center">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>