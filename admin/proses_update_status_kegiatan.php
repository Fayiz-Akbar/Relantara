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
$status_baru = $_POST['status_baru'] ?? '';
$allowed_status = ['Published', 'Rejected'];

if ($id_kegiatan && in_array($status_baru, $allowed_status)) {
    
    $sql = "UPDATE tbl_kegiatan SET status_kegiatan = ? WHERE id_kegiatan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status_baru, $id_kegiatan);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Sukses: Status kegiatan berhasil diubah menjadi '$status_baru'.";
    } else {
        $_SESSION['message'] = "Error Database: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Error: Data tidak lengkap.";
}

$conn->close();
// REDIRECT KEMBALI KE MANAGE KEGIATAN
header("Location: manage_kegiatan.php");
exit;
?>