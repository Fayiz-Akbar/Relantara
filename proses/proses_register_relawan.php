<?php
session_start();
include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register_relawan.php");
    exit;
}

$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($nama_lengkap) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['message'] = "Semua field wajib diisi.";
    header("Location: ../register_relawan.php");
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['message'] = "Password dan Konfirmasi Password tidak cocok.";
    header("Location: ../register_relawan.php");
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['message'] = "Password minimal 6 karakter.";
    header("Location: ../register_relawan.php");
    exit;
}

$stmt_check = $conn->prepare("
    SELECT email FROM tbl_relawan WHERE email = ? 
    UNION 
    SELECT email FROM tbl_penyelenggara WHERE email = ?
");
$stmt_check->bind_param("ss", $email, $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $_SESSION['message'] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
    header("Location: ../register_relawan.php");
    $stmt_check->close();
    $conn->close();
    exit;
}
$stmt_check->close();

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO tbl_relawan (nama_lengkap, email, password) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql);
$stmt_insert->bind_param("sss", $nama_lengkap, $email, $hashed_password);

if ($stmt_insert->execute()) {
    $_SESSION['message'] = "Registrasi relawan berhasil! Silakan login.";
    header("Location: ../login.php");
} else {
    $_SESSION['message'] = "Registrasi gagal. Terjadi kesalahan database: " . $stmt_insert->error;
    header("Location: ../register_relawan.php");
}

$stmt_insert->close();
$conn->close();
?>