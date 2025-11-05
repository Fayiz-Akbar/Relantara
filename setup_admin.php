<?php
header('Content-Type: text/plain');
include './config/db_connect.php';

$username = 'admin';
$password_mentah = 'pass1234';
$nama_lengkap = 'Admin Utama';

$password_hash = password_hash($password_mentah, PASSWORD_BCRYPT);

if (!$password_hash) {
    die('Gagal membuat hash password.');
}

$sql = "INSERT INTO tbl_admin (username, password, nama_lengkap) 
        VALUES (?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Gagal menyiapkan statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "sss", 
    $username, 
    $password_hash, 
    $nama_lengkap
);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin baru berhasil dibuat!\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password_mentah . "\n";
    echo "Harap HAPUS file _setup_admin.php ini sekarang!";
} else {
    echo "Gagal membuat admin: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>