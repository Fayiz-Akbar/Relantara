<?php
include '../core/auth_guard.php';
include '../config/db_connect.php';

checkRole(['relawan']);

$filter_kategori_id = $_GET['kategori_id'] ?? null;
$where_kategori = "";
$params = []; 
$types = ""; 

if ($filter_kategori_id && is_numeric($filter_kategori_id)) {
    $where_kategori = " AND kk.id_kategori = ? ";
    $params[] = $filter_kategori_id; 
    $types .= "i"; 
}

$sql_kegiatan = "
    SELECT 
        k.id_kegiatan, 
        k.judul, 
        k.lokasi, 
        k.tanggal_mulai, 
        k.gambar_poster,
        p.nama_organisasi,
        GROUP_CONCAT(DISTINCT kt.nama_kategori SEPARATOR ', ') as daftar_kategori
    FROM 
        tbl_kegiatan k
    JOIN 
        tbl_penyelenggara p ON k.id_penyelenggara = p.id_penyelenggara
    LEFT JOIN 
        tbl_kegiatan_kategori kk ON k.id_kegiatan = kk.id_kegiatan
    LEFT JOIN 
        tbl_kategori kt ON kk.id_kategori = kt.id_kategori
    WHERE 
        k.deleted_at IS NULL 
        AND k.status_kegiatan = 'Published'
        $where_kategori  -- Sisipkan string filter di sini
    GROUP BY 
        k.id_kegiatan
    ORDER BY 
        k.tanggal_posting DESC
";

$stmt_kegiatan = $conn->prepare($sql_kegiatan);
if (!empty($params)) {
    $stmt_kegiatan->bind_param($types, ...$params);
}
$stmt_kegiatan->execute();
$result_kegiatan = $stmt_kegiatan->get_result();

$sql_kat_list = "SELECT id_kategori, nama_kategori 
                 FROM tbl_kategori 
                 WHERE deleted_at IS NULL 
                 ORDER BY nama_kategori";
$result_kat_list = $conn->query($sql_kat_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kegiatan - Relantara</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: #F4F6F8;
            margin: 0;
        }
        .header {
            background-color: #FFFFFF;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { color: #4A90E2; margin: 0; font-size: 1.5rem; }
        .header-nav a { color: #C62828; text-decoration: none; font-weight: 500; }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .welcome { font-size: 1.2rem; }
        
        .filter-bar {
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        .filter-bar label { font-weight: 500; margin-right: 1rem; }
        .filter-bar select {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            flex-grow: 1;
        }

        .kegiatan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .kegiatan-card {
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden; /* Agar gambar tidak keluar */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kegiatan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .card-image {
            width: 100%;
            height: 180px;
            object-fit: cover; /* Gambar akan terpotong rapi */
            background-color: #eee;
        }
        .card-content {
            padding: 1rem 1.5rem;
        }
        .card-content h3 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .card-content .organisasi {
            font-size: 0.9rem;
            color: #555;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }
        .card-content .lokasi {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 1rem;
        }
        .kategori-tags {
            font-size: 0.8rem;
            color: #4A90E2;
            font-style: italic;
        }
        .btn-detail {
            display: block;
            background-color: #4A90E2;
            color: white;
            text-align: center;
            padding: 0.75rem;
            text-decoration: none;
            font-weight: 500;
            margin-top: 1rem;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            transition: background-color 0.2s ease;
        }
        .btn-detail:hover {
            background-color: #357ABD;
        }
        .no-data {
            text-align: center;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            color: #555;
            grid-column: 1 / -1; /* Lebarkan 1 halaman penuh */
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relantara</h1>
        <nav class="header-nav">
            <a href="../proses/logout.php">Logout</a>
        </nav>
    </div>

    <div class="container">
        <p class="welcome">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</p>
        
        <div class="filter-bar">
            <label for="kategori_id">Cari berdasarkan Kategori:</label>
            <form action="index.php" method="GET" style="flex-grow: 1; margin: 0;">
                <select name="kategori_id" id="kategori_id" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua Kategori --</option>
                    <?php while($kat = $result_kat_list->fetch_assoc()): ?>
                        <option 
                            value="<?php echo $kat['id_kategori']; ?>"
                            <?php echo ($filter_kategori_id == $kat['id_kategori']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <noscript><button type="submit">Filter</button></noscript>
            </form>
        </div>

        <h2>Temukan Kegiatan</h2>
        <div class="kegiatan-grid">
            <?php if ($result_kegiatan->num_rows > 0): ?>
                <?php while($keg = $result_kegiatan->fetch_assoc()): ?>
                    <div class="kegiatan-card">
                        <img src="../<?php echo htmlspecialchars($keg['gambar_poster'] ?? 'path/to/default-image.jpg'); ?>" 
                             alt="Poster <?php echo htmlspecialchars($keg['judul']); ?>" 
                             class="card-image">
                        
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($keg['judul']); ?></h3>
                            <p class="organisasi"><?php echo htmlspecialchars($keg['nama_organisasi']); ?></p>
                            <p class="lokasi">Lokasi: <?php echo htmlspecialchars($keg['lokasi']); ?></p>
                            
                            <?php if (!empty($keg['daftar_kategori'])): ?>
                                <p class="kategori-tags">
                                    Kategori: <?php echo htmlspecialchars($keg['daftar_kategori']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <a href="detail_kegiatan.php?id=<?php echo $keg['id_kegiatan']; ?>" class="btn-detail">
                            Lihat Detail
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>Tidak ada kegiatan yang ditemukan<?php echo $filter_kategori_id ? ' untuk kategori ini' : ''; ?>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php 
// Selalu tutup statement dan koneksi
$stmt_kegiatan->close();
$conn->close(); 
?>