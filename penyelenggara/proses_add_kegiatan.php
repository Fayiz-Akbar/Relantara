<?php

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

try {
    checkRole(['penyelenggara']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $id_penyelenggara = $_SESSION['user_id'];
    $judul            = $_POST['judul'] ?? '';
    $deskripsi        = $_POST['deskripsi'] ?? '';
    $lokasi           = $_POST['lokasi'] ?? '';
    $tanggal_mulai    = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai  = $_POST['tanggal_selesai'] ?? '';
    $kuota            = (int)($_POST['kuota'] ?? 0);
    $benefit          = $_POST['benefit'] ?? '';
    $status_kegiatan  = $_POST['status_kegiatan'] ?? 'Draft';
    $kategori_ids     = $_POST['kategori_ids'] ?? [];

    if (empty($judul) || empty($deskripsi) || empty($lokasi) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        throw new Exception('Gagal: Judul, deskripsi, lokasi, dan tanggal wajib diisi.');
    }

    $gambar_poster_path = null;
    if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] === 0) {
        $upload_dir = '../uploads/posters/'; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_extension = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
        $nama_file_unik = 'poster_' . $id_penyelenggara . '_' . time() . '.' . $file_extension;
        $target_file = $upload_dir . $nama_file_unik;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($file_extension), $allowed_types)) {
            throw new Exception('Gagal: Tipe file poster tidak valid. Hanya izinkan JPG, PNG, GIF.');
        }

        if (move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
            $gambar_poster_path = 'uploads/posters/' . $nama_file_unik;
        } else {
            throw new Exception('Gagal: Terjadi error saat mengunggah gambar poster.');
        }
    }

    $conn->begin_transaction();

    $sql_kegiatan = "INSERT INTO tbl_kegiatan 
                        (id_penyelenggara, judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, 
                         gambar_poster, kuota, benefit, status_kegiatan)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_kegiatan = $conn->prepare($sql_kegiatan);
    $stmt_kegiatan->bind_param("issssssiss", $id_penyelenggara, $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $gambar_poster_path, $kuota, $benefit, $status_kegiatan);
    $stmt_kegiatan->execute();

    $id_kegiatan_baru = $conn->insert_id;
    $stmt_kegiatan->close();

    if (!empty($kategori_ids)) {
        $sql_pivot = "INSERT INTO tbl_kegiatan_kategori (id_kegiatan, id_kategori) VALUES (?, ?)";
        $stmt_pivot = $conn->prepare($sql_pivot);
        foreach ($kategori_ids as $id_kategori) {
            $stmt_pivot->bind_param("ii", $id_kegiatan_baru, $id_kategori);
            $stmt_pivot->execute();
        }
        $stmt_pivot->close();
    }

    $conn->commit();
    
    $response['status'] = 'success';
    $response['message'] = "Sukses: Kegiatan '$judul' berhasil disimpan.";
    $response['new_kegiatan_id'] = $id_kegiatan_baru;

} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    http_response_code(500); 
    $response['message'] = "Gagal Total (Database): " . $e->getMessage();
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(403); 
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>