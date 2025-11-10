<?php
/*
 * FILE: admin/proses_create_admin.php (JSON-API Version)
 * FUNGSI: Membuat admin baru dan merespon dengan JSON.
 */

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

try {
    checkRole(['admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($nama_lengkap) || empty($username) || empty($password)) {
        throw new Exception('Nama Lengkap, Username, dan Password wajib diisi.');
    }
    if (strlen($password) < 6) {
        throw new Exception('Password minimal 6 karakter.');
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO tbl_admin (username, password, nama_lengkap) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $nama_lengkap);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = "Admin baru '$username' berhasil dibuat.";
        $response['new_admin_id'] = $conn->insert_id;
    } else {
        if ($conn->errno == 1062) {
            throw new Exception("Username '$username' sudah ada. Gunakan username lain.");
        } else {
            throw new Exception("Eksekusi database gagal: " . $stmt->error);
        }
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(403); // 403 untuk error otorisasi/validasi
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>