<?php
/*
 * FILE: penyelenggara/proses_add_kegiatan.php
 * FUNGSI: Menerima data dari kegiatan_form.php, memvalidasi,
 * mengunggah gambar, dan menyimpannya ke database.
 * PENGARUH: File ini akan membuat baris baru di `tbl_kegiatan` dan
 * `tbl_kegiatan_kategori`.
 */

// KONSEP DASAR: Selalu mulai dengan session dan guard
include '../core/auth_guard.php';
include '../config/db_connect.php';

// Proteksi: Hanya 'penyelenggara' yang boleh mengakses
checkRole(['penyelenggara']);

// Proteksi: Hanya izinkan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Error: Metode tidak diizinkan.";
    header("Location: kegiatan_form.php");
    exit;
}

// FUNGSI: Mengambil ID Penyelenggara dari Sesi
// TUJUAN: Untuk mengisi 'id_penyelenggara' di tbl_kegiatan
$id_penyelenggara = $_SESSION['user_id'];

// FUNGSI: Mengambil data dari form
// TUJUAN: Menampung semua input dari user
$judul            = $_POST['judul'] ?? '';
$deskripsi        = $_POST['deskripsi'] ?? '';
$lokasi           = $_POST['lokasi'] ?? '';
$tanggal_mulai    = $_POST['tanggal_mulai'] ?? '';
$tanggal_selesai  = $_POST['tanggal_selesai'] ?? '';
$kuota            = (int)($_POST['kuota'] ?? 0);
$benefit          = $_POST['benefit'] ?? '';
$status_kegiatan  = $_POST['status_kegiatan'] ?? 'Draft';
$kategori_ids     = $_POST['kategori_ids'] ?? []; // Ini adalah array

// FUNGSI: Validasi dasar (sisi server)
// TUJUAN: Memastikan data inti tidak kosong
if (empty($judul) || empty($deskripsi) || empty($lokasi) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
    $_SESSION['message'] = "Gagal: Judul, deskripsi, lokasi, dan tanggal wajib diisi.";
    header("Location: kegiatan_form.php");
    exit;
}

// ==========================================================
// FUNGSI: Proses Upload Gambar (jika ada)
// ==========================================================
$gambar_poster_path = null; // Default jika tidak ada gambar

if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] === 0) {
    
    // Tentukan direktori upload
    // PENTING: Pastikan folder 'uploads/posters/' ada dan bisa ditulis (writable)
    $upload_dir = '../uploads/posters/'; 
    
    // FUNGSI: Buat nama file unik untuk menghindari tumpang tindih
    // TUJUAN: Keamanan dan konsistensi file
    $file_extension = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
    $nama_file_unik = 'poster_' . $id_penyelenggara . '_' . time() . '.' . $file_extension;
    $target_file = $upload_dir . $nama_file_unik;

    // FUNGSI: Validasi tipe file (Contoh sederhana)
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        $_SESSION['message'] = "Gagal: Tipe file poster tidak valid. Hanya izinkan JPG, PNG, GIF.";
        header("Location: kegiatan_form.php");
        exit;
    }

    // FUNGSI: Memindahkan file dari temp ke direktori permanen
    if (move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
        // FUNGSI: Simpan path relatif untuk database
        // TUJUAN: Path ini yang akan dipanggil di <img> frontend
        $gambar_poster_path = 'uploads/posters/' . $nama_file_unik;
    } else {
        $_SESSION['message'] = "Gagal: Terjadi error saat mengunggah gambar poster.";
        header("Location: kegiatan_form.php");
        exit;
    }
}

// ==========================================================
// KONSEP LANJUTAN: Transaksi Database (Atomic)
// TUJUAN: Jika salah satu query (misal: insert kategori) gagal,
//         maka query utama (insert kegiatan) juga akan dibatalkan.
//         Ini menjaga data tetap konsisten.
// ==========================================================
$conn->begin_transaction();

try {
    // --- Langkah 1: Insert ke tabel utama (tbl_kegiatan) ---
    $sql_kegiatan = "INSERT INTO tbl_kegiatan 
                        (id_penyelenggara, judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, 
                         gambar_poster, kuota, benefit, status_kegiatan)
                     VALUES 
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_kegiatan = $conn->prepare($sql_kegiatan);
    $stmt_kegiatan->bind_param(
        "issssssiss", // Tipe data (i=integer, s=string)
        $id_penyelenggara,
        $judul,
        $deskripsi,
        $lokasi,
        $tanggal_mulai,
        $tanggal_selesai,
        $gambar_poster_path, // Path file atau NULL
        $kuota,
        $benefit,
        $status_kegiatan
    );

    // Eksekusi query utama
    $stmt_kegiatan->execute();

    // FUNGSI: Dapatkan ID dari kegiatan yang BARU saja dibuat
    // TUJUAN: Untuk digunakan sebagai Foreign Key di tabel pivot
    $id_kegiatan_baru = $conn->insert_id;

    $stmt_kegiatan->close();

    // --- Langkah 2: Insert ke tabel pivot (tbl_kegiatan_kategori) ---
    if (!empty($kategori_ids)) {
        
        $sql_pivot = "INSERT INTO tbl_kegiatan_kategori (id_kegiatan, id_kategori) VALUES (?, ?)";
        $stmt_pivot = $conn->prepare($sql_pivot);

        // FUNGSI: Loop sebanyak kategori yang dipilih
        // TUJUAN: Menyimpan setiap pasangan ID kegiatan dan ID kategori
        foreach ($kategori_ids as $id_kategori) {
            $stmt_pivot->bind_param("ii", $id_kegiatan_baru, $id_kategori);
            $stmt_pivot->execute(); // Eksekusi untuk setiap kategori
        }
        $stmt_pivot->close();
    }

    // KONSEP: Jika semua berhasil, "kunci" perubahan ke database
    $conn->commit();
    
    $_SESSION['message'] = "Sukses: Kegiatan '$judul' berhasil disimpan.";
    header("Location: kegiatan_form.php"); // Kembali ke form dengan pesan sukses

} catch (mysqli_sql_exception $exception) {
    
    // KONSEP: Jika ada error di mana pun, batalkan SEMUA perubahan
    $conn->rollback();
    
    $_SESSION['message'] = "Gagal Total: Terjadi kesalahan database. " . $exception->getMessage();
    header("Location: kegiatan_form.php"); // Kembali ke form dengan pesan error

} finally {
    // Selalu tutup koneksi
    $conn->close();
    exit;
}
?>