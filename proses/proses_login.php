<?php
session_start();
include '../config/db_connect.php';

$email_username = $_POST['email_username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email_username) || empty($password)) {
    $_SESSION['message'] = "Email/Username dan Password wajib diisi.";
    header("Location: ../login.php");
    exit;
}

$stmt_admin = $conn->prepare("SELECT id_admin, nama_lengkap, password FROM tbl_admin WHERE username = ?");
$stmt_admin->bind_param("s", $email_username);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();

if ($result_admin->num_rows === 1) {
    $admin = $result_admin->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id_admin'];
        $_SESSION['nama'] = $admin['nama_lengkap'];
        $_SESSION['role'] = 'admin';
        header("Location: ../admin/index.php");
        $stmt_admin->close();
        $conn->close();
        exit;
    }
}
$stmt_admin->close();

$stmt_org = $conn->prepare("SELECT id_penyelenggara, nama_organisasi, password, status_verifikasi FROM tbl_penyelenggara WHERE email = ?");
$stmt_org->bind_param("s", $email_username);
$stmt_org->execute();
$result_org = $stmt_org->get_result();

if ($result_org->num_rows === 1) {
    $org = $result_org->fetch_assoc();
    if (password_verify($password, $org['password'])) {
        if ($org['status_verifikasi'] === 'Pending') {
            $_SESSION['message'] = "Akun Penyelenggara Anda masih 'Pending'. Harap tunggu verifikasi Admin.";
            header("Location: ../login.php");
            $stmt_org->close(); $conn->close(); exit;
        }
        if ($org['status_verifikasi'] === 'Rejected') {
            $_SESSION['message'] = "Akun Penyelenggara Anda ditolak. Silakan hubungi Admin.";
            header("Location: ../login.php");
            $stmt_org->close(); $conn->close(); exit;
        }
        $_SESSION['user_id'] = $org['id_penyelenggara'];
        $_SESSION['nama'] = $org['nama_organisasi'];
        $_SESSION['role'] = 'penyelenggara';
        header("Location: ../penyelenggara/index.php");
        $stmt_org->close(); $conn->close(); exit;
    }
}
$stmt_org->close();

$stmt_relawan = $conn->prepare("SELECT id_relawan, nama_lengkap, password FROM tbl_relawan WHERE email = ?");
$stmt_relawan->bind_param("s", $email_username);
$stmt_relawan->execute();
$result_relawan = $stmt_relawan->get_result();

if ($result_relawan->num_rows === 1) {
    $relawan = $result_relawan->fetch_assoc();
    if (password_verify($password, $relawan['password'])) {
        $_SESSION['user_id'] = $relawan['id_relawan'];
        $_SESSION['nama'] = $relawan['nama_lengkap'];
        $_SESSION['role'] = 'relawan';
        header("Location: ../relawan/index.php");
        $stmt_relawan->close(); $conn->close(); exit;
    }
}
$stmt_relawan->close();

$_SESSION['message'] = "Login Gagal. Email/Username atau Password salah.";
header("Location: ../login.php");
$conn->close();
exit;
?>
