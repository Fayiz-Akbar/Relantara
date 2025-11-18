<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_pengguna.php");
    exit;
}

$user_id = $_POST['user_id'] ?? null;
$tipe_user = $_POST['tipe_user'] ?? ''; 
$allowed_types = ['relawan', 'penyelenggara'];

if ($user_id && in_array($tipe_user, $allowed_types)) {
    
    $tabel = ($tipe_user === 'relawan') ? 'tbl_relawan' : 'tbl_penyelenggara';
    $kolom_id = ($tipe_user === 'relawan') ? 'id_relawan' : 'id_penyelenggara';

    $sql = "UPDATE $tabel SET deleted_at = NOW() WHERE $kolom_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Sukses: User berhasil di-suspend.";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Error: Data user tidak valid.";
}

$conn->close();
header("Location: manage_pengguna.php");
exit;
?>