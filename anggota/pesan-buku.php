<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['anggota']);

$page_title = 'Pesan Buku';
$success = '';
$error = '';

// Ambil ID anggota
$anggota = mysqli_fetch_assoc(query("SELECT * FROM anggota WHERE id_user = {$_SESSION['user_id']}"));
$id_anggota = $anggota['id_anggota'];

// Ambil pengaturan
$pengaturan = [];
$result = query("SELECT * FROM pengaturan_sistem");
while ($row = mysqli_fetch_assoc($result)) {
    $pengaturan[$row['nama_parameter']] = $row['nilai_parameter'];
}

// Proses pemesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_buku = (int)$_POST['id_buku'];
    
    // Cek tunggakan denda
    $tunggakan = mysqli_fetch_assoc(query("SELECT COALESCE(SUM(total_denda), 0) as total FROM pengembalian WHERE id_peminjaman IN (SELECT id_peminjaman FROM peminjaman WHERE id_anggota = $id_anggota) AND status_pembayaran = 'belum_lunas'"))['total'];
    
    if ($tunggakan > 0) {
        $error = "Anda memiliki tunggakan denda sebesar " . formatRupiah($tunggakan) . ". Selesaikan pembayaran terlebih dahulu!";
    } else {
        // Cek batas peminjaman
        $total_pinjam = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE id_anggota = $id_anggota AND status = 'dipinjam'"))['total'];
        
        if ($total_pinjam >= $pengaturan['batas_peminjaman']) {
            $error = "Anda sudah mencapai batas maksimal peminjaman ({$pengaturan['batas_peminjaman']} buku)!";
        } else {
            // Cek apakah sudah pernah pesan buku yang sama
            $cek_pesan = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM pemesanan WHERE id_anggota = $id_anggota AND id_buku = $id_buku AND status_pemesanan = 'menunggu'"))['total'];
            
            if ($cek_pesan > 0) {
                $error = "Anda sudah pernah memesan buku ini dan masih menunggu verifikasi!";
            } else {
                // Cek ketersediaan buku
                $buku = mysqli_fetch_assoc(query("SELECT * FROM buku WHERE id_buku = $id_buku"));
                
                if ($buku['jumlah_tersedia'] < 1) {
                    $error = "Maaf, buku tidak tersedia untuk dipinjam!";
                } else {
                    // Insert pemesanan
                    $sql = "INSERT INTO pemesanan (id_anggota, id_buku, tanggal_pesan, status_pemesanan) 
                            VALUES ($id_anggota, $id_buku, CURDATE(), 'menunggu')";
                    
                    if (query($sql)) {
                        $success = "Pemesanan buku berhasil! Silakan tunggu verifikasi dari pustakawan.";
                    } else {
                        $error = "Gagal melakukan pemesanan!";
                    }
                }
            }
        }
    }
}

// Ambil detail buku jika ada ID
$buku = null;
if (isset($_GET['id'])) {
    $id_buku = (int)$_GET['id'];
    $buku = mysqli_fetch_assoc(query("SELECT * FROM buku WHERE id_buku = $id_buku"));
}

// Ambil daftar pemesanan anggota
$pemesanan_list = query("SELECT p.*, b.judul, b.pengarang 
                         FROM pemesanan p 
                         JOIN buku b ON p.id_buku = b.id_buku 
                         WHERE p.id_anggota = $id_anggota 
                         ORDER BY p.tanggal_pesan DESC");

include '../includes/header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($buku): ?>
<div class="card">
    <div class="card-header">
        <h3>📖 Detail Buku</h3>
    </div>
    <div class="card-body">
        <div class="buku-detail">
            <div class="buku-cover-large">
                📖
            </div>
            <div class="buku-info-detail">
                <h2><?php echo $buku['judul']; ?></h2>
                <table class="info-table">
                    <tr>
                        <th>Pengarang:</th>
                        <td><?php echo $buku['pengarang']; ?></td>
                    </tr>
                    <tr>
                        <th>Penerbit:</th>
                        <td><?php echo $buku['penerbit']; ?></td>
                    </tr>
                    <tr>
                        <th>ISBN:</th>
                        <td><?php echo $buku['isbn']; ?></td>
                    </tr>
                    <tr>
                        <th>Kategori:</th>
                        <td><?php echo $buku['kategori']; ?></td>
                    </tr>
                    <tr>
                        <th>Tahun Terbit:</th>
                        <td><?php echo $buku['tahun_terbit']; ?></td>
                    </tr>
                    <tr>
                        <th>Ketersediaan:</th>
                        <td>
                            <?php if ($buku['jumlah_tersedia'] > 0): ?>
                                <span class="badge badge-success">Tersedia (<?php echo $buku['jumlah_tersedia']; ?> dari <?php echo $buku['jumlah_total']; ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tidak Tersedia</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <?php if ($buku['jumlah_tersedia'] > 0): ?>
                    <form method="POST" action="" style="margin-top: 20px;">
                        <input type="hidden" name="id_buku" value="<?php echo $buku['id_buku']; ?>">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin memesan buku ini?')">
                            📖 Pesan Buku Ini
                        </button>
                        <a href="cari-buku.php" class="btn btn-secondary">Kembali</a>
                    </form>
                <?php else: ?>
                    <p style="color: #e74c3c; margin-top: 20px;">Buku tidak tersedia untuk dipinjam saat ini.</p>
                    <a href="cari-buku.php" class="btn btn-secondary">Kembali</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card mt-3">
    <div class="card-header">
        <h3>📋 Daftar Pemesanan Saya</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pesan</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($pemesanan_list) > 0):
                    while($row = mysqli_fetch_assoc($pemesanan_list)): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pesan'])); ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td>
                        <?php if ($row['status_pemesanan'] == 'menunggu'): ?>
                            <span class="badge badge-warning">Menunggu Verifikasi</span>
                        <?php elseif ($row['status_pemesanan'] == 'disetujui'): ?>
                            <span class="badge badge-success">Disetujui</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Ditolak</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status_pemesanan'] == 'disetujui'): ?>
                            Silakan datang ke perpustakaan untuk meminjam buku
                        <?php elseif ($row['status_pemesanan'] == 'ditolak'): ?>
                            <?php echo $row['alasan_penolakan']; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 30px;">
                        Belum ada pemesanan buku
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.buku-detail {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
}

.buku-cover-large {
    text-align: center;
    font-size: 120px;
    padding: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    height: fit-content;
}

.buku-info-detail h2 {
    margin: 0 0 20px 0;
    color: #2c3e50;
}

.info-table {
    width: 100%;
}

.info-table th {
    width: 150px;
    padding: 10px 0;
    text-align: left;
    color: #7f8c8d;
    font-weight: 500;
}

.info-table td {
    padding: 10px 0;
    color: #2c3e50;
}

@media (max-width: 768px) {
    .buku-detail {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../includes/footer.php'; ?>
