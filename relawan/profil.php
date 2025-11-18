<?php
include '../core/auth_guard.php';
checkRole(['relawan']);
include '../config/db_connect.php';

$id_relawan = $_SESSION['user_id'];
$sql = "SELECT * FROM tbl_relawan WHERE id_relawan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_relawan);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil - Relantara</title>
    <style>
        body { font-family: sans-serif; background-color: #F4F6F8; margin: 0; }
        .header { background: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header h1 { color: #4A90E2; margin: 0; }
        .header a { text-decoration: none; color: #555; margin-left: 1.5rem; font-weight: 500; }
        .container { max-width: 600px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #555; }
        input[type="text"], textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 0.75rem; background: #4A90E2; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        button:hover { background: #357ABD; }
        
        .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; background: #E8F5E9; color: #2E7D32; border: 1px solid #A5D6A7; text-align: center; }
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
        <h2 style="margin-top: 0;">Edit Profil Saya</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
            </div>
            <div class="form-group">
                <label>Bio Singkat</label>
                <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Keahlian (Pisahkan dengan koma)</label>
                <input type="text" name="keahlian" value="<?php echo htmlspecialchars($user['keahlian']); ?>" placeholder="Contoh: Desain, Mengajar, P3K">
            </div>
            <div class="form-group">
                <label>Ganti Foto Profil (Opsional)</label>
                <input type="file" name="foto_profil">
                <?php if($user['foto_profil']): ?>
                    <p style="font-size: 0.8rem; margin-top: 5px;">File saat ini: <?php echo htmlspecialchars($user['foto_profil']); ?></p>
                <?php endif; ?>
            </div>
            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>