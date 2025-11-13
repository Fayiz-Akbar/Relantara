<?php
include '../core/auth_guard.php';
checkRole(['admin']);
include '../config/db_connect.php';

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
    <link rel="stylesheet" href="path/to/your/admin-styles.css"> <style>
        .form-card {
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; 
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-primary { background-color: #4A90E2; color: white; }
        .btn-secondary { background-color: #f0f0f0; color: #333; text-decoration: none;}
        .btn-edit { background-color: #34A853; color: white; text-decoration: none; display: inline-block; padding: 0.5rem 1rem; }
        .btn-delete { background-color: #C62828; color: white; }
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
            <a href="manage_kategori.php" class="active">Manajemen Kategori</a> <a href="manage_kegiatan.php">Manajemen Kegiatan</a>
            <a href="manage_pengguna.php">Manajemen Pengguna</a>
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

        <h3>Daftar Kategori</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td>
                        <a href="manage_kategori.php?edit_id=<?php echo $row['id_kategori']; ?>" class="btn-edit" style="margin-right: 5px;">Edit</a>
                        
                        <form action="proses_delete_kategori.php" method="POST" style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus kategori ini?');">
                            <input type="hidden" name="id_kategori" value="<?php echo $row['id_kategori']; ?>">
                            <button type="submit" class="btn-delete">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($result_list->num_rows === 0): ?>
                    <tr><td colspan="3" style="text-align: center;">Belum ada data kategori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>