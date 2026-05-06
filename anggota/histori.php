<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['anggota']);

$page_title = 'Histori Peminjaman';

// Ambil ID anggota
$anggota = mysqli_fetch_assoc(query("SELECT id_anggota FROM anggota WHERE id_user = {$_SESSION['user_id']}"));
$id_anggota = $anggota['id_anggota'];

// Ambil histori peminjaman
$histori_list = query("SELECT p.*, b.judul, b.pengarang, 
                       pg.tanggal_kembali, pg.keterlambatan_hari, 
                       pg.total_denda, pg.status_pembayaran, pg.kondisi_buku
                       FROM peminjaman p 
                       JOIN buku b ON p.id_buku = b.id_buku 
                       LEFT JOIN pengembalian pg ON p.id_peminjaman = pg.id_peminjaman
                       WHERE p.id_anggota = $id_anggota 
                       ORDER BY p.tanggal_pinjam DESC");

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>📜 Histori Peminjaman Buku</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Tanggal Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Tanggal Kembali</th>
                    <th>Keterlambatan</th>
                    <th>Denda</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($histori_list) > 0):
                    while($row = mysqli_fetch_assoc($histori_list)): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])); ?></td>
                    <td>
                        <?php if ($row['status'] == 'dikembalikan'): ?>
                            <?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?>
                        <?php else: ?>
                            <span class="badge badge-warning">Belum Dikembalikan</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'dikembalikan' && $row['keterlambatan_hari'] > 0): ?>
                            <span class="badge badge-danger"><?php echo $row['keterlambatan_hari']; ?> hari</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'dikembalikan' && $row['total_denda'] > 0): ?>
                            <strong><?php echo formatRupiah($row['total_denda']); ?></strong>
                            <?php if ($row['status_pembayaran'] == 'belum_lunas'): ?>
                                <br><span class="badge badge-danger">Belum Lunas</span>
                            <?php else: ?>
                                <br><span class="badge badge-success">Lunas</span>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'dipinjam'): ?>
                            <span class="badge badge-primary">Sedang Dipinjam</span>
                        <?php else: ?>
                            <span class="badge badge-success">Dikembalikan</span>
                            <?php if ($row['kondisi_buku'] == 'rusak'): ?>
                                <br><small style="color: #e74c3c;">Kondisi: Rusak</small>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="9" style="text-align: center; color: #7f8c8d; padding: 40px;">
                        <h3>📚 Belum ada riwayat peminjaman</h3>
                        <p>Mulai meminjam buku untuk melihat histori peminjaman Anda</p>
                        <a href="cari-buku.php" class="btn btn-primary" style="margin-top: 10px;">Cari Buku</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
