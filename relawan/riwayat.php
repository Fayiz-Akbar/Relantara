<?php
include '../core/auth_guard.php';
checkRole(['relawan']);
include '../config/db_connect.php';

$id_relawan = $_SESSION['user_id'];

$sql = "SELECT p.*, k.judul, k.tanggal_mulai, k.lokasi, pen.nama_organisasi 
        FROM tbl_pendaftaran p
        JOIN tbl_kegiatan k ON p.id_kegiatan = k.id_kegiatan
        JOIN tbl_penyelenggara pen ON k.id_penyelenggara = pen.id_penyelenggara
        WHERE p.id_relawan = ?
        ORDER BY p.tanggal_daftar DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_relawan);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Kegiatan - Relantara</title>
    <style>
        /* Gunakan style dasar yang sama agar konsisten */
        body { font-family: sans-serif; background-color: #F4F6F8; margin: 0; }
        .header { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header h1 { color: #4A90E2; margin: 0; }
        .header a { text-decoration: none; color: #555; margin-left: 1.5rem; font-weight: 500; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        
        .card { background: #fff; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .card-info h3 { margin: 0 0 0.5rem 0; color: #333; }
        .card-info p { margin: 0.2rem 0; color: #666; font-size: 0.9rem; }
        
        .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-weight: bold; font-size: 0.85rem; }
        .status-Pending { background: #FFF3E0; color: #F57C00; }
        .status-Diterima { background: #E8F5E9; color: #2E7D32; }
        .status-Ditolak { background: #FFEBEE; color: #C62828; }
        .status-Selesai { background: #E3F2FD; color: #1565C0; }
    </style>
</head>
<body>
    <div class="header">
    <h1>Relantara </h1>
    <nav class="header-nav">
        <a href="index.php" style= "color: #000000ff;">Beranda</a>
        <a href="riwayat.php" style= "color: #000000ff;">Riwayat Pendaftaran</a>
        <a href="profil.php "style= "color: #000000ff;">Profil </a>
        <a href="../proses/logout.php" style="color: #C62828;">Logout</a>
    </nav>
</div>

    <div class="container">
        <h2>Riwayat Pendaftaran Saya</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-info">
                        <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                        <p><strong>Penyelenggara:</strong> <?php echo htmlspecialchars($row['nama_organisasi']); ?></p>
                        <p>📅 <?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?> | 📍 <?php echo htmlspecialchars($row['lokasi']); ?></p>
                        <p style="margin-top: 0.5rem;"><em>Daftar pada: <?php echo date('d M Y H:i', strtotime($row['tanggal_daftar'])); ?></em></p>
                    </div>
                    <div class="card-status">
                        <span class="status-badge status-<?php echo $row['status_pendaftaran']; ?>">
                            <?php echo $row['status_pendaftaran']; ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; color: #777;">Anda belum mendaftar di kegiatan apapun.</p>
        <?php endif; ?>
    </div>
</body>
</html>