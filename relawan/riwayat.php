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

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Kegiatan - Relantara</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #F8FAFC;
            color: #1e293b;
            line-height: 1.6;
        }

        .header {
            background-color: #FFFFFF;
            padding: 0.8rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }
        
        .card {
            background: #FFFFFF;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .card-info h3 {
            margin: 0 0 0.5rem 0;
            color: #1e293b;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .card-info p {
            margin: 0.3rem 0;
            color: #64748b;
            font-size: 0.9375rem;
        }

        .card-info p strong {
            color: #475569;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .status-Pending {
            background: #FFF3E0;
            color: #F57C00;
        }

        .status-Diterima {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .status-Ditolak {
            background: #FFEBEE;
            color: #C62828;
        }

        .status-Selesai {
            background: #E3F2FD;
            color: #1565C0;
        }

        .no-data {
            text-align: center;
            padding: 3rem 2rem;
            background-color: #fff;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            .card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
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
        <h2 class="page-title">Riwayat Pendaftaran Saya</h2>
        
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
            <p class="no-data">Anda belum mendaftar di kegiatan apapun.</p>
        <?php endif; ?>
    </div>
</body>
</html>