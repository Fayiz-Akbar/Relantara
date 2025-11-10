<?php
// Mengimpor auth guard untuk proteksi halaman
include '../core/auth_guard.php';

// Memeriksa apakah role adalah 'penyelenggara'
checkRole(['penyelenggara']);

// Mengimpor koneksi database
include '../config/db_connect.php';

// Mengambil daftar kategori dari database untuk ditampilkan sebagai checkbox
// Ini adalah implementasi dari langkah 3A yang kita diskusikan
$sql_kategori = "SELECT id_kategori, nama_kategori 
                 FROM tbl_kategori 
                 WHERE deleted_at IS NULL 
                 ORDER BY nama_kategori ASC";
$result_kategori = $conn->query($sql_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kegiatan Baru - Relantara</title>
    
    <style>
        /* Style dasar dari penyelenggara/index.php */
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
        .header a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
        }
        .container {
            padding: 2rem;
            max-width: 900px;
            margin: 2rem auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        .btn-green {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #34A853; 
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-green:hover {
            background-color: #2E8B45;
        }

        /* Style Form dari login.php */
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Konsistensi ukuran */
            font-family: inherit; /* Konsistensi font */
            font-size: 0.95rem;
        }
        
        /* Style untuk checkbox kategori */
        .checkbox-group {
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 0.75rem;
            max-height: 150px;
            overflow-y: auto;
            background-color: #fafafa;
        }
        .checkbox-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: normal;
            color: #333;
        }
        .checkbox-label input {
            margin-right: 0.5rem;
        }
        
        /* Style untuk notifikasi/pesan (dari login.php) */
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }
        .message-error {
            background-color: #FFEBEE;
            border: 1px solid #E57373;
            color: #C62828;
        }
        .message-success {
            background-color: #E8F5E9;
            border: 1px solid #81C784;
            color: #2E7D32;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Relantara (Penyelenggara)</h1>
        <div>
            <span style="margin-right: 1rem;">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
            <a href="../proses/logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        
        <a href="index.php" style="text-decoration: none; color: #4A90E2; font-weight: 500;">&laquo; Kembali ke Dashboard</a>
        <h2 style="margin-top: 1rem;">Buat Lowongan Kegiatan Baru</h2>
        <p>Isi formulir di bawah ini untuk mempublikasikan kegiatan Anda.</p>
        
        <?php
        // FUNGSI: Menampilkan pesan notifikasi (sukses atau error) dari proses sebelumnya
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            // Tentukan kelas CSS berdasarkan isi pesan
            $msg_class = (strpos(strtolower($message), 'gagal') !== false || strpos(strtolower($message), 'error') !== false) 
                         ? 'message-error' : 'message-success';
            
            echo "<div class='message $msg_class'>" . htmlspecialchars($message) . "</div>";
            unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
        }
        ?>

        <form action="proses_kegiatan.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="judul">Judul Kegiatan</label>
                <input type="text" id="judul" name="judul" required placeholder="e.g., Tanam 1000 Pohon untuk Masa Depan">
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi Kegiatan</label>
                <textarea id="deskripsi" name="deskripsi" rows="6" required placeholder="Jelaskan detail kegiatan, tugas relawan, dll."></textarea>
            </div>
            
            <div class="form-group">
                <label>Kategori Kegiatan</label>
                <div class="checkbox-group">
                    <?php if ($result_kategori->num_rows > 0): ?>
                        <?php while($kat = $result_kategori->fetch_assoc()): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="kategori_ids[]" value="<?php echo $kat['id_kategori']; ?>">
                                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                            </label>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #555;">Kategori belum tersedia. Hubungi Admin.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi" required placeholder="e.g., Hutan Kota Sribhawono atau Online via Zoom">
            </div>

            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
            </div>

            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
            </div>

            <div class="form-group">
                <label for="kuota">Kuota Relawan</label>
                <input type="number" id="kuota" name="kuota" value="0" min="0" required>
                <small style="color: #555;">*Isi 0 jika kuota tidak terbatas.</small>
            </div>

            <div class="form-group">
                <label for="benefit">Benefit</label>
                <input type="text" id="benefit" name="benefit" placeholder="e.g., Sertifikat, Konsumsi, Relasi (pisahkan dengan koma)">
            </div>
            
            <div class="form-group">
                <label for="gambar_poster">Gambar Poster (Opsional)</label>
                <input type="file" id="gambar_poster" name="gambar_poster" accept="image/png, image/jpeg, image/jpg">
                <small style="color: #555;">*Rekomendasi format: JPG, PNG. Maks 2MB.</small>
            </div>

            <div class="form-group">
                <label for="status_kegiatan">Status Simpan</label>
                <select name="status_kegiatan" id="status_kegiatan" required>
                    <option value="Pending">Ajukan untuk Verifikasi</option>
                    <option value="Draft">Simpan sebagai Draft</option>
                </select>
            </div>

            <hr style="margin: 2rem 0;">

            <button type="submit" class="btn btn-green">
                Simpan Kegiatan
            </button>

        </form>

    </div>

</body>
</html>
<?php
// Menutup koneksi database di akhir file
$conn->close();
?>