<?php
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['penyelenggara']);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $id_penyelenggara = $_SESSION['user_id'];
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $kuota = (int)($_POST['kuota'] ?? 0);
    $benefit = $_POST['benefit'] ?? '';
    $status_kegiatan = $_POST['status_kegiatan'] ?? 'Draft';
    $kategori_ids = $_POST['kategori_ids'] ?? [];

    if (empty($judul) || empty($deskripsi) || empty($lokasi) || empty($tanggal_mulai)) {
        throw new Exception('Data wajib tidak boleh kosong.');
    }

    // Upload Gambar
    $gambar_poster_path = null;
    if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] === 0) {
        $upload_dir = '../uploads/poster_kegiatan/'; 
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $ext = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
        $nama_file = 'poster_' . $id_penyelenggara . '_' . time() . '.' . $ext;
        $target = $upload_dir . $nama_file;
        
        if (move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target)) {
            $gambar_poster_path = $nama_file; // Simpan nama filenya saja
        }
    }

    $conn->begin_transaction();

    $sql = "INSERT INTO tbl_kegiatan (id_penyelenggara, judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, gambar_poster, kuota, benefit, status_kegiatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssiss", $id_penyelenggara, $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $gambar_poster_path, $kuota, $benefit, $status_kegiatan);
    $stmt->execute();
    $id_kegiatan = $conn->insert_id;

    // Simpan Kategori
    if (!empty($kategori_ids)) {
        $sql_pivot = "INSERT INTO tbl_kegiatan_kategori (id_kegiatan, id_kategori) VALUES (?, ?)";
        $stmt_pivot = $conn->prepare($sql_pivot);
        foreach ($kategori_ids as $id_kategori) {
            $stmt_pivot->bind_param("ii", $id_kegiatan, $id_kategori);
            $stmt_pivot->execute();
        }
    }

    $conn->commit();
    
    // SUKSES: Redirect ke Dashboard
    $_SESSION['message'] = "Kegiatan berhasil disimpan!";
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "Gagal: " . $e->getMessage();
    header("Location: kegiatan_form.php");
    exit;
}
?>