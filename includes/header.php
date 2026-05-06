<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?> SiPerpus - UMER</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/logo.png" alt="Logo" class="sidebar-logo" onerror="this.style.display='none'">
                <h3>SiPerpus<br>Universitas Merangin</h3>
            </div>
            
            <div class="user-info">
                <p class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></p>
                <p class="user-role"><?php echo ucfirst($_SESSION['role']); ?></p>
            </div>
            
            <nav class="sidebar-nav">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <!-- MENU ADMIN -->
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a>
                    <a href="data-buku.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data-buku.php' ? 'active' : ''; ?>">
                        📚 Data Buku
                    </a>
                    <a href="data-anggota.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data-anggota.php' ? 'active' : ''; ?>">
                        👥 Data Anggota
                    </a>
                    <a href="data-karyawan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data-karyawan.php' ? 'active' : ''; ?>">
                        👨‍💼 Data Karyawan
                    </a>
                    <a href="laporan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                        📄 Laporan
                    </a>
                    <a href="pengaturan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pengaturan.php' ? 'active' : ''; ?>">
                        ⚙️ Pengaturan
                    </a>
                
                <?php elseif ($_SESSION['role'] == 'pustakawan'): ?>
                    <!-- MENU PUSTAKAWAN -->
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a>
                    <a href="verifikasi-pemesanan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'verifikasi-pemesanan.php' ? 'active' : ''; ?>">
                        ✅ Verifikasi Pemesanan
                    </a>
                    <a href="input-peminjaman.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'input-peminjaman.php' ? 'active' : ''; ?>">
                        📖 Input Peminjaman
                    </a>
                    <a href="input-pengembalian.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'input-pengembalian.php' ? 'active' : ''; ?>">
                        ↩️ Input Pengembalian
                    </a>
                    <a href="data-anggota.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'data-anggota.php' ? 'active' : ''; ?>">
                        👥 Data Anggota
                    </a>
                
                <?php elseif ($_SESSION['role'] == 'anggota'): ?>
                    <!-- MENU ANGGOTA -->
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a>
                    <a href="cari-buku.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'cari-buku.php' ? 'active' : ''; ?>">
                        🔍 Cari Buku
                    </a>
                    <a href="pesan-buku.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pesan-buku.php' ? 'active' : ''; ?>">
                        📖 Pesan Buku
                    </a>
                    <a href="histori.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'histori.php' ? 'active' : ''; ?>">
                        📜 Histori Peminjaman
                    </a>
                
                <?php endif; ?>
                
                <!-- MENU LOGOUT (UNTUK SEMUA ROLE) -->
                <a href="../auth/logout.php" class="nav-link logout">
                    🚪 Logout
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="content-header">
                <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                <div class="header-actions">
                    <span class="datetime"><?php echo date('d F Y, H:i'); ?> WIB</span>
                </div>
            </header>
            
            <div class="content-body">
