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
        $where_kategori
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
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kegiatan - Relantara</title>
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
        .welcome {
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }

        .welcome strong {
            color: #4A90E2;
        }

        .filter-bar {
            background-color: #FFFFFF;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .filter-bar label {
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        .filter-bar form {
            flex: 1;
        }

        .filter-bar select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            color: #1e293b;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-bar select:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .kegiatan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .kegiatan-card {
            background-color: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
        }

        .kegiatan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        
        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-content {
            padding: 1.25rem 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-content h3 {
            margin: 0 0 0.5rem 0;
            color: #1e293b;
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .card-content .organisasi {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .card-content .lokasi {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .kategori-tags {
            font-size: 0.8125rem;
            color: #4A90E2;
            font-style: italic;
            margin-top: auto;
            padding-top: 0.5rem;
        }

        .btn-detail {
            display: block;
            background-color: #4A90E2;
            color: white;
            text-align: center;
            padding: 0.75rem 1rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-detail:hover {
            background-color: #357ABD;
            transform: translateY(-1px);
        }

        .no-data {
            text-align: center;
            padding: 3rem 2rem;
            background-color: #fff;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            color: #64748b;
            grid-column: 1 / -1;
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

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .kegiatan-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header-container">
            <a href="../index.php" class="logo">Relantara</a>
            <nav class="nav-links">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Cari Kegiatan</a>
                <a href="riwayat.php" class="<?php echo ($current_page == 'riwayat.php') ? 'active' : ''; ?>">Riwayat Pendaftaran</a>
                <a href="profil.php" class="<?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>">Profil</a>
            </nav>
            <div class="user-menu">
                <a href="../proses/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <p class="welcome">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</p>
        
        <div class="filter-bar">
            <label for="kategori_id">Filter Kategori:</label>
            <form action="index.php" method="GET">
                <select name="kategori_id" id="kategori_id" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
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

        <h2 class="section-title">Kegiatan Tersedia</h2>
        <div class="kegiatan-grid">
            <?php if ($result_kegiatan->num_rows > 0): ?>
                <?php while($keg = $result_kegiatan->fetch_assoc()): ?>
                    <?php 
                        // --- LOGIKA GAMBAR YANG LEBIH PINTAR ---
                        $poster_name = $keg['gambar_poster'];
                        $poster_path = "../uploads/poster_kegiatan/" . $poster_name;
                        
                        // Cek apakah nama file ada DAN filenya benar-benar ada di folder
                        if (!empty($poster_name) && file_exists("../uploads/poster_kegiatan/" . $poster_name)) {
                            $img_src = $poster_path;
                        } else {
                            // Jika kosong/rusak, pakai gambar placeholder online yang pasti muncul
                            $img_src = "https://placehold.co/600x400?text=No+Image";
                        }
                    ?>
                    <div class="kegiatan-card">
                        <img src="<?php echo htmlspecialchars($img_src); ?>" 
                             alt="Poster <?php echo htmlspecialchars($keg['judul']); ?>" 
                             class="card-image">
                        
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($keg['judul']); ?></h3>
                            <p class="organisasi"><?php echo htmlspecialchars($keg['nama_organisasi']); ?></p>
                            <p class="lokasi">📍 <?php echo htmlspecialchars($keg['lokasi']); ?></p>
                            
                            <?php if (!empty($keg['daftar_kategori'])): ?>
                                <p class="kategori-tags">
                                    Kategori: <?php echo htmlspecialchars($keg['daftar_kategori']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <a href="../detail_kegiatan.php?id=<?php echo $keg['id_kegiatan']; ?>" class="btn-detail">
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
$stmt_kegiatan->close();
$conn->close(); 
?>