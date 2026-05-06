<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$keyword = isset($_GET['keyword']) ? escape($_GET['keyword']) : '';

if (strlen($keyword) < 2) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id_buku, judul, pengarang, jumlah_tersedia 
        FROM buku 
        WHERE jumlah_tersedia > 0 
        AND (judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%') 
        LIMIT 10";

$result = query($sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
