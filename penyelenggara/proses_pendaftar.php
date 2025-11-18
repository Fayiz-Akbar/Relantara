<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';

checkRole(['penyelenggara']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_penyelenggara = $_SESSION['user_id'];
$id_pendaftaran = $_POST['id_pendaftaran'] ?? null;
$id_kegiatan = $_POST['id_kegiatan'] ?? null; // Untuk redirect
$status_baru = $_POST['status'] ?? '';

// Validasi Input
if (!$id_pendaftaran || !in_array($status_baru, ['Diterima', 'Ditolak'])) {
    $_SESSION['message'] = "Data tidak valid.";
    header("Location: index.php");
    exit;
}

// Validasi Keamanan: Pastikan kegiatan ini milik penyelenggara yang login
// Kita cek apakah id_pendaftaran ini terhubung dengan kegiatan milik si penyelenggara
$sql_cek = "SELECT p.id_pendaftaran 
            FROM tbl_pendaftaran p
            JOIN tbl_kegiatan k ON p.id_kegiatan = k.id_kegiatan
            WHERE p.id_pendaftaran = ? AND k.id_penyelenggara = ?";
$stmt_cek = $conn->prepare($sql_cek);
$stmt_cek->bind_param("ii", $id_pendaftaran, $id_penyelenggara);
$stmt_cek->execute();

if ($stmt_cek->get_result()->num_rows === 0) {
    $_SESSION['message'] = "Akses ditolak: Kegiatan bukan milik Anda.";
    header("Location: index.php");
    exit;
}

// Proses Update
$sql_update = "UPDATE tbl_pendaftaran SET status_pendaftaran = ? WHERE id_pendaftaran = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("si", $status_baru, $id_pendaftaran);

if ($stmt_update->execute()) {
    $_SESSION['message'] = "Status pendaftar berhasil diubah menjadi: $status_baru";
} else {
    $_SESSION['message'] = "Gagal mengupdate status: " . $stmt_update->error;
}

$stmt_update->close();
$conn->close();

// Redirect kembali ke halaman pendaftar kegiatan tersebut
header("Location: pendaftar.php?id_kegiatan=" . $id_kegiatan);
exit;
?>