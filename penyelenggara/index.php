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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kegiatan - Relantara</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F4F6F8;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #FFFFFF;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #4A90E2;
            margin: 0;
            font-size: 1.5rem;
        }
        .header-nav a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            margin-left: 1.5rem;
        }
        .header-nav a:hover {
            color: #F5A623;
        }
        
        /* --- PERUBAHAN LEBAR CONTAINER --- */
        .container {
            padding: 2rem;
            max-width: 1100px;
            margin: 2rem auto;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .page-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .btn-primary {
            background-color: #F5A623;
            color: white;
        }
        .btn-primary:hover {
            background-color: #DA911F;
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
            vertical-align: top;
        }
        .content-table tr:last-child td {
            border-bottom: none;
        }
        .status {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.8rem;
            display: inline-block;
        }
        .status-Published { background-color: #E8F5E9; color: #2E7D32; }
        .status-Pending { background-color: #FFF8E1; color: #F5A623; }
        .status-Rejected { background-color: #FFEBEE; color: #C62828; }
        .status-Completed { background-color: #E3F2FD; color: #4A90E2; }
        
        /* --- PERUBAHAN TOMBOL AKSI --- */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
        }
        .btn-small {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .btn-view {
            background-color: #4A90E2;
            color: white;
        }
        .btn-view:hover {
            background-color: #357ABD;
        }
        .btn-edit {
            background-color: #e6e6e6ff;
            border-color: #414040ff;
            color: #1f1e1eff;
        }
        .btn-edit:hover {
            background-color: #E0E0E0;
        }
        
        .no-data {
            padding: 2rem;
            text-align: center;
            color: #555;
            background-color: #FFFFFF;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relantara </h1>
        <nav class="header-nav">
            <a href="index.php">Kegiatan Saya</a>
            <a href="profil.php">Profil</a>
            <a href="../proses/logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <div class="page-header">
            <h2>Manajemen Kegiatan Saya</h2>
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
                        <td class="action-buttons">
                            <a href="pendaftar.php?id_kegiatan=<?php echo $row['id_kegiatan']; ?>" class="btn-small btn-view">Lihat Pendaftar</a>
                            <a href="kegiatan_form.php?edit_id=<?php echo $row['id_kegiatan']; ?>" class="btn-small btn-edit">Edit</a>
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