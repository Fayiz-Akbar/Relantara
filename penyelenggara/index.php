<?php
include '../core/auth_guard.php';

checkRole(['penyelenggara']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kegiatan - <?php echo htmlspecialchars($_SESSION['nama']); ?></title>
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F8;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #FFFFFF;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #4A90E2;
            margin: 0;
            font-size: 1.5rem;
        }
        .header a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
        }
        .container {
            padding: 2rem;
            max-width: 900px;
            margin: 2rem auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        .btn-green {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #34A853; 
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }
        .btn-green:hover {
            background-color: #2E8B45;
        }
        .welcome-msg {
            margin-bottom: 2rem;
            font-size: 1.1rem;
            color: #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relantara (Penyelenggara)</h1>
        <a href="../proses/logout.php">Logout</a>
    </div>

    <div class="container">
        <div class="welcome-msg">
            Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!
        </div>
        
        <h2>Manajemen Kegiatan Saya</h2>
        <p>
            Ini adalah halaman utama Anda untuk mengelola lowongan kegiatan 
            dan melihat siapa saja yang mendaftar.
        </p>

        <a href="kegiatan_form.php" class="btn-green">
            + Buat Lowongan Kegiatan Baru
        </a>
        
        <hr style="margin: 2rem 0;">
        
        <h3>Daftar Kegiatan Anda</h3>
        <p>
            (Di Tahap 3, tabel yang berisi daftar kegiatan yang sudah Anda buat akan 
            muncul di sini, lengkap dengan tombol Edit, Hapus, dan Lihat Pendaftar)
        </p>
    </div>

</body>
</html>