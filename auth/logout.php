<?php
session_start();

// Simpan data untuk notifikasi logout
if (isset($_SESSION['user_id'])) {
    require_once '../config/database.php';
    
    $user_id = $_SESSION['user_id'];
    $nama = $_SESSION['nama_lengkap'];
    
    // Insert notifikasi logout
    $notif_sql = "INSERT INTO notifikasi (id_user, judul, pesan) 
                  VALUES ('$user_id', 'Logout Berhasil', 
                  'Anda berhasil logout pada " . date('d-m-Y H:i:s') . "')";
    query($notif_sql);
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login dengan pesan
header("Location: login.php?logout=success");
exit();
?>
