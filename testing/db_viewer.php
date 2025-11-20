<?php
include '../config/db_connect.php';

$table = $_GET['table'] ?? 'tbl_relawan';
$allowed_tables = ['tbl_relawan', 'tbl_penyelenggara', 'tbl_kegiatan', 'tbl_pendaftaran', 'tbl_admin'];

if (!in_array($table, $allowed_tables)) {
    echo '<div class="empty-state">Invalid table name</div>';
    exit;
}

$check_column = $conn->query("SHOW COLUMNS FROM $table LIKE 'created_at'");
$has_created_at = $check_column->num_rows > 0;

$order_clause = $has_created_at ? "ORDER BY created_at DESC" : "";
$sql = "SELECT * FROM $table $order_clause LIMIT 50";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $columns = array_keys($first_row);
    
    echo '<table class="db-table">';
    echo '<thead><tr>';
    foreach ($columns as $col) {
        echo '<th>' . htmlspecialchars($col) . '</th>';
    }
    echo '</tr></thead>';
    echo '<tbody>';
    
    $result->data_seek(0);
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        foreach ($columns as $col) {
            $value = $row[$col];
            
            if ($col === 'password') {
                $value = '***HIDDEN***';
            } elseif (in_array($col, ['status_verifikasi', 'status_kegiatan', 'status_pendaftaran'])) {
                $status_class = $value ? strtolower(str_replace(['Verified', 'Published', 'Diterima'], 'verified', $value)) : 'pending';
                $display_value = $value ?? 'Pending';
                $value = '<span class="status-badge ' . $status_class . '">' . htmlspecialchars($display_value) . '</span>';
            } elseif ($col === 'created_at' || $col === 'updated_at' || $col === 'deleted_at') {
                $value = $value ? date('Y-m-d H:i', strtotime($value)) : '-';
            } elseif ($value === null || $value === '') {
                $value = '<span style="color: #858585;">NULL</span>';
            } elseif (strlen($value) > 50) {
                $value = htmlspecialchars(substr($value, 0, 50)) . '...';
            } else {
                $value = htmlspecialchars($value);
            }
            
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<div class="empty-state">No data found in ' . htmlspecialchars($table) . '</div>';
}

$conn->close();
?>
