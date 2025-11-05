<?php
include '../core/auth_guard.php';

checkRole(['relawan']);
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Cari Kegiatan</title></head>
<body>
    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p>Ini adalah halaman Relawan (sesuai revisi, ini adalah halaman pencarian kegiatan).</p>
    <a href="../proses/logout.php">Logout</a>
</body>
</html>