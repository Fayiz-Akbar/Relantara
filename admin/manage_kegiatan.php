<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$stmt = $conn->prepare("SELECT k.id_kegiatan, k.judul, k.status_kegiatan, k.tanggal_posting, p.nama_organisasi FROM tbl_kegiatan k JOIN tbl_penyelenggara p ON k.id_penyelenggara = p.id_penyelenggara WHERE k.deleted_at IS NULL ORDER BY k.status_kegiatan = 'Pending' DESC, k.tanggal_posting DESC");
$stmt->execute();
$result = $stmt->get_result();
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
            background-color: #f8fafc;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .sidebar-header {
            padding: 1.75rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .sidebar-header h1 {
            color: #4A90E2;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .sidebar-nav {
            flex-grow: 1;
            padding: 1.25rem 0;
        }
        .sidebar-nav a {
            display: block;
            padding: 0.875rem 1.5rem;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar-nav a.active,
        .sidebar-nav a:hover {
            background-color: #f1f5f9;
            color: #4A90E2;
            border-left-color: #4A90E2;
        }
        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }
        .sidebar-footer a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #ef4444;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .sidebar-footer a:hover {
            color: #dc2626;
        }
        .main-content {
            flex: 1;
            padding: 2.5rem;
        }
        .main-header {
            margin-bottom: 2rem;
        }
        .main-header h2 {
            color: #1e293b;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        .content-table th,
        .content-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }
        .content-table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content-table td {
            color: #1e293b;
            font-size: 0.95rem;
        }
        .content-table tr:last-child td {
            border-bottom: none;
        }
        .content-table tbody tr {
            transition: background-color 0.15s;
        }
        .content-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .btn-icon {
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-icon:hover {
            transform: translateY(-1px);
        }
        .btn-icon svg {
            width: 18px;
            height: 18px;
            stroke: #ffffff;
        }
        .btn-approve {
            background-color: #16a34a;
        }
        .btn-approve:hover {
            background-color: #15803d;
        }
        .btn-reject {
            background-color: #f59e0b;
        }
        .btn-reject:hover {
            background-color: #d97706;
        }
        .btn-delete {
            background-color: #ef4444;
        }
        .btn-delete:hover {
            background-color: #dc2626;
        }
        .status {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8125rem;
            display: inline-block;
        }
        .status-Published { background-color: #dcfce7; color: #16a34a; }
        .status-Pending { background-color: #fef3c7; color: #d97706; }
        .status-Rejected { background-color: #fee2e2; color: #dc2626; }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .action-form {
            display: contents;
        }
    </style>
     
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Admin Relantara</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php">Dashboard</a>
            <a href="verifikasi.php">Verifikasi Penyelenggara</a>
            <a href="manage_kegiatan.php" class="active">Manajemen Kegiatan</a>
            <a href="manage_pengguna.php">Manajemen Pengguna</a>
            <a href="manage_kategori.php">Manajemen Kategori</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../proses/logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="main-header">
            <h2>Manajemen Kegiatan (Moderasi)</h2>
        </div>

        <table class="content-table">
            <thead>
                <tr>
                    <th>Judul Kegiatan</th>
                    <th>Penyelenggara</th>
                    <th>Tanggal Posting</th>
                    <th>Status</th>
                    <th style="text-align: center; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_organisasi']); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_posting'])); ?></td>
                    <td>
                        <span class="status status-<?php echo htmlspecialchars($row['status_kegiatan']); ?>">
                            <?php echo htmlspecialchars($row['status_kegiatan']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if ($row['status_kegiatan'] == 'Pending'): ?>
                                <form class="action-form" action="proses_update_status_kegiatan.php" method="POST">
                                    <input type="hidden" name="id_kegiatan" value="<?php echo $row['id_kegiatan']; ?>">
                                    <button type="submit" name="status_baru" value="Published" class="btn-icon btn-approve" title="Setujui Kegiatan">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                                <form class="action-form" action="proses_update_status_kegiatan.php" method="POST">
                                    <input type="hidden" name="id_kegiatan" value="<?php echo $row['id_kegiatan']; ?>">
                                    <button type="submit" name="status_baru" value="Rejected" class="btn-icon btn-reject" title="Tolak Kegiatan">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <form class="action-form" action="proses_delete_kegiatan.php" method="POST" onsubmit="return confirm('Hapus kegiatan ini?');">
                                <input type="hidden" name="id_kegiatan" value="<?php echo $row['id_kegiatan']; ?>">
                                <button type="submit" class="btn-icon btn-delete" title="Hapus Kegiatan">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>