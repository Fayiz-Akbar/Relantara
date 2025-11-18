<?php
session_start();
include 'config/db_connect.php';

$id_kegiatan = $_GET['id'] ?? 0;

$sql = "SELECT k.*, p.nama_organisasi, p.logo as logo_org 
        FROM tbl_kegiatan k
        JOIN tbl_penyelenggara p ON k.id_penyelenggara = p.id_penyelenggara
        WHERE k.id_kegiatan = ? AND k.status_kegiatan = 'Published' AND k.deleted_at IS NULL";
// -------------------------------

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_kegiatan);
$stmt->execute();
$result = $stmt->get_result();
$kegiatan = $result->fetch_assoc();

if (!$kegiatan) {
    die("Kegiatan tidak ditemukan atau telah dihapus.");
}

// Cek status pendaftaran jika user adalah relawan
$status_pendaftaran = null;
if (isset($_SESSION['role']) && $_SESSION['role'] == 'relawan') {
    $sql_cek = "SELECT status_pendaftaran FROM tbl_pendaftaran WHERE id_relawan = ? AND id_kegiatan = ?";
    $stmt_cek = $conn->prepare($sql_cek);
    $stmt_cek->bind_param("ii", $_SESSION['user_id'], $id_kegiatan);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();
    if ($res_cek->num_rows > 0) {
        $status_pendaftaran = $res_cek->fetch_assoc()['status_pendaftaran'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($kegiatan['judul']); ?> - Relantara</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #F4F6F8; margin: 0; color: #333; }
        .header { background-color: #FFFFFF; padding: 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header .container { max-width: 1100px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header .logo { color: #4A90E2; font-size: 1.5rem; font-weight: bold; text-decoration: none; }
        
        .main-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card-detail { background-color: #FFFFFF; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        
        .poster-banner { width: 100%; max-height: 400px; object-fit: cover; background-color: #ddd; }
        
        .content-wrapper { padding: 2rem; }
        .org-header { display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem; }
        .org-logo { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; }
        .org-label { color: #4A90E2; font-weight: 600; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 0.5px; }
        
        h1 { margin: 0 0 1.5rem 0; font-size: 2rem; color: #333; }
        
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; background: #F8F9FA; padding: 1.5rem; border-radius: 8px; }
        .info-item label { display: block; font-size: 0.85rem; color: #777; margin-bottom: 0.25rem; }
        .info-item span { font-weight: 600; color: #333; font-size: 1.05rem; }
        
        .desc-section h3 { border-bottom: 2px solid #F4F6F8; padding-bottom: 0.5rem; margin-top: 2rem; }
        .desc-content { line-height: 1.8; color: #444; }
        
        .action-bar { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #eee; text-align: center; }
        .btn-daftar { display: inline-block; background-color: #F5A623; color: white; padding: 1rem 3rem; font-size: 1.2rem; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: background 0.2s; }
        .btn-daftar:hover { background-color: #DA911F; }
        .btn-disabled { background-color: #ccc; cursor: not-allowed; }
        
        .message-box { padding: 1rem; border-radius: 4px; margin-bottom: 1rem; text-align: center; }
        .msg-success { background-color: #E8F5E9; color: #2E7D32; border: 1px solid #A5D6A7; }
        .msg-error { background-color: #FFEBEE; color: #C62828; border: 1px solid #EF9A9A; }
        
        @media (max-width: 600px) { .main-container { padding: 0; } .content-wrapper { padding: 1.5rem; } }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Relantara</a>
            <a href="kegiatan.php" style="text-decoration: none; color: #555;">&larr; Kembali</a>
        </div>
    </header>

    <div class="main-container">
        
        <?php if (isset($_SESSION['message'])): ?>
            <?php 
                $msg_type = (strpos($_SESSION['message'], 'Gagal') !== false) ? 'msg-error' : 'msg-success';
            ?>
            <div class="message-box <?php echo $msg_type; ?>">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="card-detail">
            <img src="uploads/poster_kegiatan/<?php echo htmlspecialchars($kegiatan['gambar_poster'] ?? 'default_poster.jpg'); ?>" class="poster-banner" alt="Poster">
            
            <div class="content-wrapper">
                <div class="org-header">
                    <div class="org-label"><?php echo htmlspecialchars($kegiatan['nama_organisasi']); ?></div>
                </div>
                
                <h1><?php echo htmlspecialchars($kegiatan['judul']); ?></h1>

                <div class="info-grid">
                    <div class="info-item">
                        <label>Lokasi</label>
                        <span><?php echo htmlspecialchars($kegiatan['lokasi']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Tanggal Mulai</label>
                        <span><?php echo date('d M Y', strtotime($kegiatan['tanggal_mulai'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Kuota</label>
                        <span>
                            <?php echo ($kegiatan['kuota'] == 0) ? 'Tidak Terbatas' : htmlspecialchars($kegiatan['kuota']) . ' Orang'; ?>
                        </span>
                    </div>
                     <div class="info-item">
                        <label>Waktu</label>
                        <span>
                           <?php 
                                $jam_mulai = !empty($kegiatan['waktu_mulai']) ? date('H:i', strtotime($kegiatan['waktu_mulai'])) : '-';
                                $jam_selesai = !empty($kegiatan['waktu_selesai']) ? date('H:i', strtotime($kegiatan['waktu_selesai'])) : '-';
                                echo $jam_mulai . ' - ' . $jam_selesai;
                            ?>
                        </span>
                    </div>
                </div>

                <div class="desc-section">
                    <h3>Deskripsi Kegiatan</h3>
                    <div class="desc-content">
                        <?php echo nl2br(htmlspecialchars($kegiatan['deskripsi'])); ?>
                    </div>

                    <h3>Benefit Relawan</h3>
                    <div class="desc-content">
                        <?php echo nl2br(htmlspecialchars($kegiatan['benefit'])); ?>
                    </div>
                </div>

                <div class="action-bar">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <p style="margin-bottom: 1rem;">Anda harus login untuk mendaftar.</p>
                        <a href="login.php" class="btn-daftar">Login untuk Daftar</a>
                    
                    <?php elseif ($_SESSION['role'] != 'relawan'): ?>
                        <button class="btn-daftar btn-disabled" disabled>Hanya Relawan yang Bisa Daftar</button>
                    
                    <?php elseif ($status_pendaftaran): ?>
                        <button class="btn-daftar btn-disabled" disabled>
                            Status: <?php echo $status_pendaftaran; ?>
                        </button>
                        <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #666;">Anda sudah mendaftar di kegiatan ini.</p>
                    
                    <?php else: ?>
                        <form action="proses/proses_pendaftaran.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mendaftar kegiatan ini?');">
                            <input type="hidden" name="id_kegiatan" value="<?php echo $kegiatan['id_kegiatan']; ?>">
                            <button type="submit" class="btn-daftar">Daftar Sekarang</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>