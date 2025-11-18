<?php
session_start();
include 'config/db_connect.php';

// Logika Filter & Pencarian
$where_clause = "WHERE k.status_kegiatan = 'Published' AND k.deleted_at IS NULL";
$params = [];
$types = "";

// 1. Filter Kategori
$cat_id = $_GET['kategori'] ?? '';
if (!empty($cat_id)) {
    $where_clause .= " AND kk.id_kategori = ?";
    $params[] = $cat_id;
    $types .= "i";
}

// 2. Pencarian Keyword (Judul/Lokasi)
$keyword = $_GET['q'] ?? '';
if (!empty($keyword)) {
    $where_clause .= " AND (k.judul LIKE ? OR k.lokasi LIKE ?)";
    $search_term = "%" . $keyword . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

// Query Utama
$sql = "SELECT k.id_kegiatan, k.judul, k.lokasi, k.tanggal_mulai, k.gambar_poster, p.nama_organisasi 
        FROM tbl_kegiatan k
        JOIN tbl_penyelenggara p ON k.id_penyelenggara = p.id_penyelenggara
        LEFT JOIN tbl_kegiatan_kategori kk ON k.id_kegiatan = kk.id_kegiatan
        $where_clause
        GROUP BY k.id_kegiatan
        ORDER BY k.tanggal_posting DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Ambil Daftar Kategori untuk Dropdown
$sql_kat = "SELECT * FROM tbl_kategori WHERE deleted_at IS NULL ORDER BY nama_kategori ASC";
$res_kat = $conn->query($sql_kat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kegiatan - Relantara</title>
    <style>
        /* --- GUNAKAN STYLE YANG SAMA DENGAN LANDING PAGE --- */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #F4F6F8; margin: 0; color: #333; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 2rem; }
        
        /* Header & Nav (Sama seperti Index) */
        .header { background-color: #FFFFFF; padding: 1rem 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .header .container { display: flex; justify-content: space-between; align-items: center; }
        .header .logo { color: #4A90E2; margin: 0; font-size: 1.8rem; font-weight: bold; text-decoration: none; }
        .header-nav a { color: #555; text-decoration: none; font-weight: 500; margin-left: 1.5rem; transition: color 0.2s ease; }
        .header-nav a:hover { color: #4A90E2; }
        .header-nav a.btn-login { padding: 0.5rem 1rem; border: 1px solid #4A90E2; color: #4A90E2; border-radius: 4px; }
        .header-nav a.btn-login:hover { background-color: #4A90E2; color: white; }
        
        /* Search Bar Section */
        .search-section { background-color: #4A90E2; padding: 3rem 0; margin-bottom: 2rem; color: white; text-align: center; }
        .search-section h2 { margin: 0 0 1.5rem 0; font-size: 2rem; }
        .search-form { display: flex; justify-content: center; gap: 0.5rem; max-width: 700px; margin: 0 auto; flex-wrap: wrap; }
        .search-form input, .search-form select { padding: 0.8rem; border: none; border-radius: 4px; font-size: 1rem; outline: none; }
        .search-form input { flex: 2; min-width: 200px; }
        .search-form select { flex: 1; min-width: 150px; }
        .search-form button { padding: 0.8rem 1.5rem; background-color: #F5A623; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.2s; }
        .search-form button:hover { background-color: #DA911F; }

        /* Grid Kegiatan */
        .kegiatan-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; padding-bottom: 3rem; }
        .card { background-color: #FFFFFF; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.07); overflow: hidden; transition: transform 0.2s ease; }
        .card:hover { transform: translateY(-5px); }
        .card-image { width: 100%; height: 200px; object-fit: cover; background-color: #E0E0E0; }
        .card-content { padding: 1.5rem; }
        .card-content h3 { margin: 0 0 0.5rem 0; color: #333; font-size: 1.2rem; }
        .card-content .penyelenggara { color: #4A90E2; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .card-content .lokasi { color: #777; font-size: 0.9rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 5px; }
        .card-content .btn-detail { display: block; text-align: center; padding: 0.7rem; background-color: #F4F6F8; color: #4A90E2; text-decoration: none; font-weight: bold; border-radius: 4px; transition: background 0.2s; }
        .card-content .btn-detail:hover { background-color: #e1e8ed; }

        .no-data { text-align: center; padding: 3rem; color: #777; font-size: 1.1rem; grid-column: 1 / -1; }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
            <a href="index.php" class="logo">Relantara</a>
            <nav class="header-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="proses/logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login / Daftar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <section class="search-section">
        <div class="container">
            <h2>Temukan Kegiatan Kebaikanmu</h2>
            <form class="search-form" action="kegiatan.php" method="GET">
                <input type="text" name="q" placeholder="Cari judul kegiatan atau lokasi..." value="<?php echo htmlspecialchars($keyword); ?>">
                <select name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php while($kat = $res_kat->fetch_assoc()): ?>
                        <option value="<?php echo $kat['id_kategori']; ?>" <?php echo ($cat_id == $kat['id_kategori']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Cari</button>
            </form>
        </div>
    </section>

    <div class="container">
        <div class="kegiatan-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img class="card-image" src="uploads/poster_kegiatan/<?php echo htmlspecialchars($row['gambar_poster'] ?? 'default_poster.jpg'); ?>" alt="Poster">
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                            <div class="penyelenggara"><?php echo htmlspecialchars($row['nama_organisasi']); ?></div>
                            <div class="lokasi">
                                <span>📍</span> <?php echo htmlspecialchars($row['lokasi']); ?>
                            </div>
                            <a href="detail_kegiatan.php?id=<?php echo $row['id_kegiatan']; ?>" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>Tidak ada kegiatan yang ditemukan sesuai pencarian Anda.</p>
                    <a href="kegiatan.php" style="color: #4A90E2;">Lihat semua kegiatan</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php $stmt->close(); $conn->close(); ?>