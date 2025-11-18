<?php
include '../core/auth_guard.php';
checkRole(['admin']);
include '../config/db_connect.php';

$edit_id = null;
$edit_nama = '';
$edit_deskripsi = '';

// Logika untuk mengambil data edit
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
        // Pastikan kolom deskripsi ada di database. Jika error, hapus baris ini.
        $edit_deskripsi = $data_edit['deskripsi'] ?? ''; 
    }
    $stmt_edit->close();
}

// Ambil semua kategori
$sql_list = "SELECT * FROM tbl_kategori WHERE deleted_at IS NULL ORDER BY nama_kategori ASC";
$result_list = $conn->query($sql_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Relantara</title>
    <style>
        /* --- CSS ADMIN LAYOUT (SAMA DENGAN FILE LAIN) --- */
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

        /* --- CSS KHUSUS HALAMAN INI --- */
        .form-card {
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            max-width: 1200px;
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
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        
        .content-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
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
        
        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background-color: #4A90E2; color: white; }
        .btn-primary:hover { background-color: #357ABD; }
        
        .btn-secondary { background-color: #e0e0e0; color: #333; margin-left: 10px; }
        .btn-secondary:hover { background-color: #d5d5d5; }

        .btn-edit { background-color: #FFCA28; color: #333; margin-right: 5px; padding: 0.4rem 0.8rem; font-size: 0.85rem; }
        .btn-delete { background-color: #C62828; color: white; padding: 0.4rem 0.8rem; font-size: 0.85rem; }
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
            <h3 style="margin-top: 0;"><?php echo $edit_id ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h3>
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
                    <a href="manage_kategori.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <table class="content-table">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_list->num_rows > 0): ?>
                    <?php while($row = $result_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                        <td><?php echo htmlspecialchars($row['deskripsi'] ?? '-'); ?></td>
                        <td>
                            <a href="manage_kategori.php?edit_id=<?php echo $row['id_kategori']; ?>" class="btn btn-edit">Edit</a>
                            
                            <form action="proses_delete_kategori.php" method="POST" style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus kategori ini?');">
                                <input type="hidden" name="id_kategori" value="<?php echo $row['id_kategori']; ?>">
                                <button type="submit" class="btn btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align: center; padding: 2rem;">Belum ada data kategori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>