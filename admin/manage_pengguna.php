<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$sql_relawan = "SELECT id_relawan, nama_lengkap, email, tanggal_daftar FROM tbl_relawan WHERE deleted_at IS NULL ORDER BY tanggal_daftar DESC";
$res_relawan = $conn->query($sql_relawan);

$sql_penyelenggara = "SELECT id_penyelenggara, nama_organisasi, email, tanggal_daftar, status_verifikasi FROM tbl_penyelenggara WHERE deleted_at IS NULL ORDER BY tanggal_daftar DESC";
$res_penyelenggara = $conn->query($sql_penyelenggara);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Relantara</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F8;
            margin: 0;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #FFFFFF;
            border-right: 1px solid #E0E0E0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid #E0E0E0;
        }
        .sidebar-header h1 {
            color: #4A90E2;
            margin: 0;
            font-size: 1.5rem;
        }
        .sidebar-nav {
            flex-grow: 1;
            padding: 1rem 0;
        }
        .sidebar-nav a {
            display: block;
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: #555;
            font-weight: 500;
            border-left: 4px solid transparent;
        }
        .sidebar-nav a.active,
        .sidebar-nav a:hover {
            background-color: #F4F6F8;
            color: #4A90E2;
            border-left-color: #4A90E2;
        }
        .sidebar-footer {
            padding: 1.5rem;
            border-top: 1px solid #E0E0E0;
        }
        .sidebar-footer a {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #C62828;
            font-weight: 500;
        }
        .main-content {
            flex-grow: 1;
            padding: 2rem;
            background-color: #F4F6F8;
        }
        .main-header {
            margin-bottom: 2rem;
        }
        .main-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8rem;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .content-table th,
        .content-table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #E0E0E0;
        }
        .content-table th {
            background-color: #F4F6F8;
            color: #555;
            font-weight: 600;
        }
        .content-table td {
            color: #333;
        }
        .content-table tr:last-child td {
            border-bottom: none;
        }
        .btn-suspend {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: opacity 0.2s ease;
            background-color: #F5A623;
            color: white;
        }
        .btn-suspend:hover {
            opacity: 0.8;
        }
        .status {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        .status-Verified { background-color: #E8F5E9; color: #2E7D32; }
        .status-Pending { background-color: #FFF8E1; color: #F5A623; }
        .status-Rejected { background-color: #FFEBEE; color: #C62828; }
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
            <a href="manage_kegiatan.php">Manajemen Kegiatan</a>
            <a href="manage_pengguna.php" class="active">Manajemen Pengguna</a>
            <a href="manage_kategori.php">Manajemen Kategori</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../proses/logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="main-header">
            <h2>Manajemen Pengguna</h2>
        </div>
        
        <h3>Daftar Penyelenggara</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Nama Penyelenggara</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $res_penyelenggara->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_organisasi']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <span class="status status-<?php echo htmlspecialchars($row['status_verifikasi']); ?>">
                            <?php echo htmlspecialchars($row['status_verifikasi']); ?>
                        </span>
                    </td>
                    <td>
                        <form action="proses_suspend_user.php" method="POST" onsubmit="return confirm('Anda yakin ingin men-suspend pengguna ini?');">
                            <input type="hidden" name="user_id" value="<?php echo $row['id_penyelenggara']; ?>">
                            <input type="hidden" name="tipe_user" value="penyelenggara">
                            <button type="submit" class="btn-suspend">Suspend</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Daftar Relawan</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $res_relawan->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_daftar'])); ?></td>
                    <td>
                        <form action="proses_suspend_user.php" method="POST" onsubmit="return confirm('Anda yakin ingin men-suspend pengguna ini?');">
                            <input type="hidden" name="user_id" value="<?php echo $row['id_relawan']; ?>">
                            <input type="hidden" name="tipe_user" value="relawan">
                            <button type="submit" class="btn-suspend">Suspend</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>