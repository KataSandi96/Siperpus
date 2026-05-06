<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['pustakawan']);

$page_title = 'Input Pengembalian';
$success = '';
$error = '';

// Ambil ID pustakawan
$karyawan = mysqli_fetch_assoc(query("SELECT id_karyawan FROM karyawan WHERE id_user = {$_SESSION['user_id']}"));
$id_pustakawan = $karyawan['id_karyawan'];

// Ambil pengaturan sistem
$pengaturan = [];
$result = query("SELECT * FROM pengaturan_sistem");
while ($row = mysqli_fetch_assoc($result)) {
    $pengaturan[$row['nama_parameter']] = $row['nilai_parameter'];
}

// Proses pengembalian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_peminjaman = (int)$_POST['id_peminjaman'];
    $tanggal_kembali = escape($_POST['tanggal_kembali']);
    $kondisi_buku = escape($_POST['kondisi_buku']);
    $denda_kerusakan = (int)$_POST['denda_kerusakan'];
    
    // Ambil data peminjaman
    $peminjaman = mysqli_fetch_assoc(query("SELECT * FROM peminjaman WHERE id_peminjaman = $id_peminjaman"));
    
    if (!$peminjaman) {
        $error = "Data peminjaman tidak ditemukan!";
    } else {
        // Hitung keterlambatan
        $tanggal_jatuh_tempo = strtotime($peminjaman['tanggal_jatuh_tempo']);
        $tanggal_pengembalian = strtotime($tanggal_kembali);
        
        $keterlambatan_hari = 0;
        $denda_keterlambatan = 0;
        
        if ($tanggal_pengembalian > $tanggal_jatuh_tempo) {
            $keterlambatan_hari = floor(($tanggal_pengembalian - $tanggal_jatuh_tempo) / (60 * 60 * 24));
            $denda_keterlambatan = $keterlambatan_hari * $pengaturan['denda_per_hari'];
        }
        
        // Total denda
        $total_denda = $denda_keterlambatan + $denda_kerusakan;
        $status_pembayaran = $total_denda > 0 ? 'belum_lunas' : 'lunas';
        
        // Insert pengembalian
        $sql = "INSERT INTO pengembalian (
                    id_peminjaman, tanggal_kembali, kondisi_buku, 
                    keterlambatan_hari, denda_keterlambatan, 
                    denda_kerusakan, total_denda, status_pembayaran, id_pustakawan
                ) VALUES (
                    $id_peminjaman, '$tanggal_kembali', '$kondisi_buku',
                    $keterlambatan_hari, $denda_keterlambatan,
                    $denda_kerusakan, $total_denda, '$status_pembayaran', $id_pustakawan
                )";
        
        if (query($sql)) {
            // Update status peminjaman
            query("UPDATE peminjaman SET status = 'dikembalikan' WHERE id_peminjaman = $id_peminjaman");
            
            // Update stok buku
            query("UPDATE buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id_buku = {$peminjaman['id_buku']}");
            
            // Kirim notifikasi ke anggota
            $anggota = mysqli_fetch_assoc(query("SELECT id_user FROM anggota WHERE id_anggota = {$peminjaman['id_anggota']}"));
            $pesan_notif = "Pengembalian buku berhasil dicatat.";
            if ($total_denda > 0) {
                $pesan_notif .= " Total denda: " . formatRupiah($total_denda);
            }
            
            $notif_sql = "INSERT INTO notifikasi (id_user, judul, pesan) 
                         VALUES ({$anggota['id_user']}, 'Pengembalian Berhasil', '$pesan_notif')";
            query($notif_sql);
            
            $success = "Pengembalian berhasil dicatat!";
            if ($total_denda > 0) {
                $success .= " Total denda: " . formatRupiah($total_denda);
            }
        } else {
            $error = "Gagal mencatat pengembalian!";
        }
    }
}

// Ambil peminjaman yang belum dikembalikan
$peminjaman_aktif = query("SELECT p.*, a.nama_lengkap, b.judul, b.pengarang 
                           FROM peminjaman p 
                           JOIN anggota a ON p.id_anggota = a.id_anggota 
                           JOIN buku b ON p.id_buku = b.id_buku 
                           WHERE p.status = 'dipinjam' 
                           ORDER BY p.tanggal_jatuh_tempo ASC");

include '../includes/header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>↩️ Data Peminjaman Aktif</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $today = date('Y-m-d');
                while($row = mysqli_fetch_assoc($peminjaman_aktif)): 
                    $is_late = $row['tanggal_jatuh_tempo'] < $today;
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nama_lengkap']; ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])); ?></td>
                    <td>
                        <?php if ($is_late): ?>
                            <span class="badge badge-danger">Terlambat</span>
                        <?php elseif ($row['tanggal_jatuh_tempo'] == $today): ?>
                            <span class="badge badge-warning">Jatuh Tempo Hari Ini</span>
                        <?php else: ?>
                            <span class="badge badge-success">Normal</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='prosesKembali(<?php echo json_encode($row); ?>)'>
                            ↩️ Kembalikan
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pengembalian -->
<div id="pengembalianModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('pengembalianModal')">&times;</span>
        <h2>Proses Pengembalian Buku</h2>
        
        <div id="info-peminjaman" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <!-- Info akan diisi via JavaScript -->
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="id_peminjaman" id="form_id_peminjaman">
            
            <div class="form-group">
                <label>Tanggal Pengembalian *</label>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" value="<?php echo date('Y-m-d'); ?>" required onchange="hitungDenda()">
            </div>
            
            <div class="form-group">
                <label>Kondisi Buku *</label>
                <select name="kondisi_buku" id="kondisi_buku" required onchange="cekKondisi()">
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>
            
            <div class="form-group" id="group_denda_kerusakan" style="display: none;">
                <label>Denda Kerusakan (Rp)</label>
                <input type="number" name="denda_kerusakan" id="denda_kerusakan" value="0" min="0" onchange="hitungDenda()">
            </div>
            
            <div id="rincian-denda" style="background: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: none;">
                <!-- Rincian denda akan diisi via JavaScript -->
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Proses Pengembalian</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('pengembalianModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPeminjaman = null;
const dendaPerHari = <?php echo $pengaturan['denda_per_hari']; ?>;

function prosesKembali(data) {
    currentPeminjaman = data;
    
    // Isi info peminjaman
    const infoPeminjaman = `
        <table style="width: 100%;">
            <tr>
                <td><strong>Anggota:</strong></td>
                <td>${data.nama_lengkap}</td>
            </tr>
            <tr>
                <td><strong>Buku:</strong></td>
                <td>${data.judul}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Pinjam:</strong></td>
                <td>${formatTanggal(data.tanggal_pinjam)}</td>
            </tr>
            <tr>
                <td><strong>Jatuh Tempo:</strong></td>
                <td>${formatTanggal(data.tanggal_jatuh_tempo)}</td>
            </tr>
        </table>
    `;
    
    document.getElementById('info-peminjaman').innerHTML = infoPeminjaman;
    document.getElementById('form_id_peminjaman').value = data.id_peminjaman;
    
    // Reset form
    document.getElementById('kondisi_buku').value = 'baik';
    document.getElementById('denda_kerusakan').value = 0;
    document.getElementById('group_denda_kerusakan').style.display = 'none';
    
    hitungDenda();
    showModal('pengembalianModal');
}

function cekKondisi() {
    const kondisi = document.getElementById('kondisi_buku').value;
    const groupDenda = document.getElementById('group_denda_kerusakan');
    
    if (kondisi === 'rusak') {
        groupDenda.style.display = 'block';
    } else {
        groupDenda.style.display = 'none';
        document.getElementById('denda_kerusakan').value = 0;
    }
    
    hitungDenda();
}

function hitungDenda() {
    if (!currentPeminjaman) return;
    
    const tanggalKembali = new Date(document.getElementById('tanggal_kembali').value);
    const tanggalJatuhTempo = new Date(currentPeminjaman.tanggal_jatuh_tempo);
    
    let keterlambatan = 0;
    let dendaKeterlambatan = 0;
    
    if (tanggalKembali > tanggalJatuhTempo) {
        keterlambatan = Math.floor((tanggalKembali - tanggalJatuhTempo) / (1000 * 60 * 60 * 24));
        dendaKeterlambatan = keterlambatan * dendaPerHari;
    }
    
    const dendaKerusakan = parseInt(document.getElementById('denda_kerusakan').value) || 0;
    const totalDenda = dendaKeterlambatan + dendaKerusakan;
    
    if (totalDenda > 0) {
        const rincian = `
            <h4 style="margin-top: 0;">Rincian Denda:</h4>
            <table style="width: 100%;">
                ${keterlambatan > 0 ? `
                <tr>
                    <td>Keterlambatan:</td>
                    <td>${keterlambatan} hari x ${formatRupiah(dendaPerHari)}</td>
                    <td style="text-align: right;"><strong>${formatRupiah(dendaKeterlambatan)}</strong></td>
                </tr>
                ` : ''}
                ${dendaKerusakan > 0 ? `
                <tr>
                    <td>Kerusakan:</td>
                    <td>-</td>
                    <td style="text-align: right;"><strong>${formatRupiah(dendaKerusakan)}</strong></td>
                </tr>
                ` : ''}
                <tr style="border-top: 2px solid #333;">
                    <td colspan="2"><strong>Total Denda:</strong></td>
                    <td style="text-align: right;"><strong style="font-size: 18px; color: #e74c3c;">${formatRupiah(totalDenda)}</strong></td>
                </tr>
            </table>
        `;
        document.getElementById('rincian-denda').innerHTML = rincian;
        document.getElementById('rincian-denda').style.display = 'block';
    } else {
        document.getElementById('rincian-denda').style.display = 'none';
    }
}

function formatTanggal(tanggal) {
    const date = new Date(tanggal);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>

<?php include '../includes/footer.php'; ?>
