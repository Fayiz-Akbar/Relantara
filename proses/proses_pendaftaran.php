<?php
session_start();
include '../config/db_connect.php';

// Pastikan hanya relawan yang bisa akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'relawan') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_relawan = $_SESSION['user_id'];
    $id_kegiatan = $_POST['id_kegiatan'] ?? null;

    if ($id_kegiatan) {
        // Cek apakah sudah daftar sebelumnya (untuk keamanan ganda)
        $check_sql = "SELECT id_pendaftaran FROM tbl_pendaftaran WHERE id_relawan = ? AND id_kegiatan = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $id_relawan, $id_kegiatan);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $_SESSION['message'] = "Anda sudah terdaftar.";
            header("Location: ../detail_kegiatan.php?id=" . $id_kegiatan);
            exit;
        }

        // Insert Pendaftaran Baru
        $sql = "INSERT INTO tbl_pendaftaran (id_relawan, id_kegiatan, status_pendaftaran) VALUES (?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_relawan, $id_kegiatan);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Pendaftaran berhasil! Tunggu konfirmasi penyelenggara.";
        } else {
            $_SESSION['message'] = "Gagal mendaftar. Silakan coba lagi.";
        }
        $stmt->close();
    }
    
    // Redirect kembali ke halaman detail
    header("Location: ../detail_kegiatan.php?id=" . $id_kegiatan);
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
?>