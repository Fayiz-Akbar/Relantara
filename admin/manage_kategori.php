<?php
session_start();
include '../core/auth_guard.php';
checkRole(['admin']);
include '../config/db_connect.php';

$notification = null;
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    unset($_SESSION['notification']);
}

$edit_id = null;
$edit_nama = '';
$edit_deskripsi = '';

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql_edit = "SELECT * FROM tbl_kategori WHERE id_kategori = ? AND deleted_at IS NULL";
    $stmt_edit = $conn->prepare($sql_edit);
    $stmt_edit->bind_param("i", $edit_id);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    if ($result_edit->num_rows > 0) {
        $data_edit = $result_edit->fetch_assoc();
        $edit_nama = $data_edit['nama_kategori'];
        $edit_deskripsi = $data_edit['deskripsi'];
    }
    $stmt_edit->close();
}

$sql_list = "SELECT id_kategori, nama_kategori, deskripsi FROM tbl_kategori WHERE deleted_at IS NULL ORDER BY nama_kategori ASC";
$result_list = $conn->query($sql_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Relantara</title>
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
        .form-card {
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .form-card h3 {
            margin: 0 0 1.5rem 0;
            color: #333;
            font-size: 1.2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #E0E0E0;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4A90E2;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .btn-primary { 
            background-color: #4A90E2; 
            color: white; 
        }
        .btn-primary:hover {
            background-color: #357ABD;
        }
        .btn-secondary { 
            background-color: #E0E0E0; 
            color: #333; 
            text-decoration: none;
            display: inline-block;
            margin-left: 0.5rem;
        }
        .btn-secondary:hover {
            background-color: #D0D0D0;
        }
        .content-table {
            width: 100%;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            border-collapse: collapse;
        }
        .content-table thead {
            background-color: #F4F6F8;
        }
        .content-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #E0E0E0;
        }
        .content-table td {
            padding: 1rem;
            border-bottom: 1px solid #F4F6F8;
            color: #333;
        }
        .content-table tbody tr:hover {
            background-color: #F9FAFB;
        }
        .btn-edit { 
            background-color: #34A853; 
            color: white; 
            text-decoration: none; 
            display: inline-block; 
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }
        .btn-edit:hover {
            background-color: #2E8B47;
        }
        .btn-delete { 
            background-color: #C62828; 
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .btn-delete:hover {
            background-color: #A52020;
        }
        .table-section {
            margin-top: 2rem;
        }
        .table-section h3 {
            margin: 0 0 1rem 0;
            color: #333;
            font-size: 1.2rem;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }
        .notification.success {
            background-color: #34A853;
            color: white;
        }
        .notification.error {
            background-color: #C62828;
            color: white;
        }
        .notification.info {
            background-color: #4A90E2;
            color: white;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <?php if ($notification): ?>
    <div class="notification <?php echo htmlspecialchars($notification['type']); ?>" id="notification">
        <?php echo htmlspecialchars($notification['message']); ?>
    </div>
    <script>
        setTimeout(function() {
            const notif = document.getElementById('notification');
            if (notif) {
                notif.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notif.remove(), 300);
            }
        }, 3000);
    </script>
    <?php endif; ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Admin Relantara</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php">Dashboard</a>
            <a href="verifikasi.php">Verifikasi Penyelenggara</a>
            <a href="manage_kegiatan.php">Manajemen Kegiatan</a>
            <a href="manage_pengguna.php">Manajemen Pengguna</a>
            <a href="manage_kategori.php" class="active">Manajemen Kategori</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../proses/logout.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="main-header">
            <h2>Manajemen Kategori</h2>
        </div>

        <div class="form-card">
            <h3><?php echo $edit_id ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h3>
            <form action="proses_kategori.php" method="POST">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="id_kategori" value="<?php echo $edit_id; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori</label>
                    <input type="text" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($edit_nama); ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"><?php echo htmlspecialchars($edit_deskripsi); ?></textarea>
                </div>
                
                <button type="submit" name="action" value="<?php echo $edit_id ? 'update' : 'add'; ?>" class="btn btn-primary">
                    <?php echo $edit_id ? 'Simpan Perubahan' : 'Tambah Kategori'; ?>
                </button>
                <?php if ($edit_id): ?>
                    <a href="manage_kategori.php" class="btn btn-secondary">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-section">
            <h3>Daftar Kategori</h3>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                        <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                        <td>
                            <a href="manage_kategori.php?edit_id=<?php echo $row['id_kategori']; ?>" class="btn-edit">Edit</a>
                            
                            <form action="proses_delete_kategori.php" method="POST" style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus kategori ini?');">
                                <input type="hidden" name="id_kategori" value="<?php echo $row['id_kategori']; ?>">
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($result_list->num_rows === 0): ?>
                        <tr><td colspan="3" style="text-align: center; padding: 2rem; color: #999;">Belum ada data kategori.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>