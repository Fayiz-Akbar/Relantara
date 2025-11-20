<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);
include '../config/db_connect.php';

$id_penyelenggara = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM tbl_penyelenggara WHERE id_penyelenggara = ?");
$stmt->bind_param("i", $id_penyelenggara);
$stmt->execute();
$profil = $stmt->get_result()->fetch_assoc();
$stmt->close();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Organisasi - Relantara</title>
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
        
        .logo-container {
            position: relative;
            width: 180px;
            height: 180px;
        }
        
        .logo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f1f5f9;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .logo-placeholder {
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
        
        .change-logo-label {
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
        
        .change-logo-label:hover {
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group textarea {
            padding: 0.875rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            color: #1e293b;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-group input:focus,
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
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .logo-container {
                width: 150px;
                height: 150px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .header-nav {
                flex-direction: column;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-container">
            <a href="../index.php" class="logo">Relantara</a>
            <nav class="nav-links">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Kegiatan</a>
                <a href="profil.php" class="<?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>">Profil Organisasi</a>
            </nav>
            <div class="user-menu">
                <a href="../proses/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Profil Organisasi</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <?php $alert_class = (strpos(strtolower($_SESSION['message']), 'gagal') !== false) ? 'alert-error' : 'alert-success'; ?>
            <div class="alert <?php echo $alert_class; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form action="proses_profil.php" method="POST" enctype="multipart/form-data">
            <div class="profile-layout">
                <div class="profile-sidebar">
                    <div class="logo-container">
                        <?php if (!empty($profil['logo'])): ?>
                            <img src="../uploads/logo_penyelenggara/<?php echo htmlspecialchars($profil['logo']); ?>" alt="Logo Organisasi" class="logo-img">
                        <?php else: ?>
                            <div class="logo-placeholder">
                                <?php echo strtoupper(substr($profil['nama_organisasi'] ?? 'O', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <label for="logo" class="change-logo-label">Ubah Logo</label>
                    <input type="file" name="logo" id="logo" class="file-input-hidden" accept="image/png, image/jpeg, image/jpg">
                </div>

                <div class="profile-form">
                    <div class="form-group">
                        <label for="nama_organisasi">Nama Organisasi</label>
                        <input type="text" name="nama_organisasi" id="nama_organisasi" value="<?php echo htmlspecialchars($profil['nama_organisasi']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Singkat</label>
                        <textarea name="deskripsi" id="deskripsi" placeholder="Jelaskan tentang organisasi Anda..."><?php echo htmlspecialchars($profil['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3"><?php echo htmlspecialchars($profil['alamat'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kontak_email">Email Kontak (Publik)</label>
                            <input type="email" name="kontak_email" id="kontak_email" value="<?php echo htmlspecialchars($profil['kontak_email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="kontak_telp">No. Telepon / WhatsApp</label>
                            <input type="tel" name="kontak_telp" id="kontak_telp" value="<?php echo htmlspecialchars($profil['kontak_telp'] ?? ''); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
<?php $conn->close(); ?>