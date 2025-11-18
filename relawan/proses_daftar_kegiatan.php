<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['relawan']);

// HAPUS HEADER JSON

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_relawan = $_SESSION['user_id'];
$id_kegiatan = $_POST['id_kegiatan'] ?? null;
$alasan_bergabung = $_POST['alasan_bergabung'] ?? '';

if ($id_kegiatan) {
    // Cek apakah sudah daftar
    $check_sql = "SELECT id_pendaftaran FROM tbl_pendaftaran WHERE id_relawan = ? AND id_kegiatan = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_relawan, $id_kegiatan);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        $_SESSION['message'] = "Anda sudah terdaftar di kegiatan ini.";
    } else {
        // Insert
        $sql = "INSERT INTO tbl_pendaftaran (id_relawan, id_kegiatan, alasan_bergabung, status_pendaftaran) 
                VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $id_relawan, $id_kegiatan, $alasan_bergabung);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Berhasil mendaftar! Silakan tunggu konfirmasi penyelenggara.";
        } else {
            $_SESSION['message'] = "Gagal mendaftar: " . $stmt->error;
        }
        $stmt->close();
    }
    $check_stmt->close();
} else {
    $_SESSION['message'] = "ID Kegiatan tidak valid.";
}

$conn->close();
// Redirect kembali ke halaman detail kegiatan
header("Location: ../detail_kegiatan.php?id=" . $id_kegiatan);
exit;
?>