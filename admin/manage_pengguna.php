<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$user_type = $_GET['type'] ?? 'relawan';
$allowed_types = ['relawan', 'penyelenggara'];
if (!in_array($user_type, $allowed_types)) {
    $user_type = 'relawan';
}

$result = null;
if ($user_type === 'relawan') {
    $stmt = $conn->prepare("SELECT id_relawan, nama_lengkap, email, tanggal_daftar FROM tbl_relawan WHERE deleted_at IS NULL ORDER BY tanggal_daftar DESC");
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT id_penyelenggara, nama_organisasi, email, tanggal_daftar, status_verifikasi FROM tbl_penyelenggara WHERE deleted_at IS NULL ORDER BY tanggal_daftar DESC");
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Relantara</title>
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
        .filter-section {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        .filter-section label {
            display: block;
            margin-bottom: 0.5rem;
            color: #475569;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .filter-section select {
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            color: #1e293b;
            background-color: #ffffff;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            min-width: 200px;
        }
        .filter-section select:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
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
            background-color: #ef4444;
        }
        .btn-icon:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }
        .btn-icon svg {
            width: 18px;
            height: 18px;
            stroke: #ffffff;
        }
        .status {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8125rem;
            display: inline-block;
        }
        .status-Verified { background-color: #dcfce7; color: #16a34a; }
        .status-Pending { background-color: #fef3c7; color: #d97706; }
        .status-Rejected { background-color: #fee2e2; color: #dc2626; }
        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-weight: 500;
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

        <?php if (isset($_SESSION['message'])): ?>
            <?php $alert_class = (strpos($_SESSION['message'], 'Sukses') !== false) ? 'alert-success' : 'alert-error'; ?>
            <div class="alert <?php echo $alert_class; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="filter-section">
            <label for="user_type">Pilih Tipe Pengguna</label>
            <select id="user_type" name="user_type" onchange="window.location.href='?type=' + this.value">
                <option value="relawan" <?php echo ($user_type === 'relawan') ? 'selected' : ''; ?>>Relawan</option>
                <option value="penyelenggara" <?php echo ($user_type === 'penyelenggara') ? 'selected' : ''; ?>>Penyelenggara</option>
            </select>
        </div>

        <table class="content-table">
            <thead>
                <tr>
                    <?php if ($user_type === 'relawan'): ?>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    <?php else: ?>
                        <th>Nama Organisasi</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php if ($user_type === 'relawan'): ?>
                            <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['tanggal_daftar'])); ?></td>
                            <td>
                                <form action="proses_suspend_user.php" method="POST" style="display: inline;" onsubmit="return confirm('Suspend pengguna ini?');">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id_relawan']; ?>">
                                    <input type="hidden" name="tipe_user" value="relawan">
                                    <button type="submit" class="btn-icon" title="Suspend Pengguna">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        <?php else: ?>
                            <td><?php echo htmlspecialchars($row['nama_organisasi']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="status status-<?php echo htmlspecialchars($row['status_verifikasi']); ?>">
                                    <?php echo htmlspecialchars($row['status_verifikasi']); ?>
                                </span>
                            </td>
                            <td>
                                <form action="proses_suspend_user.php" method="POST" style="display: inline;" onsubmit="return confirm('Suspend pengguna ini?');">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id_penyelenggara']; ?>">
                                    <input type="hidden" name="tipe_user" value="penyelenggara">
                                    <button type="submit" class="btn-icon" title="Suspend Pengguna">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: #64748b;">Tidak ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?>