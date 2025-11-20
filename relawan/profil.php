<?php
include '../core/auth_guard.php';
checkRole(['relawan']);
include '../config/db_connect.php';

$id_relawan = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM tbl_relawan WHERE id_relawan = ?");
$stmt->bind_param("i", $id_relawan);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Relantara</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background-color: #f8fafc; 
            color: #1e293b; 
        }
        
        .header {
            background-color: #ffffff;
            padding: 0.8rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            margin-bottom: 2.5rem;
        }
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
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
            align-items: center;
        }
        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            position: relative;
        }
        .nav-links a:hover {
            color: #4A90E2;
        }
        .nav-links a.active {
            color: #4A90E2;
            font-weight: 600;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #4A90E2;
            transition: width 0.2s ease;
        }
        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }
        .user-menu a {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }
        .user-menu a:hover {
            color: #dc2626;
        }

        .container { 
            max-width: 1000px; 
            margin: 0 auto 4rem auto; 
            padding: 0 2rem; 
        }
        
        .page-title {
            margin-bottom: 2rem;
            color: #1e293b;
            font-size: 2rem;
            font-weight: 700;
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }

        .profile-sidebar {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .avatar-container {
            position: relative;
            width: 180px;
            height: 180px;
        }

        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f1f5f9;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 4rem;
            font-weight: 700;
            border: 4px solid #f1f5f9;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .change-photo-label {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #4A90E2;
            color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s;
            text-align: center;
        }

        .change-photo-label:hover {
            background-color: #357ABD;
        }

        .file-input-hidden {
            display: none;
        }

        .profile-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: #475569;
            font-size: 0.95rem;
        }

        .form-group input[type="text"],
        .form-group textarea {
            padding: 0.875rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            color: #1e293b;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            padding: 1rem;
            background-color: #4A90E2;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: #357ABD;
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .profile-layout {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }

            .avatar-container {
                width: 150px;
                height: 150px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .header-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 0.75rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-container">
            <a href="../index.php" class="logo">Relantara</a>
            <nav class="nav-links">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Cari Kegiatan</a>
                <a href="riwayat.php" class="<?php echo ($current_page == 'riwayat.php') ? 'active' : ''; ?>">Riwayat Pendaftaran</a>
                <a href="profil.php" class="<?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>">Profil</a>
            </nav>
            <div class="user-menu">
                <a href="../proses/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Edit Profil</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <?php $alert_class = (strpos($_SESSION['message'], 'Gagal') !== false) ? 'alert-error' : 'alert-success'; ?>
            <div class="alert <?php echo $alert_class; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data">
            <div class="profile-layout">
                <div class="profile-sidebar">
                    <div class="avatar-container">
                        <?php if (!empty($user['foto_profil'])): ?>
                            <img src="../uploads/foto_profil/<?php echo htmlspecialchars($user['foto_profil']); ?>" alt="Foto Profil" class="avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?php echo strtoupper(substr($user['nama_lengkap'] ?? 'U', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <label for="foto_profil" class="change-photo-label">Ubah Foto</label>
                    <input type="file" name="foto_profil" id="foto_profil" class="file-input-hidden" accept="image/*">
                </div>

                <div class="profile-form">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio Singkat</label>
                        <textarea name="bio" id="bio" placeholder="Ceritakan sedikit tentang diri Anda..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="keahlian">Keahlian (Pisahkan dengan koma)</label>
                        <input type="text" name="keahlian" id="keahlian" value="<?php echo htmlspecialchars($user['keahlian'] ?? ''); ?>" placeholder="Contoh: Desain Grafis, Mengajar, P3K">
                    </div>

                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
<?php $conn->close(); ?>