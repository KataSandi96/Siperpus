<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['anggota']);

$page_title = 'Dashboard Anggota';

// Ambil ID anggota
$anggota = mysqli_fetch_assoc(query("SELECT id_anggota FROM anggota WHERE id_user = {$_SESSION['user_id']}"));
$id_anggota = $anggota['id_anggota'];

// Statistik anggota
$total_peminjaman = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE id_anggota = $id_anggota"))['total'];
$sedang_dipinjam = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE id_anggota = $id_anggota AND status = 'dipinjam'"))['total'];
$pemesanan_pending = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM pemesanan WHERE id_anggota = $id_anggota AND status_pemesanan = 'menunggu'"))['total'];
$tunggakan_denda = mysqli_fetch_assoc(query("SELECT COALESCE(SUM(total_denda), 0) as total FROM pengembalian WHERE id_peminjaman IN (SELECT id_peminjaman FROM peminjaman WHERE id_anggota = $id_anggota) AND status_pembayaran = 'belum_lunas'"))['total'];

// Peminjaman aktif
$peminjaman_aktif = query("SELECT p.*, b.judul, b.pengarang 
                           FROM peminjaman p 
                           JOIN buku b ON p.id_buku = b.id_buku 
                           WHERE p.id_anggota = $id_anggota AND p.status = 'dipinjam' 
                           ORDER BY p.tanggal_jatuh_tempo ASC");

// Notifikasi terbaru
$notifikasi = query("SELECT * FROM notifikasi 
                     WHERE id_user = {$_SESSION['user_id']} 
                     ORDER BY created_at DESC 
                     LIMIT 5");

include '../includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #3498db;">📚</div>
        <div class="stat-info">
            <h3><?php echo $total_peminjaman; ?></h3>
            <p>Total Peminjaman</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e74c3c;">📖</div>
        <div class="stat-info">
            <h3><?php echo $sedang_dipinjam; ?></h3>
            <p>Sedang Dipinjam</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #f39c12;">⏳</div>
        <div class="stat-info">
            <h3><?php echo $pemesanan_pending; ?></h3>
            <p>Pemesanan Pending</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: <?php echo $tunggakan_denda > 0 ? '#e74c3c' : '#27ae60'; ?>;">💰</div>
        <div class="stat-info">
            <h3><?php echo formatRupiah($tunggakan_denda); ?></h3>
            <p>Tunggakan Denda</p>
        </div>
    </div>
</div>

<?php if ($tunggakan_denda > 0): ?>
<div class="alert alert-danger">
    <strong>⚠️ Perhatian!</strong> Anda memiliki tunggakan denda sebesar <strong><?php echo formatRupiah($tunggakan_denda); ?></strong>. 
    Silakan selesaikan pembayaran untuk dapat meminjam buku kembali.
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>📖 Buku yang Sedang Dipinjam</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $today = date('Y-m-d');
                        if (mysqli_num_rows($peminjaman_aktif) > 0):
                            while($row = mysqli_fetch_assoc($peminjaman_aktif)): 
                                $is_late = $row['tanggal_jatuh_tempo'] < $today;
                                $days_left = floor((strtotime($row['tanggal_jatuh_tempo']) - strtotime($today)) / (60 * 60 * 24));
                        ?>
                        <tr>
                            <td><?php echo $row['judul']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])); ?></td>
                            <td>
                                <?php if ($is_late): ?>
                                    <span class="badge badge-danger">Terlambat</span>
                                <?php elseif ($days_left <= 1): ?>
                                    <span class="badge badge-warning">Segera Jatuh Tempo</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?php echo $days_left; ?> hari lagi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #7f8c8d;">
                                Tidak ada buku yang sedang dipinjam
                            </td>
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
                <h3>🔔 Notifikasi Terbaru</h3>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($notifikasi) > 0): ?>
                    <div class="notifikasi-list">
                        <?php while($row = mysqli_fetch_assoc($notifikasi)): ?>
                        <div class="notifikasi-item <?php echo $row['status_baca'] == 'belum_dibaca' ? 'unread' : ''; ?>">
                            <h4><?php echo $row['judul']; ?></h4>
                            <p><?php echo $row['pesan']; ?></p>
                            <small><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></small>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #7f8c8d; padding: 20px;">Tidak ada notifikasi</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.notifikasi-list {
    max-height: 400px;
    overflow-y: auto;
}

.notifikasi-item {
    padding: 15px;
    border-bottom: 1px solid #ecf0f1;
    transition: background 0.3s;
}

.notifikasi-item.unread {
    background-color: #e8f4fd;
    border-left: 3px solid #3498db;
}

.notifikasi-item:hover {
    background-color: #f8f9fa;
}

.notifikasi-item h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #2c3e50;
}

.notifikasi-item p {
    margin: 0 0 5px 0;
    font-size: 13px;
    color: #555;
}

.notifikasi-item small {
    color: #7f8c8d;
    font-size: 12px;
}
</style>

<?php include '../includes/footer.php'; ?>
