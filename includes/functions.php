<?php
// Fungsi untuk cek login
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Fungsi untuk cek role
function checkRole($allowed_roles = []) {
    checkLogin();
    
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Fungsi untuk format tanggal Indonesia
function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Fungsi untuk format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi untuk sanitasi input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk hitung durasi login
function getDurasiLogin() {
    if (isset($_SESSION['login_time'])) {
        $durasi = time() - $_SESSION['login_time'];
        $jam = floor($durasi / 3600);
        $menit = floor(($durasi % 3600) / 60);
        
        if ($jam > 0) {
            return $jam . ' jam ' . $menit . ' menit';
        } else {
            return $menit . ' menit';
        }
    }
    return '0 menit';
}

// Fungsi untuk generate ID unik
function generateID($prefix = '') {
    return $prefix . date('YmdHis') . rand(1000, 9999);
}
?>
