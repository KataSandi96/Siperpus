<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['admin']);

$page_title = 'Laporan';

// Fungsi export Excel
function exportToExcel($data, $headers, $filename) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<table border="1">';
    echo '<thead><tr>';
    foreach ($headers as $header) {
        echo '<th>' . $header . '</th>';
    }
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . $cell . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    exit();
}

// Proses export
if (isset($_GET['export'])) {
    $jenis = $_GET['export'];
    
    if ($jenis == 'buku') {
        $data = query("SELECT judul, pengarang, penerbit, isbn, kategori, tahun_terbit, jumlah_total, jumlah_tersedia FROM buku ORDER BY judul");
        $headers = ['Judul', 'Pengarang', 'Penerbit', 'ISBN', 'Kategori', 'Tahun Terbit', 'Total', 'Tersedia'];
        $rows = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $rows[] = $row;
        }
        exportToExcel($rows, $headers, 'Laporan_Data_Buku_' . date('Ymd'));
    }
    
    elseif ($jenis == 'anggota') {
        $data = query("SELECT nama_lengkap, email, no_telepon, alamat, tanggal_daftar, status_verifikasi FROM anggota ORDER BY nama_lengkap");
        $headers = ['Nama', 'Email', 'No. Telepon', 'Alamat', 'Tanggal Daftar', 'Status'];
        $rows = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $rows[] = $row;
        }
        exportToExcel($rows, $headers, 'Laporan_Data_Anggota_' . date('Ymd'));
    }
    
    elseif ($jenis == 'peminjaman') {
        $dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
        $sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d');
        
        $data = query("SELECT a.nama_lengkap, b.judul, p.tanggal_pinjam, p.tanggal_jatuh_tempo, p.status 
                       FROM peminjaman p 
                       JOIN anggota a ON p.id_anggota = a.id_anggota 
                       JOIN buku b ON p.id_buku = b.id_buku 
                       WHERE p.tanggal_pinjam BETWEEN '$dari' AND '$sampai'
                       ORDER BY p.tanggal_pinjam DESC");
        $headers = ['Nama Anggota', 'Judul Buku', 'Tanggal Pinjam', 'Jatuh Tempo', 'Status'];
        $rows = [];
        while ($row = mysqli_fetch_assoc($data)) {
            $rows[] = $row;
        }
        exportToExcel($rows, $headers, 'Laporan_Peminjaman_' . date('Ymd'));
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>📊 Laporan Data Buku</h3>
            </div>
            <div class="card-body">
                <p>Laporan seluruh data koleksi buku perpustakaan</p>
                <a href="?export=buku" class="btn btn-success">📥 Export Excel</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>👥 Laporan Data Anggota</h3>
            </div>
            <div class="card-body">
                <p>Laporan seluruh data anggota perpustakaan</p>
                <a href="?export=anggota" class="btn btn-success">📥 Export Excel</a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>📖 Laporan Peminjaman</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="export" value="peminjaman">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="<?php echo date('Y-m-01'); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-success" style="width: 100%;">📥 Export Excel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>🏆 Buku Terpopuler</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Total Dipinjam</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $populer = query("SELECT b.judul, b.pengarang, COUNT(p.id_buku) as total_pinjam 
                                 FROM peminjaman p 
                                 JOIN buku b ON p.id_buku = b.id_buku 
                                 GROUP BY p.id_buku 
                                 ORDER BY total_pinjam DESC 
                                 LIMIT 10");
                $rank = 1;
                while ($row = mysqli_fetch_assoc($populer)):
                ?>
                <tr>
                    <td><?php echo $rank++; ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td><span class="badge badge-success"><?php echo $row['total_pinjam']; ?>x</span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
