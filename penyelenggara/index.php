<?php
include '../core/auth_guard.php';

checkRole(['admin']); 
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Dashboard Admin</title></head>
<body>
    <h1>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p>Ini adalah halaman Dashboard Admin.</p>
    <a href="../proses/logout.php">Logout</a>
</body>
</html>