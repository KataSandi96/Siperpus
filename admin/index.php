<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Cek login dan role
checkRole(['admin']);

$page_title = 'Dashboard Admin';

// Ambil statistik
$total_buku = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM anggota WHERE status_verifikasi = 'aktif'"))['total'];
$total_peminjaman = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'"))['total'];
$total_karyawan = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM karyawan"))['total'];

// Anggota menunggu verifikasi
$anggota_pending = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM anggota WHERE status_verifikasi = 'menunggu'"))['total'];

// Peminjaman jatuh tempo hari ini dan besok
$tgl_sekarang = date('Y-m-d');
$tgl_besok = date('Y-m-d', strtotime('+1 day'));
$jatuh_tempo_hari_ini = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_jatuh_tempo = '$tgl_sekarang' AND status = 'dipinjam'"))['total'];
$jatuh_tempo_besok = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_jatuh_tempo = '$tgl_besok' AND status = 'dipinjam'"))['total'];

// Buku terpopuler
$buku_populer = query("SELECT b.judul, b.pengarang, COUNT(p.id_buku) as total_pinjam 
                       FROM peminjaman p 
                       JOIN buku b ON p.id_buku = b.id_buku 
                       GROUP BY p.id_buku 
                       ORDER BY total_pinjam DESC 
                       LIMIT 5");

// Peminjaman terbaru
$peminjaman_terbaru = query("SELECT p.*, b.judul, a.nama_lengkap 
                             FROM peminjaman p 
                             JOIN buku b ON p.id_buku = b.id_buku 
                             JOIN anggota a ON p.id_anggota = a.id_anggota 
                             WHERE p.status = 'dipinjam'
                             ORDER BY p.tanggal_pinjam DESC 
                             LIMIT 5");

include '../includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3498db;">📚</div>
        <div class="stat-info">
            <h3><?php echo $total_buku; ?></h3>
            <p>Total Buku</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #2ecc71;">👥</div>
        <div class="stat-info">
            <h3><?php echo $total_anggota; ?></h3>
            <p>Anggota Aktif</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c;">📖</div>
        <div class="stat-info">
            <h3><?php echo $total_peminjaman; ?></h3>
            <p>Sedang Dipinjam</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12;">👨‍💼</div>
        <div class="stat-info">
            <h3><?php echo $total_karyawan; ?></h3>
            <p>Karyawan</p>
        </div>
    </div>
</div>

<?php if ($anggota_pending > 0): ?>
<div class="alert alert-warning">
    <strong>⚠️ Perhatian!</strong> Ada <?php echo $anggota_pending; ?> anggota baru yang menunggu verifikasi.
    <a href="data-anggota.php" style="color: #333; text-decoration: underline;">Lihat sekarang</a>
</div>
<?php endif; ?>

<?php if ($jatuh_tempo_hari_ini > 0 || $jatuh_tempo_besok > 0): ?>
<div class="alert alert-info">
    <strong>ℹ️ Info!</strong> 
    <?php if ($jatuh_tempo_hari_ini > 0): ?>
        <?php echo $jatuh_tempo_hari_ini; ?> peminjaman jatuh tempo hari ini. 
    <?php endif; ?>
    <?php if ($jatuh_tempo_besok > 0): ?>
        <?php echo $jatuh_tempo_besok; ?> peminjaman jatuh tempo besok.
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>📊 Buku Terpopuler</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Total Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while($row = mysqli_fetch_assoc($buku_populer)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['judul']; ?></td>
                            <td><?php echo $row['pengarang']; ?></td>
                            <td><span class="badge badge-success"><?php echo $row['total_pinjam']; ?>x</span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>📖 Peminjaman Terbaru</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Jatuh Tempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($peminjaman_terbaru)): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo substr($row['judul'], 0, 30); ?>...</td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
