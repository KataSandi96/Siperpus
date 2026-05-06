<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['anggota']);

$page_title = 'Cari Buku';

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

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>🔍 Pencarian Buku</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="" class="search-filter-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kata Kunci</label>
                        <input type="text" name="keyword" class="form-control" 
                               placeholder="Cari berdasarkan judul, pengarang, atau penerbit..." 
                               value="<?php echo $keyword; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php while($row = mysqli_fetch_assoc($kategori_list)): ?>
                                <option value="<?php echo $row['kategori']; ?>" <?php echo $kategori == $row['kategori'] ? 'selected' : ''; ?>>
                                    <?php echo $row['kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">🔍 Cari</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>📚 Hasil Pencarian 
            <?php if ($keyword || $kategori): ?>
                (<?php echo mysqli_num_rows($buku_list); ?> buku ditemukan)
            <?php endif; ?>
        </h3>
    </div>
    <div class="card-body">
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
                    <p class="pengarang">✍️ <?php echo $row['pengarang']; ?></p>
                    <p class="penerbit">🏢 <?php echo $row['penerbit']; ?></p>
                    <p class="kategori">📁 <?php echo $row['kategori']; ?></p>
                    <p class="tahun">📅 <?php echo $row['tahun_terbit']; ?></p>
                    
                    <div class="buku-stok">
                        <?php if ($row['jumlah_tersedia'] > 0): ?>
                            <span class="badge badge-success">✅ Tersedia (<?php echo $row['jumlah_tersedia']; ?>)</span>
                        <?php else: ?>
                            <span class="badge badge-danger">❌ Tidak Tersedia</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($row['jumlah_tersedia'] > 0): ?>
                        <a href="pesan-buku.php?id=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-primary" style="width: 100%; margin-top: 10px;">
                            📖 Pesan Buku
                        </a>
                    <?php endif; ?>
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

<style>
.search-filter-form .row {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 15px;
    align-items: end;
}

.buku-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.buku-card {
    border: 1px solid #ecf0f1;
    border-radius: 10px;
    padding: 15px;
    background: white;
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

@media (max-width: 768px) {
    .search-filter-form .row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../includes/footer.php'; ?>
