<?php

include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

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
        throw new Exception('Password dan Konfirmasi Password tidak cocok.');
    }
    if (strlen($password) < 6) {
        throw new Exception('Password minimal 6 karakter.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid.');
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
        throw new Exception('Email sudah terdaftar. Silakan gunakan email lain atau login.');
    }
    $stmt_check->close();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO tbl_penyelenggara (nama_organisasi, email, password) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql);
    $stmt_insert->bind_param("sss", $nama_organisasi, $email, $hashed_password);

    if ($stmt_insert->execute()) {
        $response['status'] = 'success';
        $response['message'] = "Registrasi penyelenggara berhasil! Akun Anda akan diverifikasi oleh Admin.";
        $response['new_user_id'] = $stmt_insert->insert_id;
    } else {
        throw new Exception('Registrasi gagal. Terjadi kesalahan database: ' . $stmt_insert->error);
    }
    $stmt_insert->close();

} catch (Exception $e) {
    http_response_code(400); // 400 Bad Request untuk error validasi
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>