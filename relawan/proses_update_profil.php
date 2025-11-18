<?php

include '../core/auth_guard.php';
include '../config/db_connect.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

try {
    checkRole(['relawan']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode tidak diizinkan.');
    }

    $id_relawan = $_SESSION['user_id'];
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $keahlian = $_POST['keahlian'] ?? '';

    if (empty($nama_lengkap)) {
        throw new Exception('Nama Lengkap tidak boleh kosong.');
    }

    $sql = "UPDATE tbl_relawan 
            SET nama_lengkap = ?, bio = ?, keahlian = ? 
            WHERE id_relawan = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama_lengkap, $bio, $keahlian, $id_relawan);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = "Profil berhasil diperbarui.";
            $_SESSION['nama'] = $nama_lengkap;
            $response['new_session_nama'] = $nama_lengkap;
        } else {
            $response['status'] = 'info';
            $response['message'] = "Tidak ada data yang berubah.";
        }
    } else {
        throw new Exception("Eksekusi database gagal: " . $stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    http_response_code(403);
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit;
?>