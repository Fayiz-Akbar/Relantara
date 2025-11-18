<?php
session_start();
include '../core/auth_guard.php';
include '../config/db_connect.php';
checkRole(['relawan']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profil.php");
    exit;
}

$id_relawan = $_SESSION['user_id'];
$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$bio = $_POST['bio'] ?? '';
$keahlian = $_POST['keahlian'] ?? '';

if (empty($nama_lengkap)) {
    $_SESSION['message'] = "Nama Lengkap tidak boleh kosong.";
    header("Location: profil.php");
    exit;
}

// Handle Upload Foto (Opsional)
$foto_query = "";
$params = [$nama_lengkap, $bio, $keahlian, $id_relawan];
$types = "sssi";

if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
    $target_dir = "../uploads/foto_profil/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    $ext = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
    $nama_file = "relawan_" . $id_relawan . "_" . time() . "." . $ext;
    
    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_dir . $nama_file)) {
        $foto_query = ", foto_profil = ?";
        // Sisipkan parameter foto sebelum ID
        array_splice($params, 3, 0, $nama_file); 
        $types = "ssssi";
    }
}

$sql = "UPDATE tbl_relawan SET nama_lengkap = ?, bio = ?, keahlian = ? $foto_query WHERE id_relawan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $_SESSION['message'] = "Profil berhasil diperbarui.";
    $_SESSION['nama'] = $nama_lengkap; // Update session nama
} else {
    $_SESSION['message'] = "Gagal update profil: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: profil.php");
exit;
?>