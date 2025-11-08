<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kegiatan = $_POST['id_kegiatan'] ?? null;
    $status_baru = $_POST['status_baru'] ?? '';

    $allowed_status = ['Published', 'Rejected'];

    if ($id_kegiatan && in_array($status_baru, $allowed_status)) {
        
        $sql = "UPDATE tbl_kegiatan SET status_kegiatan = ? WHERE id_kegiatan = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_kegiatan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Status kegiatan (ID: $id_kegiatan) diubah menjadi '$status_baru'.";
        } else {
            $_SESSION['message'] = 'Eksekusi database gagal.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "Input tidak valid. 'id_kegiatan' dan 'status_baru' ('Published' atau 'Rejected') wajib diisi.";
    }
} else {
    $_SESSION['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

mysqli_close($conn);
header("Location: manage_kegiatan.php");
exit;
?>