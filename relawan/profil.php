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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Relantara</title>
    <style>
        /* --- CSS GLOBAL & HEADER (SAMA DENGAN INDEX.PHP) --- */
        * { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #F8FAFC; margin: 0; color: #1e293b; }
        
        .header {
            background-color: #FFFFFF;
            padding: 0.8rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            margin-bottom: 2rem;
        }
        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            color: #4A90E2;
            font-size: 1.5rem;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: #4A90E2;
        }
        .user-menu a {
             color: #ef4444;
             text-decoration: none;
             font-weight: 600;
             font-size: 0.9rem;
        }

        /* --- CSS KHUSUS FORM PROFIL --- */
        .container { 
            max-width: 700px; 
            margin: 0 auto 4rem auto; 
            padding: 0 1.5rem; 
        }
        
        .card {
            background: #FFFFFF; 
            border-radius: 12px; 
            padding: 2.5rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f5f9;
        }

        h2 { margin-top: 0; margin-bottom: 1.5rem; color: #1e293b; font-size: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.95rem; }
        
        input[type="text"], textarea { 
            width: 100%; 
            padding: 0.75rem 1rem; 
            border: 1px solid #cbd5e1; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .file-input-wrapper {
            padding: 1rem;
            border: 2px dashed #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            text-align: center;
        }
        
        button { 
            width: 100%; 
            padding: 0.875rem; 
            background-color: #4A90E2; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: 600; 
            font-size: 1rem; 
            cursor: pointer; 
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 1rem;
        }
        button:hover { background: #357ABD; }
        button:active { transform: translateY(1px); }
        
        .alert { 
            padding: 1rem; 
            margin-bottom: 1.5rem; 
            border-radius: 6px; 
            font-weight: 500;
            text-align: center;
        }
        .alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        
        .current-photo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #64748b;
        }
        .mini-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="container-fluid">
            <a href="../index.php" class="logo">Relantara</a>
            <nav class="nav-links">
                <a href="index.php">Cari Kegiatan</a>
                <a href="riwayat.php">Riwayat Pendaftaran</a>
                <a href="profil.php" class="active">Profil</a>
            </nav>
            <div class="user-menu">
                <a href="../proses/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h2>Edit Profil Saya</h2>
            
            <?php if (isset($_SESSION['message'])): ?>
                <?php $msg_type = (strpos($_SESSION['message'], 'Gagal') !== false) ? 'alert-error' : 'alert-success'; ?>
                <div class="alert <?php echo $msg_type; ?>">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Bio Singkat</label>
                    <textarea name="bio" rows="4" placeholder="Ceritakan sedikit tentang diri Anda..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Keahlian (Pisahkan dengan koma)</label>
                    <input type="text" name="keahlian" value="<?php echo htmlspecialchars($user['keahlian'] ?? ''); ?>" placeholder="Contoh: Desain Grafis, Mengajar, P3K">
                </div>
                
                <div class="form-group">
                    <label>Foto Profil</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="foto_profil" accept="image/*">
                    </div>
                </div>
                
                <button type="submit">Simpan Perubahan</button>
            </form>
        </div>
    </div>

</body>
</html>