<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$keyword = isset($_GET['keyword']) ? escape($_GET['keyword']) : '';

if (strlen($keyword) < 2) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id_anggota, nama_lengkap, email 
        FROM anggota 
        WHERE status_verifikasi = 'aktif' 
        AND (nama_lengkap LIKE '%$keyword%' OR email LIKE '%$keyword%') 
        LIMIT 10";

$result = query($sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
