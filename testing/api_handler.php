<?php
header('Content-Type: application/json');
$start_time = microtime(true);

session_start();
include '../config/db_connect.php';

function sendResponse($status, $message, $data = null, $start_time = null) {
    $response = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
        'execution_time' => $start_time ? round((microtime(true) - $start_time) * 1000, 2) . 'ms' : null
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$role = $_POST['role'] ?? $_GET['role'] ?? '';

try {
    switch ($action) {
        
        case 'login':
            $email_username = $_POST['email_username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email_username) || empty($password)) {
                sendResponse('error', 'Email/Username dan Password wajib diisi.', null, $start_time);
            }
            
            $stmt_admin = $conn->prepare("SELECT id_admin, nama_lengkap, password FROM tbl_admin WHERE username = ?");
            $stmt_admin->bind_param("s", $email_username);
            $stmt_admin->execute();
            $result_admin = $stmt_admin->get_result();
            
            if ($result_admin->num_rows === 1) {
                $admin = $result_admin->fetch_assoc();
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['test_user_id'] = $admin['id_admin'];
                    $_SESSION['test_nama'] = $admin['nama_lengkap'];
                    $_SESSION['test_role'] = 'admin';
                    sendResponse('success', 'Login berhasil sebagai Admin.', [
                        'user_id' => $admin['id_admin'],
                        'nama' => $admin['nama_lengkap'],
                        'role' => 'admin'
                    ], $start_time);
                }
            }
            
            $stmt_org = $conn->prepare("SELECT id_penyelenggara, nama_organisasi, password, status_verifikasi FROM tbl_penyelenggara WHERE email = ?");
            $stmt_org->bind_param("s", $email_username);
            $stmt_org->execute();
            $result_org = $stmt_org->get_result();
            
            if ($result_org->num_rows === 1) {
                $org = $result_org->fetch_assoc();
                if (password_verify($password, $org['password'])) {
                    if ($org['status_verifikasi'] === 'Pending') {
                        sendResponse('error', 'Akun Penyelenggara masih Pending. Tunggu verifikasi Admin.', null, $start_time);
                    }
                    if ($org['status_verifikasi'] === 'Rejected') {
                        sendResponse('error', 'Akun Penyelenggara ditolak. Hubungi Admin.', null, $start_time);
                    }
                    $_SESSION['test_user_id'] = $org['id_penyelenggara'];
                    $_SESSION['test_nama'] = $org['nama_organisasi'];
                    $_SESSION['test_role'] = 'penyelenggara';
                    sendResponse('success', 'Login berhasil sebagai Penyelenggara.', [
                        'user_id' => $org['id_penyelenggara'],
                        'nama' => $org['nama_organisasi'],
                        'role' => 'penyelenggara',
                        'status_verifikasi' => $org['status_verifikasi']
                    ], $start_time);
                }
            }
            
            $stmt_relawan = $conn->prepare("SELECT id_relawan, nama_lengkap, password FROM tbl_relawan WHERE email = ?");
            $stmt_relawan->bind_param("s", $email_username);
            $stmt_relawan->execute();
            $result_relawan = $stmt_relawan->get_result();
            
            if ($result_relawan->num_rows === 1) {
                $relawan = $result_relawan->fetch_assoc();
                if (password_verify($password, $relawan['password'])) {
                    $_SESSION['test_user_id'] = $relawan['id_relawan'];
                    $_SESSION['test_nama'] = $relawan['nama_lengkap'];
                    $_SESSION['test_role'] = 'relawan';
                    sendResponse('success', 'Login berhasil sebagai Relawan.', [
                        'user_id' => $relawan['id_relawan'],
                        'nama' => $relawan['nama_lengkap'],
                        'role' => 'relawan'
                    ], $start_time);
                }
            }
            
            sendResponse('error', 'Login Gagal. Email/Username atau Password salah.', null, $start_time);
            break;
            
        case 'logout':
            unset($_SESSION['test_user_id']);
            unset($_SESSION['test_nama']);
            unset($_SESSION['test_role']);
            sendResponse('success', 'Logout berhasil.', null, $start_time);
            break;
            
        case 'register_relawan':
            $nama_lengkap = $_POST['nama_lengkap'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($nama_lengkap) || empty($email) || empty($password) || empty($confirm_password)) {
                sendResponse('error', 'Semua field wajib diisi.', null, $start_time);
            }
            if ($password !== $confirm_password) {
                sendResponse('error', 'Password dan Konfirmasi Password tidak cocok.', null, $start_time);
            }
            if (strlen($password) < 6) {
                sendResponse('error', 'Password minimal 6 karakter.', null, $start_time);
            }
            
            $stmt_check = $conn->prepare("SELECT email FROM tbl_relawan WHERE email = ? UNION SELECT email FROM tbl_penyelenggara WHERE email = ?");
            $stmt_check->bind_param("ss", $email, $email);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) {
                sendResponse('error', 'Email sudah terdaftar. Gunakan email lain.', null, $start_time);
            }
            
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO tbl_relawan (nama_lengkap, email, password) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql);
            $stmt_insert->bind_param("sss", $nama_lengkap, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                sendResponse('success', 'Registrasi relawan berhasil!', ['id_relawan' => $conn->insert_id], $start_time);
            } else {
                sendResponse('error', 'Terjadi kesalahan database.', null, $start_time);
            }
            break;
            
        case 'register_penyelenggara':
            $nama_organisasi = $_POST['nama_organisasi'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($nama_organisasi) || empty($email) || empty($password) || empty($confirm_password)) {
                sendResponse('error', 'Semua field wajib diisi.', null, $start_time);
            }
            if ($password !== $confirm_password) {
                sendResponse('error', 'Password tidak cocok.', null, $start_time);
            }
            if (strlen($password) < 6) {
                sendResponse('error', 'Password minimal 6 karakter.', null, $start_time);
            }
            
            $stmt_check = $conn->prepare("SELECT email FROM tbl_relawan WHERE email = ? UNION SELECT email FROM tbl_penyelenggara WHERE email = ?");
            $stmt_check->bind_param("ss", $email, $email);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows > 0) {
                sendResponse('error', 'Email sudah terdaftar.', null, $start_time);
            }
            
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO tbl_penyelenggara (nama_organisasi, email, password) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql);
            $stmt_insert->bind_param("sss", $nama_organisasi, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                sendResponse('success', 'Registrasi berhasil! Menunggu verifikasi Admin.', ['id_penyelenggara' => $conn->insert_id], $start_time);
            } else {
                sendResponse('error', 'Gagal database.', null, $start_time);
            }
            break;
            
        case 'update_profil_relawan':
            $id_relawan = $_POST['id_relawan'] ?? '';
            $nama_lengkap = $_POST['nama_lengkap'] ?? '';
            $bio = $_POST['bio'] ?? '';
            $keahlian = $_POST['keahlian'] ?? '';
            
            if (empty($nama_lengkap)) {
                sendResponse('error', 'Nama Lengkap tidak boleh kosong.', null, $start_time);
            }
            
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
                    array_splice($params, 3, 0, $nama_file);
                    $types = "ssssi";
                }
            }
            
            $sql = "UPDATE tbl_relawan SET nama_lengkap = ?, bio = ?, keahlian = ? $foto_query WHERE id_relawan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                sendResponse('success', 'Profil berhasil diperbarui.', isset($nama_file) ? ['foto_profil' => $nama_file] : null, $start_time);
            } else {
                sendResponse('error', 'Gagal update profil: ' . $stmt->error, null, $start_time);
            }
            break;
            
        case 'apply_kegiatan':
            $id_relawan = $_POST['id_relawan'] ?? '';
            $id_kegiatan = $_POST['id_kegiatan'] ?? '';
            
            if (empty($id_relawan) || empty($id_kegiatan)) {
                sendResponse('error', 'Data tidak lengkap.', null, $start_time);
            }
            
            $check_sql = "SELECT id_pendaftaran FROM tbl_pendaftaran WHERE id_relawan = ? AND id_kegiatan = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $id_relawan, $id_kegiatan);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows > 0) {
                sendResponse('error', 'Anda sudah terdaftar.', null, $start_time);
            }
            
            $sql = "INSERT INTO tbl_pendaftaran (id_relawan, id_kegiatan, status_pendaftaran) VALUES (?, ?, 'Pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_relawan, $id_kegiatan);
            
            if ($stmt->execute()) {
                sendResponse('success', 'Pendaftaran berhasil! Tunggu konfirmasi penyelenggara.', ['id_pendaftaran' => $conn->insert_id], $start_time);
            } else {
                sendResponse('error', 'Gagal mendaftar.', null, $start_time);
            }
            break;
            
        case 'create_kegiatan':
            $id_penyelenggara = $_POST['id_penyelenggara'] ?? '';
            $judul = $_POST['judul'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $benefit = $_POST['benefit'] ?? '';
            $lokasi = $_POST['lokasi'] ?? '';
            $kuota = $_POST['kuota'] ?? 0;
            $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
            $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
            
            $nama_file_gambar = null;
            
            if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] == 0) {
                $target_dir = "../uploads/poster_kegiatan/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                
                $ekstensi = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
                $nama_file_gambar = "kegiatan_" . $id_penyelenggara . "_" . uniqid() . "." . $ekstensi;
                $target_file = $target_dir . $nama_file_gambar;
                
                if (!move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
                    $nama_file_gambar = null;
                }
            }
            
            $sql = "INSERT INTO tbl_kegiatan (id_penyelenggara, judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai, kuota, benefit, gambar_poster) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssiss", $id_penyelenggara, $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $kuota, $benefit, $nama_file_gambar);
            
            if ($stmt->execute()) {
                sendResponse('success', 'Kegiatan baru berhasil dibuat. Menunggu persetujuan admin.', ['id_kegiatan' => $conn->insert_id, 'gambar_poster' => $nama_file_gambar], $start_time);
            } else {
                sendResponse('error', 'Error: Gagal menyimpan data. ' . $stmt->error, null, $start_time);
            }
            break;
            
        case 'update_kegiatan':
            $id_kegiatan = $_POST['id_kegiatan'] ?? '';
            $id_penyelenggara = $_POST['id_penyelenggara'] ?? '';
            $judul = $_POST['judul'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $benefit = $_POST['benefit'] ?? '';
            $lokasi = $_POST['lokasi'] ?? '';
            $kuota = $_POST['kuota'] ?? 0;
            $tanggal_mulai = $_POST['tanggal_mulai'] ?? null;
            $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
            
            $nama_file_gambar = null;
            
            if (isset($_FILES['gambar_poster']) && $_FILES['gambar_poster']['error'] == 0) {
                $target_dir = "../uploads/poster_kegiatan/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                
                $ekstensi = pathinfo($_FILES['gambar_poster']['name'], PATHINFO_EXTENSION);
                $nama_file_gambar = "kegiatan_" . $id_penyelenggara . "_" . uniqid() . "." . $ekstensi;
                $target_file = $target_dir . $nama_file_gambar;
                
                if (!move_uploaded_file($_FILES['gambar_poster']['tmp_name'], $target_file)) {
                    $nama_file_gambar = null;
                }
            }
            
            if ($nama_file_gambar) {
                $sql = "UPDATE tbl_kegiatan SET judul = ?, deskripsi = ?, lokasi = ?, tanggal_mulai = ?, tanggal_selesai = ?, kuota = ?, benefit = ?, gambar_poster = ?, status_kegiatan = 'Pending' WHERE id_kegiatan = ? AND id_penyelenggara = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssissii", $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $kuota, $benefit, $nama_file_gambar, $id_kegiatan, $id_penyelenggara);
            } else {
                $sql = "UPDATE tbl_kegiatan SET judul = ?, deskripsi = ?, lokasi = ?, tanggal_mulai = ?, tanggal_selesai = ?, kuota = ?, benefit = ?, status_kegiatan = 'Pending' WHERE id_kegiatan = ? AND id_penyelenggara = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssisii", $judul, $deskripsi, $lokasi, $tanggal_mulai, $tanggal_selesai, $kuota, $benefit, $id_kegiatan, $id_penyelenggara);
            }
            
            if ($stmt->execute()) {
                sendResponse('success', 'Kegiatan berhasil diperbarui. Menunggu persetujuan admin.', $nama_file_gambar ? ['gambar_poster' => $nama_file_gambar] : null, $start_time);
            } else {
                sendResponse('error', 'Error: Gagal update. ' . $stmt->error, null, $start_time);
            }
            break;
            
        case 'delete_kegiatan':
            $id_kegiatan = $_POST['id_kegiatan'] ?? '';
            $id_penyelenggara = $_POST['id_penyelenggara'] ?? '';
            
            $sql = "UPDATE tbl_kegiatan SET deleted_at = NOW() WHERE id_kegiatan = ? AND id_penyelenggara = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_kegiatan, $id_penyelenggara);
            
            if ($stmt->execute()) {
                sendResponse('success', 'Kegiatan berhasil dihapus.', null, $start_time);
            } else {
                sendResponse('error', 'Gagal menghapus kegiatan: ' . $stmt->error, null, $start_time);
            }
            break;
            
        case 'update_status_pendaftar':
            $id_penyelenggara = $_POST['id_penyelenggara'] ?? '';
            $id_pendaftaran = $_POST['id_pendaftaran'] ?? '';
            $status_baru = $_POST['status'] ?? '';
            
            if (!in_array($status_baru, ['Diterima', 'Ditolak'])) {
                sendResponse('error', 'Status tidak valid.', null, $start_time);
            }
            
            $sql_cek = "SELECT p.id_pendaftaran FROM tbl_pendaftaran p JOIN tbl_kegiatan k ON p.id_kegiatan = k.id_kegiatan WHERE p.id_pendaftaran = ? AND k.id_penyelenggara = ?";
            $stmt_cek = $conn->prepare($sql_cek);
            $stmt_cek->bind_param("ii", $id_pendaftaran, $id_penyelenggara);
            $stmt_cek->execute();
            
            if ($stmt_cek->get_result()->num_rows === 0) {
                sendResponse('error', 'Akses ditolak: Kegiatan bukan milik Anda.', null, $start_time);
            }
            
            $sql_update = "UPDATE tbl_pendaftaran SET status_pendaftaran = ? WHERE id_pendaftaran = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $status_baru, $id_pendaftaran);
            
            if ($stmt_update->execute()) {
                sendResponse('success', "Status pendaftar berhasil diubah menjadi: $status_baru", null, $start_time);
            } else {
                sendResponse('error', 'Gagal mengupdate status: ' . $stmt_update->error, null, $start_time);
            }
            break;
            
        case 'verify_penyelenggara':
            $id_penyelenggara = $_POST['id_penyelenggara'] ?? '';
            $status_baru = $_POST['status_baru'] ?? '';
            
            if (!in_array($status_baru, ['Verified', 'Rejected'])) {
                sendResponse('error', 'Status tidak valid.', null, $start_time);
            }
            
            $sql = "UPDATE tbl_penyelenggara SET status_verifikasi = ? WHERE id_penyelenggara = ? AND status_verifikasi = 'Pending'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status_baru, $id_penyelenggara);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendResponse('success', "Penyelenggara berhasil diubah menjadi '$status_baru'.", null, $start_time);
                } else {
                    sendResponse('info', 'Tidak ada data yang berubah (Mungkin sudah diverifikasi sebelumnya).', null, $start_time);
                }
            } else {
                sendResponse('error', 'Error Database: ' . $stmt->error, null, $start_time);
            }
            break;
            
        case 'update_status_kegiatan':
            $id_kegiatan = $_POST['id_kegiatan'] ?? '';
            $status_baru = $_POST['status_baru'] ?? '';
            
            if (!in_array($status_baru, ['Published', 'Rejected'])) {
                sendResponse('error', 'Status tidak valid.', null, $start_time);
            }
            
            $sql = "UPDATE tbl_kegiatan SET status_kegiatan = ? WHERE id_kegiatan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status_baru, $id_kegiatan);
            
            if ($stmt->execute()) {
                sendResponse('success', "Status kegiatan berhasil diubah menjadi '$status_baru'.", null, $start_time);
            } else {
                sendResponse('error', 'Error Database: ' . $stmt->error, null, $start_time);
            }
            break;
            
        case 'get_relawan_data':
            $id_relawan = $_GET['id_relawan'] ?? '';
            if (!$id_relawan) {
                sendResponse('error', 'ID relawan tidak diberikan.', null, $start_time);
            }
            
            $sql = "SELECT id_relawan, nama_lengkap, email, bio, keahlian, foto_profil, created_at FROM tbl_relawan WHERE id_relawan = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_relawan);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                sendResponse('success', 'Data relawan ditemukan.', $result->fetch_assoc(), $start_time);
            } else {
                sendResponse('error', 'Relawan tidak ditemukan.', null, $start_time);
            }
            break;
            
        case 'get_riwayat_relawan':
            $id_relawan = $_GET['id_relawan'] ?? '';
            if (!$id_relawan) {
                sendResponse('error', 'ID relawan tidak diberikan.', null, $start_time);
            }
            
            $sql = "SELECT p.id_pendaftaran, p.status_pendaftaran, p.created_at, 
                           k.judul, k.lokasi, k.tanggal_mulai, k.tanggal_selesai
                    FROM tbl_pendaftaran p
                    JOIN tbl_kegiatan k ON p.id_kegiatan = k.id_kegiatan
                    WHERE p.id_relawan = ?
                    ORDER BY p.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_relawan);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            sendResponse('success', 'Riwayat pendaftaran relawan.', $data, $start_time);
            break;
            
        case 'get_kegiatan_list':
            $id_penyelenggara = $_GET['id_penyelenggara'] ?? '';
            
            if ($id_penyelenggara) {
                $sql = "SELECT * FROM tbl_kegiatan WHERE id_penyelenggara = ? AND deleted_at IS NULL ORDER BY created_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_penyelenggara);
            } else {
                $sql = "SELECT * FROM tbl_kegiatan WHERE deleted_at IS NULL ORDER BY created_at DESC";
                $stmt = $conn->prepare($sql);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            sendResponse('success', 'Daftar kegiatan.', $data, $start_time);
            break;
            
        case 'get_pendaftar_list':
            $id_kegiatan = $_GET['id_kegiatan'] ?? '';
            if (!$id_kegiatan) {
                sendResponse('error', 'ID kegiatan tidak diberikan.', null, $start_time);
            }
            
            $sql = "SELECT p.id_pendaftaran, p.status_pendaftaran, p.created_at,
                           r.id_relawan, r.nama_lengkap, r.email, r.keahlian
                    FROM tbl_pendaftaran p
                    JOIN tbl_relawan r ON p.id_relawan = r.id_relawan
                    WHERE p.id_kegiatan = ?
                    ORDER BY p.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_kegiatan);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            sendResponse('success', 'Daftar pendaftar kegiatan.', $data, $start_time);
            break;
            
        case 'get_penyelenggara_pending':
            $sql = "SELECT id_penyelenggara, nama_organisasi, email, status_verifikasi, created_at 
                    FROM tbl_penyelenggara 
                    WHERE status_verifikasi = 'Pending' 
                    ORDER BY created_at ASC";
            $result = $conn->query($sql);
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            sendResponse('success', 'Daftar penyelenggara pending.', $data, $start_time);
            break;
            
        case 'get_kegiatan_pending':
            $sql = "SELECT k.*, p.nama_organisasi 
                    FROM tbl_kegiatan k
                    JOIN tbl_penyelenggara p ON k.id_penyelenggara = p.id_penyelenggara
                    WHERE k.status_kegiatan = 'Pending' AND k.deleted_at IS NULL
                    ORDER BY k.created_at ASC";
            $result = $conn->query($sql);
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            sendResponse('success', 'Daftar kegiatan pending.', $data, $start_time);
            break;
            
        case 'get_all_users':
            $sql_relawan = "SELECT id_relawan as id, nama_lengkap as nama, email, 'relawan' as role, created_at FROM tbl_relawan";
            $sql_penyelenggara = "SELECT id_penyelenggara as id, nama_organisasi as nama, email, 'penyelenggara' as role, created_at FROM tbl_penyelenggara";
            
            $result_relawan = $conn->query($sql_relawan);
            $result_penyelenggara = $conn->query($sql_penyelenggara);
            
            $data = [];
            while ($row = $result_relawan->fetch_assoc()) {
                $data[] = $row;
            }
            while ($row = $result_penyelenggara->fetch_assoc()) {
                $data[] = $row;
            }
            
            sendResponse('success', 'Daftar semua pengguna.', $data, $start_time);
            break;
            
        default:
            sendResponse('error', 'Action tidak dikenali.', null, $start_time);
    }
    
} catch (Exception $e) {
    sendResponse('error', 'Exception: ' . $e->getMessage(), null, $start_time);
}

$conn->close();
?>
