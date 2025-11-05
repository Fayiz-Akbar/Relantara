<?php
header('Content-Type: application/json');
include '../config/auth_check.php';
include '../config/db_connect.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_volunteer = $_POST['id_volunteer'] ?? null;

    if ($id_volunteer) {
        $sql = "UPDATE tbl_volunteer 
                SET deleted_at = NOW() 
                WHERE id_volunteer = ?";

        $stmt = mysqli_prepare($conn, $sql);
        
        mysqli_stmt_bind_param($stmt, "i", $id_volunteer);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $response['status'] = 'success';
                $response['message'] = 'Data volunteer berhasil dihapus (soft delete).';
            } else {
                $response['message'] = 'Data tidak ditemukan dengan ID tersebut.';
            }
        } else {
            $response['message'] = 'Gagal mengeksekusi kueri: ' . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);

    } else {
        $response['message'] = 'ID Volunteer tidak boleh kosong.';
    }

} else {
    $response['message'] = 'Metode request tidak valid. Harap gunakan POST.';
}

mysqli_close($conn);
echo json_encode($response);

?>