<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan SDN259 Bukit Subur</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="assets/img/logo.png" alt="Logo SDN259" class="logo-img">
                <div class="logo-text">
                    <h1>SiPerpus</h1>
                    <p>Universitas Merangin</p>
                </div>
            </div>
            <nav class="nav">
                <a href="index.php" class="active">Beranda</a>
                <a href="pengunjung/cari-buku.php">Katalog Buku</a>
                <a href="pengunjung/daftar.php" class="btn-daftar">Daftar Anggota</a>
                <a href="auth/login.php" class="btn-login">Login</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Selamat Datang di<br>Sistem Informasi Perpustakaan</h2>
                <p>Kelola peminjaman buku dengan mudah dan cepat</p>
                <div class="hero-buttons">
                    <a href="pengunjung/cari-buku.php" class="btn btn-primary">Cari Buku</a>
                    <a href="pengunjung/daftar.php" class="btn btn-secondary">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <h3>Cari Koleksi Buku</h3>
            <form action="pengunjung/cari-buku.php" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="Cari berdasarkan judul, pengarang, atau kategori..." class="search-input">
                <button type="submit" class="btn-search">Cari</button>
            </form>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h3 class="section-title">Fitur Layanan</h3>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon">📚</div>
                    <h4>Katalog Lengkap</h4>
                    <p>Akses katalog buku perpustakaan secara online</p>
                </div>
                <div class="feature-card">
                    <div class="icon">🔍</div>
                    <h4>Pencarian Mudah</h4>
                    <p>Cari buku berdasarkan judul, pengarang, atau kategori</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📖</div>
                    <h4>Pemesanan Online</h4>
                    <p>Pesan buku yang ingin dipinjam secara online</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📊</div>
                    <h4>Histori Peminjaman</h4>
                    <p>Lihat riwayat peminjaman buku Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info">
        <div class="container">
            <h3 class="section-title">Informasi Perpustakaan</h3>
            <div class="info-grid">
                <div class="info-card">
                    <h4>Jam Operasional</h4>
                    <p>Senin - Jumat: 08.00 - 15.00 WIB<br>Sabtu: 08.00 - 12.00 WIB</p>
                </div>
                <div class="info-card">
                    <h4>Durasi Peminjaman</h4>
                    <p>Maksimal 7 hari per buku</p>
                </div>
                <div class="info-card">
                    <h4>Batas Peminjaman</h4>
                    <p>Maksimal 3 buku per anggota</p>
                </div>
                <div class="info-card">
                    <h4>Denda Keterlambatan</h4>
                    <p>Rp 1.000 per hari</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 SiPerpus - Perpustakaan Digital - Universitas Merangin. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
