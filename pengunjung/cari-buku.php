<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Proses pencarian
$keyword = isset($_GET['keyword']) ? escape($_GET['keyword']) : '';
$kategori = isset($_GET['kategori']) ? escape($_GET['kategori']) : '';

$where = "WHERE 1=1";
if ($keyword) {
    $where .= " AND (judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%' OR penerbit LIKE '%$keyword%')";
}
if ($kategori) {
    $where .= " AND kategori = '$kategori'";
}

$buku_list = query("SELECT * FROM buku $where ORDER BY judul ASC");

// Ambil kategori untuk filter
$kategori_list = query("SELECT DISTINCT kategori FROM buku ORDER BY kategori ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Perpustakaan SDN259 Bukit Subur</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .public-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .public-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .public-header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .public-header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .public-header .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .search-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 15px;
        }
        
        .buku-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .buku-card {
            background: white;
            border: 1px solid #ecf0f1;
            border-radius: 10px;
            padding: 15px;
            transition: all 0.3s;
        }
        
        .buku-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .buku-cover {
            text-align: center;
            font-size: 60px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .buku-info h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
            min-height: 40px;
        }
        
        .buku-info p {
            margin: 5px 0;
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .buku-stok {
            margin-top: 10px;
        }
        
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .search-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="public-header">
        <div class="container">
            <h1>📚 Katalog Buku - Perpustakaan SDN259 Bukit Subur</h1>
            <div class="nav-links">
                <a href="../index.php">🏠 Beranda</a>
                <a href="daftar.php">✍️ Daftar Anggota</a>
                <a href="../auth/login.php">🔐 Login</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="search-section">
            <h2 style="margin-top: 0;">🔍 Pencarian Buku</h2>
            <form method="GET" action="" class="search-form">
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Cari berdasarkan judul, pengarang, atau penerbit..." 
                       value="<?php echo $keyword; ?>">
                
                <select name="kategori" class="form-control">
                    <option value="">Semua Kategori</option>
                    <?php while($row = mysqli_fetch_assoc($kategori_list)): ?>
                        <option value="<?php echo $row['kategori']; ?>" <?php echo $kategori == $row['kategori'] ? 'selected' : ''; ?>>
                            <?php echo $row['kategori']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" class="btn btn-primary">🔍 Cari</button>
            </form>
            
            <div class="info-box">
                <strong>ℹ️ Informasi:</strong> Untuk meminjam buku, silakan <a href="daftar.php">daftar sebagai anggota</a> terlebih dahulu atau <a href="../auth/login.php">login</a> jika sudah memiliki akun.
            </div>
        </div>

        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="margin-top: 0;">
                📚 Koleksi Buku 
                <?php if ($keyword || $kategori): ?>
                    (<?php echo mysqli_num_rows($buku_list); ?> buku ditemukan)
                <?php endif; ?>
            </h2>
            
            <div class="buku-grid">
                <?php 
                if (mysqli_num_rows($buku_list) > 0):
                    while($row = mysqli_fetch_assoc($buku_list)): 
                ?>
                <div class="buku-card">
                    <div class="buku-cover">
                        📖
                    </div>
                    <div class="buku-info">
                        <h4><?php echo $row['judul']; ?></h4>
                        <p><strong>✍️ Pengarang:</strong> <?php echo $row['pengarang']; ?></p>
                        <p><strong>🏢 Penerbit:</strong> <?php echo $row['penerbit']; ?></p>
                        <p><strong>📁 Kategori:</strong> <?php echo $row['kategori']; ?></p>
                        <p><strong>📅 Tahun:</strong> <?php echo $row['tahun_terbit']; ?></p>
                        <p><strong>📚 ISBN:</strong> <?php echo $row['isbn'] ?: '-'; ?></p>
                        
                        <div class="buku-stok">
                            <?php if ($row['jumlah_tersedia'] > 0): ?>
                                <span class="badge badge-success">✅ Tersedia (<?php echo $row['jumlah_tersedia']; ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-danger">❌ Tidak Tersedia</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <div style="text-align: center; padding: 40px; color: #7f8c8d; grid-column: 1/-1;">
                    <h3>📚 Tidak ada buku ditemukan</h3>
                    <p>Coba gunakan kata kunci lain atau pilih kategori yang berbeda</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer style="background: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 40px;">
        <p>&copy; 2025 Perpustakaan SDN259 Bukit Subur. All Rights Reserved.</p>
    </footer>
</body>
</html>
