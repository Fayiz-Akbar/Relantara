<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);
include '../config/db_connect.php';

$id_penyelenggara = $_SESSION['user_id'];
$id_kegiatan = $_GET['id_kegiatan'] ?? 0;

// 1. Verifikasi bahwa kegiatan ini milik penyelenggara yang login
$sql_cek = "SELECT judul FROM tbl_kegiatan WHERE id_kegiatan = ? AND id_penyelenggara = ?";
$stmt_cek = $conn->prepare($sql_cek);
$stmt_cek->bind_param("ii", $id_kegiatan, $id_penyelenggara);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();
$data_kegiatan = $result_cek->fetch_assoc();

if (!$data_kegiatan) {
    // Jika kegiatan tidak ditemukan atau bukan miliknya
    header("Location: index.php");
    exit;
}

// 2. Ambil daftar pendaftar
$sql_pendaftar = "SELECT p.*, r.nama_lengkap, r.email, r.keahlian, r.foto_profil 
                  FROM tbl_pendaftaran p
                  JOIN tbl_relawan r ON p.id_relawan = r.id_relawan
                  WHERE p.id_kegiatan = ?
                  ORDER BY p.tanggal_daftar DESC";
$stmt_pendaftar = $conn->prepare($sql_pendaftar);
$stmt_pendaftar->bind_param("i", $id_kegiatan);
$stmt_pendaftar->execute();
$result_pendaftar = $stmt_pendaftar->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftar: <?php echo htmlspecialchars($data_kegiatan['judul']); ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #F4F6F8; margin: 0; }
        .header { background-color: #FFFFFF; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #4A90E2; margin: 0; font-size: 1.5rem; }
        .header a { color: #555; text-decoration: none; font-weight: 500; margin-left: 1.5rem; }
        
        .container { padding: 2rem; max-width: 1000px; margin: 2rem auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .back-link { text-decoration: none; color: #555; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
        .back-link:hover { color: #4A90E2; }

        .card { background: #fff; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; gap: 1.5rem; align-items: start; }
        .avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; background: #eee; }
        .info { flex: 1; }
        .info h3 { margin: 0 0 0.3rem 0; color: #333; }
        .meta { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .reason { background: #F9F9F9; padding: 0.8rem; border-radius: 4px; font-size: 0.95rem; color: #444; margin-top: 0.5rem; border-left: 3px solid #ddd; }
        
        .actions { display: flex; gap: 0.5rem; flex-direction: column; min-width: 120px; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; color: white; text-align: center; font-size: 0.9rem; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-accept { background-color: #34A853; }
        .btn-reject { background-color: #C62828; }
        .status-badge { padding: 0.5rem; text-align: center; border-radius: 4px; font-weight: 500; font-size: 0.9rem; }
        .status-Diterima { background: #E8F5E9; color: #2E7D32; }
        .status-Ditolak { background: #FFEBEE; color: #C62828; }
        
        .message-box { padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; text-align: center; background-color: #E3F2FD; color: #0D47A1; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relantara (Penyelenggara)</h1>
        <nav>
            <a href="index.php">Kegiatan Saya</a>
            <a href="../proses/logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <a href="index.php" class="back-link">&larr; Kembali</a>
                <h2 style="margin-top: 0.5rem;">Pendaftar: <?php echo htmlspecialchars($data_kegiatan['judul']); ?></h2>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message-box">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if ($result_pendaftar->num_rows > 0): ?>
            <?php while($row = $result_pendaftar->fetch_assoc()): ?>
                <div class="card">
                    <img src="../uploads/foto_profil/<?php echo htmlspecialchars($row['foto_profil'] ?? 'default_profil.png'); ?>" alt="Foto" class="avatar">
                    
                    <div class="info">
                        <h3><?php echo htmlspecialchars($row['nama_lengkap']); ?></h3>
                        <div class="meta">
                            <span>📧 <?php echo htmlspecialchars($row['email']); ?></span> | 
                            <span>📅 Daftar: <?php echo date('d M Y', strtotime($row['tanggal_daftar'])); ?></span>
                        </div>
                        <?php if (!empty($row['keahlian'])): ?>
                            <div class="meta"><strong>Keahlian:</strong> <?php echo htmlspecialchars($row['keahlian']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($row['alasan_bergabung'])): ?>
                            <div class="reason">
                                <strong>Alasan:</strong> "<?php echo htmlspecialchars($row['alasan_bergabung']); ?>"
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="actions">
                        <?php if ($row['status_pendaftaran'] == 'Pending'): ?>
                            <form action="proses_pendaftar.php" method="POST">
                                <input type="hidden" name="id_pendaftaran" value="<?php echo $row['id_pendaftaran']; ?>">
                                <input type="hidden" name="id_kegiatan" value="<?php echo $id_kegiatan; ?>">
                                <button type="submit" name="status" value="Diterima" class="btn btn-accept" style="width: 100%; margin-bottom: 5px;">Terima</button>
                                <button type="submit" name="status" value="Ditolak" class="btn btn-reject" style="width: 100%;">Tolak</button>
                            </form>
                        <?php else: ?>
                            <div class="status-badge status-<?php echo $row['status_pendaftaran']; ?>">
                                <?php echo $row['status_pendaftaran']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: white; border-radius: 8px; color: #777;">
                Belum ada relawan yang mendaftar untuk kegiatan ini.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>