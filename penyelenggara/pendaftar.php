<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);
include '../config/db_connect.php';

$id_penyelenggara = $_SESSION['user_id'];
$id_kegiatan = $_GET['id_kegiatan'] ?? 0;
$current_page = basename($_SERVER['PHP_SELF']);

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
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 0.75rem;
        }

        .back-link {
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9375rem;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #4A90E2;
        }

        .card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            display: flex;
            gap: 1.5rem;
            align-items: start;
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
        }

        .info {
            flex: 1;
        }

        .info h3 {
            margin: 0 0 0.5rem 0;
            color: #1e293b;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .meta {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .reason {
            background: #f8fafc;
            padding: 0.875rem;
            border-radius: 8px;
            font-size: 0.9375rem;
            color: #475569;
            margin-top: 0.75rem;
            border-left: 3px solid #cbd5e1;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            flex-direction: column;
            min-width: 130px;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: white;
            text-align: center;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-accept {
            background-color: #16a34a;
        }

        .btn-accept:hover {
            background-color: #15803d;
        }

        .btn-reject {
            background-color: #ef4444;
        }

        .btn-reject:hover {
            background-color: #dc2626;
        }

        .status-badge {
            padding: 0.5rem 0.75rem;
            text-align: center;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .status-Diterima {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .status-Ditolak {
            background: #FFEBEE;
            color: #C62828;
        }
        
        .message-box {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            background-color: #E3F2FD;
            color: #0D47A1;
            border: 1px solid #BBDEFB;
            font-weight: 500;
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
            }

            .actions {
                flex-direction: row;
                width: 100%;
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
        <div class="page-header">
            <a href="index.php" class="back-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke Kegiatan
            </a>
            <h2 class="page-title">Pendaftar: <?php echo htmlspecialchars($data_kegiatan['judul']); ?></h2>
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
            <div class="no-data">
                Belum ada relawan yang mendaftar untuk kegiatan ini.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>