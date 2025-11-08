<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);
include '../config/db_connect.php';

$page_title = "Buat Kegiatan Baru";
$form_action = "proses_kegiatan.php";
$kegiatan = [
    'judul' => '',
    'deskripsi' => '',
    'lokasi' => '',
    'tanggal_mulai' => '',
    'tanggal_selesai' => '',
    'waktu_mulai' => '',
    'waktu_selesai' => '',
    'kuota' => 0,
    'benefit' => '',
    'id_kegiatan' => null,
    'gambar_poster' => null
];

if (isset($_GET['edit_id'])) {
    $page_title = "Edit Kegiatan";
    $id_kegiatan = $_GET['edit_id'];
    $id_penyelenggara_login = $_SESSION['user_id'];

    $sql = "SELECT * FROM tbl_kegiatan 
            WHERE id_kegiatan = ? AND id_penyelenggara = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_kegiatan, $id_penyelenggara_login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $kegiatan = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Kegiatan tidak ditemukan atau Anda tidak punya akses.";
        header("Location: index.php");
        exit;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Relantara</title>
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
        .header-nav a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            margin-left: 1.5rem;
        }
        .header-nav a:hover {
            color: #F5A623;
        }
        .container {
            padding: 2rem;
            max-width: 900px;
            margin: 2rem auto;
        }
        .page-header {
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .back-link {
            display: inline-block;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.5rem;
            transition: color 0.2s ease;
        }
        .back-link:hover {
            color: #4A90E2;
        }
        .page-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8rem;
        }
        .form-container {
            background-color: #FFFFFF;
            padding: 2rem 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-group-inline {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .btn-primary {
            background-color: #F5A623;
            color: white;
        }
        .btn-primary:hover {
            background-color: #DA911F;
        }
        .btn-delete {
            background-color: #C62828;
            color: white;
            margin-left: 1rem;
        }
        .btn-delete:hover {
            background-color: #A91B1B;
        }
        
        @media (max-width: 600px) {
            .form-group-inline {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relantara </h1>
        <nav class="header-nav">
            <a href="index.php">Kegiatan Saya</a>
            <a href="profil.php">Profil</a>
            <a href="../proses/logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <div class="page-header">
            <a href="index.php" class="back-link" title="Kembali ke Manajemen Kegiatan">&larr;</a>
            <h2><?php echo $page_title; ?></h2>
        </div>

        <div class="form-container">
            <form action="proses_kegiatan.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_kegiatan" value="<?php echo $kegiatan['id_kegiatan']; ?>">

                <div class="form-group">
                    <label for="judul">Judul Kegiatan</label>
                    <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($kegiatan['judul']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Deskripsi Lengkap Kegiatan</label>
                    <textarea id="deskripsi" name="deskripsi" rows="6" required><?php echo htmlspecialchars($kegiatan['deskripsi']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="benefit">Benefit untuk Relawan</label>
                    <textarea id="benefit" name="benefit" rows="4" placeholder="Contoh: Sertifikat, Konsumsi, Transport, Relasi"><?php echo htmlspecialchars($kegiatan['benefit']); ?></textarea>
                </div>
                
                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="lokasi">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi" value="<?php echo htmlspecialchars($kegiatan['lokasi']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kuota">Kuota Relawan (Isi 0 jika tidak terbatas)</label>
                        <input type="number" id="kuota" name="kuota" min="0" value="<?php echo htmlspecialchars($kegiatan['kuota']); ?>" required>
                    </div>
                </div>

                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($kegiatan['tanggal_mulai']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo htmlspecialchars($kegiatan['tanggal_selesai']); ?>" required>
                    </div>
                </div>

                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="waktu_mulai">Waktu Mulai</label>
                        <input type="time" id="waktu_mulai" name="waktu_mulai" value="<?php echo htmlspecialchars($kegiatan['waktu_mulai']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="waktu_selesai">Waktu Selesai</label>
                        <input type="time" id="waktu_selesai" name="waktu_selesai" value="<?php echo htmlspecialchars($kegiatan['waktu_selesai']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="gambar_poster">Gambar Poster</label>
                    <input type="file" id="gambar_poster" name="gambar_poster" accept="image/jpeg, image/png">
                    <?php if ($kegiatan['id_kegiatan'] && $kegiatan['gambar_poster']): ?>
                        <p style="font-size: 0.9rem; color: #555;">Poster saat ini: <?php echo htmlspecialchars($kegiatan['gambar_poster']); ?> (Kosongkan jika tidak ingin ganti)</p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="action" value="save" class="btn btn-primary">Simpan Kegiatan</button>
                <?php if ($kegiatan['id_kegiatan']): ?>
                    <button type="submit" name="action" value="delete" class="btn btn-delete" onclick="return confirm('Anda yakin ingin menghapus kegiatan ini?');">Hapus Kegiatan</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

</body>
</html>