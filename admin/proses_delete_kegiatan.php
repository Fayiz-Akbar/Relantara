<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_kegiatan.php");
    exit;
}

$id_kegiatan = $_POST['id_kegiatan'] ?? null;

if ($id_kegiatan) {
    // Soft Delete (Update deleted_at)
    $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() WHERE id_kegiatan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_kegiatan);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Sukses: Kegiatan berhasil dihapus (soft delete).";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Error: ID Kegiatan tidak ditemukan.";
}

$conn->close();
header("Location: manage_kegiatan.php");
exit;
?>