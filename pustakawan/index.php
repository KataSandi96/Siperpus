<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['pustakawan']);

$page_title = 'Dashboard Pustakawan';

// Ambil ID pustakawan
$karyawan = mysqli_fetch_assoc(query("SELECT id_karyawan FROM karyawan WHERE id_user = {$_SESSION['user_id']}"));
$id_pustakawan = $karyawan['id_karyawan'];

// Statistik
$pemesanan_pending = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM pemesanan WHERE status_pemesanan = 'menunggu'"))['total'];
$anggota_pending = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM anggota WHERE status_verifikasi = 'menunggu'"))['total'];
$sedang_dipinjam = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'"))['total'];
$jatuh_tempo_hari_ini = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_jatuh_tempo = CURDATE() AND status = 'dipinjam'"))['total'];

// Pemesanan terbaru menunggu verifikasi
$pemesanan_list = query("SELECT p.*, a.nama_lengkap, b.judul 
                         FROM pemesanan p 
                         JOIN anggota a ON p.id_anggota = a.id_anggota 
                         JOIN buku b ON p.id_buku = b.id_buku 
                         WHERE p.status_pemesanan = 'menunggu' 
                         ORDER BY p.tanggal_pesan DESC 
                         LIMIT 5");

// Peminjaman jatuh tempo hari ini
$jatuh_tempo_list = query("SELECT p.*, a.nama_lengkap, b.judul 
                           FROM peminjaman p 
                           JOIN anggota a ON p.id_anggota = a.id_anggota 
                           JOIN buku b ON p.id_buku = b.id_buku 
                           WHERE p.tanggal_jatuh_tempo = CURDATE() AND p.status = 'dipinjam' 
                           ORDER BY p.tanggal_pinjam ASC");

include '../includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12;">⏳</div>
        <div class="stat-info">
            <h3><?php echo $pemesanan_pending; ?></h3>
            <p>Pemesanan Menunggu</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #9b59b6;">👤</div>
        <div class="stat-info">
            <h3><?php echo $anggota_pending; ?></h3>
            <p>Anggota Baru</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #3498db;">📖</div>
        <div class="stat-info">
            <h3><?php echo $sedang_dipinjam; ?></h3>
            <p>Sedang Dipinjam</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c;">⚠️</div>
        <div class="stat-info">
            <h3><?php echo $jatuh_tempo_hari_ini; ?></h3>
            <p>Jatuh Tempo Hari Ini</p>
        </div>
    </div>
</div>

<?php if ($pemesanan_pending > 0): ?>
<div class="alert alert-warning">
    <strong>⚠️ Perhatian!</strong> Ada <?php echo $pemesanan_pending; ?> pemesanan buku yang menunggu verifikasi.
    <a href="verifikasi-pemesanan.php" style="color: #333; text-decoration: underline;">Verifikasi sekarang</a>
</div>
<?php endif; ?>

<?php if ($anggota_pending > 0): ?>
<div class="alert alert-info">
    <strong>ℹ️ Info!</strong> Ada <?php echo $anggota_pending; ?> anggota baru yang menunggu verifikasi.
    <a href="data-anggota.php" style="color: #333; text-decoration: underline;">Lihat sekarang</a>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>⏳ Pemesanan Menunggu Verifikasi</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($pemesanan_list) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($pemesanan_list)): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pesan'])); ?></td>
                                <td><?php echo $row['nama_lengkap']; ?></td>
                                <td><?php echo substr($row['judul'], 0, 30); ?>...</td>
                                <td>
                                    <a href="verifikasi-pemesanan.php?id=<?php echo $row['id_pemesanan']; ?>" class="btn btn-sm btn-primary">Verifikasi</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #7f8c8d;">Tidak ada pemesanan menunggu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>⚠️ Jatuh Tempo Hari Ini</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($jatuh_tempo_list) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($jatuh_tempo_list)): ?>
                            <tr>
                                <td><?php echo $row['nama_lengkap']; ?></td>
                                <td><?php echo substr($row['judul'], 0, 30); ?>...</td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #7f8c8d;">Tidak ada peminjaman jatuh tempo</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
