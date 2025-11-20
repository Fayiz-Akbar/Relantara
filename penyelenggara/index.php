<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);

include '../config/db_connect.php';

$id_penyelenggara_login = $_SESSION['user_id'];

$sql = "SELECT id_kegiatan, judul, lokasi, tanggal_mulai, status_kegiatan 
        FROM tbl_kegiatan 
        WHERE id_penyelenggara = ? AND deleted_at IS NULL 
        ORDER BY tanggal_posting DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_penyelenggara_login);
$stmt->execute();
$result = $stmt->get_result();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kegiatan - Relantara</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .page-header h2 {
            margin: 0;
            color: #1e293b;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9375rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-primary {
            background-color: #F5A623;
            color: white;
        }

        .btn-primary:hover {
            background-color: #DA911F;
            transform: translateY(-1px);
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .content-table th,
        .content-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }

        .content-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-table td {
            color: #1e293b;
            vertical-align: middle;
        }

        .content-table tr:last-child td {
            border-bottom: none;
        }

        .content-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .content-table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .status {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8125rem;
            display: inline-block;
        }

        .status-Published {
            background-color: #E8F5E9;
            color: #2E7D32;
        }

        .status-Pending {
            background-color: #FFF8E1;
            color: #F57C00;
        }

        .status-Rejected {
            background-color: #FFEBEE;
            color: #C62828;
        }

        .status-Completed {
            background-color: #E3F2FD;
            color: #1565C0;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            justify-content: center;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-view {
            background-color: #4A90E2;
            color: white;
        }

        .btn-view:hover {
            background-color: #357ABD;
        }

        .btn-edit {
            background-color: #F5A623;
            color: white;
        }

        .btn-edit:hover {
            background-color: #DA911F;
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background-color: #dc2626;
        }
        
        .no-data {
            padding: 3rem 2rem;
            text-align: center;
            color: #64748b;
            background-color: #FFFFFF;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
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

            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .action-buttons {
                flex-wrap: wrap;
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
            <h2>Manajemen Kegiatan</h2>
            <a href="kegiatan_form.php" class="btn btn-primary">+ Buat Kegiatan Baru</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Judul Kegiatan</th>
                        <th>Lokasi</th>
                        <th>Tanggal Mulai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                        <td><?php echo htmlspecialchars($row['lokasi']); ?></td>
                        <td>
                            <?php 
                            if (!empty($row['tanggal_mulai'])) {
                                echo date('d M Y', strtotime($row['tanggal_mulai']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="status status-<?php echo htmlspecialchars($row['status_kegiatan']); ?>">
                                <?php echo htmlspecialchars($row['status_kegiatan']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="pendaftar.php?id_kegiatan=<?php echo $row['id_kegiatan']; ?>" class="btn-icon btn-view" title="Lihat Pendaftar">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </a>
                                <a href="kegiatan_form.php?edit_id=<?php echo $row['id_kegiatan']; ?>" class="btn-icon btn-edit" title="Edit Kegiatan">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>Anda belum membuat kegiatan apapun.</p>
                <p>Silakan klik tombol "Buat Kegiatan Baru" untuk memulai.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>