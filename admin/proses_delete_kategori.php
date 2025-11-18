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
$id_kegiatan = $_POST['id_kegiatan'] ?? null;

if ($id_kegiatan) {
    // Pastikan hanya menghapus milik sendiri
    $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() 
            WHERE id_kegiatan = ? AND id_penyelenggara = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_kegiatan, $id_penyelenggara);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Kegiatan berhasil dihapus.";
        } else {
            $_SESSION['message'] = "Gagal: Kegiatan tidak ditemukan atau bukan milik Anda.";
        }
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Error: ID Kegiatan tidak valid.";
}

$conn->close();
header("Location: index.php");
exit;
?>