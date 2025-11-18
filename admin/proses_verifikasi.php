<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['admin']);

// HAPUS HEADER JSON

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: verifikasi.php");
    exit;
}

$id_penyelenggara = $_POST['id_penyelenggara'] ?? null;
$status_baru = $_POST['status_baru'] ?? '';
$allowed_status = ['Verified', 'Rejected'];

if ($id_penyelenggara && in_array($status_baru, $allowed_status)) {
    
    $sql = "UPDATE tbl_penyelenggara 
            SET status_verifikasi = ? 
            WHERE id_penyelenggara = ? AND status_verifikasi = 'Pending'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status_baru, $id_penyelenggara);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Sukses: Penyelenggara berhasil diubah menjadi '$status_baru'.";
        } else {
            $_SESSION['message'] = "Info: Tidak ada data yang berubah (Mungkin sudah diverifikasi sebelumnya).";
        }
    } else {
        $_SESSION['message'] = "Error Database: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Error: Input tidak valid.";
}

$conn->close();
header("Location: verifikasi.php");
exit;
?>