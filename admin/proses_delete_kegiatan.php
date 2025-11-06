<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kegiatan = $_POST['id_kegiatan'] ?? null;

    if ($id_kegiatan) {
        
        $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() WHERE id_kegiatan = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_kegiatan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Kegiatan (ID: $id_kegiatan) berhasil di-soft-delete.";
        } else {
            $_SESSION['message'] = 'Eksekusi database gagal.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "'id_kegiatan' wajib diisi.";
    }
} else {
    $_SESSION['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

mysqli_close($conn);
header("Location: manage_kegiatan.php");
exit;
?>