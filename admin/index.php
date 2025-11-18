<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$sql_relawan = "SELECT COUNT(id_relawan) as total_relawan FROM tbl_relawan WHERE deleted_at IS NULL";
$sql_penyelenggara = "SELECT COUNT(id_penyelenggara) as total_penyelenggara FROM tbl_penyelenggara WHERE deleted_at IS NULL";
$sql_kegiatan = "SELECT COUNT(id_kegiatan) as total_kegiatan FROM tbl_kegiatan WHERE deleted_at IS NULL AND status_kegiatan = 'Published'";
$sql_pending = "SELECT COUNT(id_penyelenggara) as total_pending FROM tbl_penyelenggara WHERE status_verifikasi = 'Pending' AND deleted_at IS NULL";

$total_relawan = $conn->query($sql_relawan)->fetch_assoc()['total_relawan'];
$total_penyelenggara = $conn->query($sql_penyelenggara)->fetch_assoc()['total_penyelenggara'];
$total_kegiatan = $conn->query($sql_kegiatan)->fetch_assoc()['total_kegiatan'];
$total_pending = $conn->query($sql_pending)->fetch_assoc()['total_pending'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Relantara</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F8;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #FFFFFF;
            border-right: 1px solid #E0E0E0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid #E0E0E0;
        }
        .sidebar-header h1 {
            color: #4A90E2;
            margin: 0;
            font-size: 1.5rem;
        }
        .sidebar-nav {
            flex-grow: 1;
            padding: 1rem 0;
        }
        .sidebar-nav a {
            display: block;
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #555;
            font-weight: 500;
            border-left: 4px solid transparent;
        }
        .sidebar-nav a.active,
        .sidebar-nav a:hover {
            background-color: #F4F6F8;
            color: #4A90E2;
            border-left-color: #4A90E2;
        }
        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #E0E0E0;
        }
        .sidebar-footer a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #C62828;
            font-weight: 500;
        }
        .main-content {
            flex-grow: 1;
            padding: 2rem;
            background-color: #F4F6F8;
        }
        .main-header {
            margin-bottom: 2rem;
        }
        .main-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8rem;
        }
        .main-header p {
            color: #555;
            font-size: 1.1rem;
        }
        .stat-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .stat-card {
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            margin: 0 0 0.5rem 0;
            color: #555;
            font-size: 1rem;
        }
        .stat-card .value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #4A90E2;
        }
        .stat-card.pending .value {
            color: #F5A623;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Admin Relantara</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="active">Dashboard</a>
            <a href="verifikasi.php">Verifikasi Penyelenggara</a>
            <a href="manage_kegiatan.php">Manajemen Kegiatan</a>
            <a href="manage_pengguna.php">Manajemen Pengguna</a>
            <a href="manage_kategori.php">Manajemen Kategori</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../proses/logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="main-header">
            <h2>Dashboard</h2>
            <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</p>
        </div>

        <div class="stat-container">
            <div class="stat-card pending">
                <h3>Menunggu Verifikasi</h3>
                <div class="value"><?php echo $total_pending; ?></div>
            </div>
            <div class="stat-card">
                <h3>Relawan Terdaftar</h3>
                <div class="value"><?php echo $total_relawan; ?></div>
            </div>
            <div class="stat-card">
                <h3>Penyelenggara Aktif</h3>
                <div class="value"><?php echo $total_penyelenggara; ?></div>
            </div>
            <div class="stat-card">
                <h3>Kegiatan Terbit</h3>
                <div class="value"><?php echo $total_kegiatan; ?></div>
            </div>
        </div>
    </div>
</body>
</html>