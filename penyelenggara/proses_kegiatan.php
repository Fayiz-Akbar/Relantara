<?php
include '../core/auth_guard.php';
checkRole(['penyelenggara']);
include '../config/db_connect.php';

$id_penyelenggara_login = $_SESSION['user_id'];
$action = $_POST['action'] ?? 'save'; 
$id_kegiatan = $_POST['id_kegiatan'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

if ($action === 'delete' && $id_kegiatan) {
    $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() WHERE id_kegiatan = ? AND id_penyelenggara = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_kegiatan, $id_penyelenggara_login);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Kegiatan berhasil dihapus.";
    } else {
        $_SESSION['message'] = "Gagal menghapus kegiatan: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}

if ($action === 'save') {
    $judul = $_POST['judul'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $benefit = $_POST['benefit'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $kuota = $_POST['kuota'] ?? 0;
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
    
    $nama_file_gambar = null;
    
    if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] == 0) {
        $target_dir = "../uploads/poster_kegiatan/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $ekstensi = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
        $nama_file_gambar = "kegiatan_" . $id_penyelenggara_login . "_" . uniqid() . "." . $ekstensi;
        $target_file = $target_dir . $nama_file_gambar;

        if (!move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
            $_SESSION['message'] = "Gagal mengupload gambar poster.";
            $nama_file_gambar = null;
        }
    }
    
    if ($id_kegiatan) {
        if ($nama_file_gambar) {
            $sql = "UPDATE tbl_kegiatan SET 
                        judul = ?, deskripsi = ?, lokasi = ?, tanggal_mulai = ?, 
                        tanggal_selesai = ?, kuota = ?, benefit = ?, gambar_poster = ?, 
                        status_kegiatan = 'Pending'
                    WHERE id_kegiatan = ? AND id_penyelenggara = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssissii", $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $kuota, $benefit, $nama_file_gambar, $id_kegiatan, $id_penyelenggara_login);
        } else {
            $sql = "UPDATE tbl_kegiatan SET 
                        judul = ?, deskripsi = ?, lokasi = ?, tanggal_mulai = ?, 
                        tanggal_selesai = ?, kuota = ?, benefit = ?, 
                        status_kegiatan = 'Pending'
                    WHERE id_kegiatan = ? AND id_penyelenggara = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssissi", $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $kuota, $benefit, $id_kegiatan, $id_penyelenggara_login);
        }
        $pesan_sukses = "Kegiatan berhasil diperbarui. Menunggu persetujuan admin.";

    } else {
        $sql = "INSERT INTO tbl_kegiatan 
                    (id_penyelenggara, judul, deskripsi, lokasi, tanggal_mulai, 
                     tanggal_selesai, kuota, benefit, gambar_poster)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssiss", $id_penyelenggara_login, $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $kuota, $benefit, $nama_file_gambar);
        $pesan_sukses = "Kegiatan baru berhasil dibuat. Menunggu persetujuan admin.";
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = $pesan_sukses;
    } else {
        $_SESSION['message'] = "Error: Gagal menyimpan data. " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
header("Location: index.php");
exit;
?>