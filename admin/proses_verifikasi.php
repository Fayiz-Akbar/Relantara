<?php
include '../core/auth_guard.php';
checkRole(['admin']);

include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Input tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penyelenggara = $_POST['id_penyelenggara'] ?? null;
    $status_baru = $_POST['status_baru'] ?? '';

    $allowed_status = ['Verified', 'Rejected'];

    if ($id_penyelenggara && in_array($status_baru, $allowed_status)) {
        
        $sql = "UPDATE tbl_penyelenggara 
                SET status_verifikasi = ? 
                WHERE id_penyelenggara = ? AND status_verifikasi = 'Pending'";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_penyelenggara);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['message'] = "Status penyelenggara (ID: $id_penyelenggara) berhasil diubah menjadi '$status_baru'.";
            } else {
                $_SESSION['message'] = 'Tidak ada data yang diubah. ID tidak ditemukan atau status bukan Pending.';
            }
        } else {
            $_SESSION['message'] = 'Eksekusi database gagal: ' . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "Input tidak valid. Pastikan 'id_penyelenggara' dan 'status_baru' ('Verified' atau 'Rejected') dikirim.";
    }
} else {
    $_SESSION['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

mysqli_close($conn);
header("Location: verifikasi.php");
exit;
?>