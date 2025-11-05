<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['message'] = "Anda harus login untuk mengakses halaman ini.";
    header("Location: ../login.php");
    exit;
}

function checkRole($allowed_roles = []) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        http_response_code(403);
        echo "<h1>Akses Ditolak (403 Forbidden)</h1>";
        echo "<p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>";
        echo "<a href='../index.php'>Kembali ke Beranda</a>";
        exit;
    }
}
?>
