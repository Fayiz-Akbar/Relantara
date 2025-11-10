<?php
include '../core/auth_guard.php';
checkRole(['admin']);
include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kategori = $_POST['id_kategori'] ?? null;

    if ($id_kategori) {
        // Kita gunakan Soft Delete (sesuai pola tabelmu)
        $sql = "UPDATE tbl_kategori SET deleted_at = NOW() WHERE id_kategori = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_kategori);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Kategori (ID: $id_kategori) berhasil di-soft-delete.";
        } else {
            $_SESSION['message'] = 'Eksekusi database gagal.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "'id_kategori' wajib diisi.";
    }
} else {
    $_SESSION['message'] = 'Metode tidak diizinkan. Gunakan POST.';
}

mysqli_close($conn);
header("Location: manage_kategori.php");
exit;
?>