<?php
$db_host = 'localhost'; 
$db_user = 'root';      
$db_pass = '';          
$db_name = 'relantara';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>