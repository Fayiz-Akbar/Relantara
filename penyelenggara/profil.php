<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);
include '../config/db_connect.php';

$id_penyelenggara = $_SESSION['user_id'];

// Ambil data terbaru dari database
$sql = "SELECT * FROM tbl_penyelenggara WHERE id_penyelenggara = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_penyelenggara);
$stmt->execute();
$result = $stmt->get_result();
$profil = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Organisasi - Relantara</title>
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
            max-width: 800px;
            margin: 2rem auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        .page-header {
            margin-bottom: 2rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }
        .page-header h2 {
            margin: 0;
            color: #333;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
            font-family: inherit;
            font-size: 1rem;
        }
        .form-group-inline {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .logo-preview {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 0.5rem;
            background-color: #f9f9f9;
        }
        .btn {
            padding: 0.75rem 1.5rem;
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
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }
        .message-success {
            background-color: #E8F5E9;
            border: 1px solid #81C784;
            color: #2E7D32;
        }
        .message-error {
            background-color: #FFEBEE;
            border: 1px solid #E57373;
            color: #C62828;
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
        <h1>Relantara (Penyelenggara)</h1>
        <nav class="header-nav">
            <a href="index.php">Kegiatan Saya</a>
            <a href="profil.php" style="color: #F5A623;">Profil</a>
            <a href="../proses/logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <div class="page-header">
            <h2>Edit Profil Organisasi</h2>
        </div>

        <?php
        if (isset($_SESSION['message'])) {
            $msg_class = (strpos(strtolower($_SESSION['message']), 'gagal') !== false) ? 'message-error' : 'message-success';
            echo "<div class='message $msg_class'>" . htmlspecialchars($_SESSION['message']) . "</div>";
            unset($_SESSION['message']);
        }
        ?>

        <form action="proses_profil.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Nama Organisasi</label>
                <input type="text" name="nama_organisasi" value="<?php echo htmlspecialchars($profil['nama_organisasi']); ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="4" placeholder="Jelaskan tentang organisasi Anda..."><?php echo htmlspecialchars($profil['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" rows="2"><?php echo htmlspecialchars($profil['alamat']); ?></textarea>
            </div>

            <div class="form-group-inline">
                <div class="form-group">
                    <label>Email Kontak (Publik)</label>
                    <input type="email" name="kontak_email" value="<?php echo htmlspecialchars($profil['kontak_email']); ?>">
                </div>
                <div class="form-group">
                    <label>No. Telepon / WhatsApp</label>
                    <input type="tel" name="kontak_telp" value="<?php echo htmlspecialchars($profil['kontak_telp']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Logo Organisasi</label>
                <input type="file" name="logo" accept="image/png, image/jpeg, image/jpg">
                <?php if (!empty($profil['logo'])): ?>
                    <br>
                    <img src="../uploads/logo_penyelenggara/<?php echo htmlspecialchars($profil['logo']); ?>" alt="Logo Saat Ini" class="logo-preview">
                    <p style="font-size: 0.85rem; color: #666; margin-top: 5px;">Logo saat ini (Biarkan kosong jika tidak ingin mengubah)</p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

</body>
</html>