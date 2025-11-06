<?php
session_start();
include '../config/db_connect.php'; //
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Email/Username atau Password salah.'];

$email_username = $_POST['email_username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email_username) || empty($password)) {
    $response['message'] = "Email/Username dan Password wajib diisi.";
    http_response_code(400); 
    echo json_encode($response);
    exit;
}

$stmt_admin = $conn->prepare("SELECT id_admin, nama_lengkap, password FROM tbl_admin WHERE username = ?"); //
$stmt_admin->bind_param("s", $email_username);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();

if ($result_admin->num_rows === 1) {
    $admin = $result_admin->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id_admin'];
        $_SESSION['nama'] = $admin['nama_lengkap'];
        $_SESSION['role'] = 'admin';
        
        $response = [
            'status' => 'success',
            'message' => 'Login Admin berhasil.',
            'data' => ['id' => $admin['id_admin'], 'nama' => $admin['nama_lengkap'], 'role' => 'admin']
        ];
        echo json_encode($response);
        $stmt_admin->close(); $conn->close(); exit;
    }
}
$stmt_admin->close();

$stmt_org = $conn->prepare("SELECT id_penyelenggara, nama_organisasi, password, status_verifikasi FROM tbl_penyelenggara WHERE email = ?"); //
$stmt_org->bind_param("s", $email_username);
$stmt_org->execute();
$result_org = $stmt_org->get_result();

if ($result_org->num_rows === 1) {
    $org = $result_org->fetch_assoc();
    if (password_verify($password, $org['password'])) {
        if ($org['status_verifikasi'] === 'Pending') {
            $response['message'] = "Akun Penyelenggara Anda masih 'Pending'. Harap tunggu verifikasi Admin.";
            http_response_code(403);
            echo json_encode($response);
            $stmt_org->close(); $conn->close(); exit;
        }
        if ($org['status_verifikasi'] === 'Rejected') {
            $response['message'] = "Akun Penyelenggara Anda ditolak. Silakan hubungi Admin.";
            http_response_code(403);
            echo json_encode($response);
            $stmt_org->close(); $conn->close(); exit;
        }
        
        $_SESSION['user_id'] = $org['id_penyelenggara'];
        $_SESSION['nama'] = $org['nama_organisasi'];
        $_SESSION['role'] = 'penyelenggara';
        
        $response = [
            'status' => 'success',
            'message' => 'Login Penyelenggara berhasil.',
            'data' => ['id' => $org['id_penyelenggara'], 'nama' => $org['nama_organisasi'], 'role' => 'penyelenggara']
        ];
        echo json_encode($response);
        $stmt_org->close(); $conn->close(); exit;
    }
}
$stmt_org->close();

$stmt_relawan = $conn->prepare("SELECT id_relawan, nama_lengkap, password FROM tbl_relawan WHERE email = ?"); //
$stmt_relawan->bind_param("s", $email_username);
$stmt_relawan->execute();
$result_relawan = $stmt_relawan->get_result();

if ($result_relawan->num_rows === 1) {
    $relawan = $result_relawan->fetch_assoc();
    if (password_verify($password, $relawan['password'])) {
        $_SESSION['user_id'] = $relawan['id_relawan'];
        $_SESSION['nama'] = $relawan['nama_lengkap'];
        $_SESSION['role'] = 'relawan';
        
        $response = [
            'status' => 'success',
            'message' => 'Login Relawan berhasil.',
            'data' => ['id' => $relawan['id_relawan'], 'nama' => $relawan['nama_lengkap'], 'role' => 'relawan']
        ];
        echo json_encode($response);
        $stmt_relawan->close(); $conn->close(); exit;
    }
}
$stmt_relawan->close();

// Jika semua gagal
http_response_code(401);
echo json_encode($response);
$conn->close();
?>