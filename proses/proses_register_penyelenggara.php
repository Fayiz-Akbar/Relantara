<?php
session_start();
include '../config/db_connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $nama_organisasi = $_POST['nama_organisasi'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($nama_organisasi) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new Exception('Semua field wajib diisi.');
    }
    if ($password !== $confirm_password) {
        throw new Exception('Password tidak cocok.');
    }
    if (strlen($password) < 6) {
        throw new Exception('Password minimal 6 karakter.');
    }

    // Cek Email
    $stmt_check = $conn->prepare("SELECT email FROM tbl_relawan WHERE email = ? UNION SELECT email FROM tbl_penyelenggara WHERE email = ?");
    $stmt_check->bind_param("ss", $email, $email);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        throw new Exception('Email sudah terdaftar.');
    }
    $stmt_check->close();

    // Insert
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO tbl_penyelenggara (nama_organisasi, email, password) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql);
    $stmt_insert->bind_param("sss", $nama_organisasi, $email, $hashed_password);

    if ($stmt_insert->execute()) {
        // SUKSES: Redirect ke Login
        $_SESSION['message'] = "Registrasi berhasil! Akun menunggu verifikasi Admin.";
        header("Location: ../login.php");
        exit;
    } else {
        throw new Exception('Gagal database.');
    }

} catch (Exception $e) {
    // GAGAL: Kembali ke Form Register
    $_SESSION['message'] = "Gagal: " . $e->getMessage();
    header("Location: ../register_penyelenggara.php");
    exit;
}
?>