<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$stmt = $conn->prepare("SELECT id_penyelenggara, nama_organisasi, email, tanggal_daftar FROM tbl_penyelenggara WHERE status_verifikasi = 'Pending' AND deleted_at IS NULL ORDER BY tanggal_daftar ASC");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Penyelenggara - Relantara</title>
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
        .action-form {
            display: flex;
            gap: 0.5rem;
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
            background-color: #ef4444;
        }
        .btn-reject:hover {
            background-color: #dc2626;
        }
        .no-data {
            padding: 3rem 2rem;
            text-align: center;
            color: #64748b;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { padding: 1.5rem; }
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
            <a href="verifikasi.php" class="active">Verifikasi Penyelenggara</a>
            <a href="manage_kegiatan.php">Manajemen Kegiatan</a>
            <a href="manage_pengguna.php">Manajemen Pengguna</a>
            <a href="manage_kategori.php">Manajemen Kategori</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../proses/logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="main-header">
            <h2>Verifikasi Penyelenggara</h2>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Nama Penyelenggara</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_organisasi']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_daftar'])); ?></td>
                        <td>
                            <form class="action-form" action="proses_verifikasi.php" method="POST">
                                <input type="hidden" name="id_penyelenggara" value="<?php echo $row['id_penyelenggara']; ?>">
                                <button type="submit" name="status_baru" value="Verified" class="btn-icon btn-approve" title="Setujui Penyelenggara">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                <button type="submit" name="status_baru" value="Rejected" class="btn-icon btn-reject" title="Tolak Penyelenggara">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>Tidak ada penyelenggara yang menunggu verifikasi saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>