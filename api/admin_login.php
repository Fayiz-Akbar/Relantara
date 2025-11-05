<?php
session_start();
header('Content-Type: application/json');
include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Login Gagal.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password_input = $_POST['password'] ?? '';

    if (empty($username) || empty($password_input)) {
        $response['message'] = 'Username dan password tidak boleh kosong.';
    } else {
        $sql = "SELECT id_admin, username, password FROM tbl_admin 
                WHERE username = ? AND deleted_at IS NULL";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && $admin = mysqli_fetch_assoc($result)) {
            $password_hash_db = $admin['password'];

            if (password_verify($password_input, $password_hash_db)) {
                
                $_SESSION['id_admin'] = $admin['id_admin'];
                $_SESSION['username'] = $admin['username'];

                $response['status'] = 'success';
                $response['message'] = 'Login berhasil.';
                $response['data'] = [
                    'id_admin' => $admin['id_admin'],
                    'username' => $admin['username']
                ];
            } else {
                $response['message'] = 'Password salah.';
            }
        } else {
            $response['message'] = 'Username tidak ditemukan.';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

mysqli_close($conn);
echo json_encode($response);
?>