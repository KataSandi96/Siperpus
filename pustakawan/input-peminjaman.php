<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['pustakawan']);

$page_title = 'Input Peminjaman';
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

// Proses input peminjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = (int)$_POST['id_anggota'];
    $id_buku = (int)$_POST['id_buku'];
    $id_pemesanan = isset($_POST['id_pemesanan']) ? (int)$_POST['id_pemesanan'] : null;
    $tanggal_pinjam = escape($_POST['tanggal_pinjam']);
    
    // Validasi anggota
    $anggota = mysqli_fetch_assoc(query("SELECT * FROM anggota WHERE id_anggota = $id_anggota"));
    if (!$anggota || $anggota['status_verifikasi'] != 'aktif') {
        $error = "Anggota tidak valid atau belum diverifikasi!";
    } else {
        // Cek tunggakan denda
        $tunggakan = mysqli_fetch_assoc(query("SELECT SUM(total_denda) as total FROM pengembalian WHERE id_peminjaman IN (SELECT id_peminjaman FROM peminjaman WHERE id_anggota = $id_anggota) AND status_pembayaran = 'belum_lunas'"))['total'];
        
        if ($tunggakan > 0) {
            $error = "Anggota memiliki tunggakan denda sebesar " . formatRupiah($tunggakan) . ". Selesaikan pembayaran terlebih dahulu!";
        } else {
            // Cek batas peminjaman
            $total_pinjam = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE id_anggota = $id_anggota AND status = 'dipinjam'"))['total'];
            
            if ($total_pinjam >= $pengaturan['batas_peminjaman']) {
                $error = "Anggota sudah mencapai batas maksimal peminjaman ({$pengaturan['batas_peminjaman']} buku)!";
            } else {
                // Cek ketersediaan buku
                $buku = mysqli_fetch_assoc(query("SELECT * FROM buku WHERE id_buku = $id_buku"));
                
                if ($buku['jumlah_tersedia'] < 1) {
                    $error = "Buku tidak tersedia untuk dipinjam!";
                } else {
                    // Hitung tanggal jatuh tempo
                    $tanggal_jatuh_tempo = date('Y-m-d', strtotime($tanggal_pinjam . ' + ' . $pengaturan['durasi_peminjaman'] . ' days'));
                    
                    // Insert peminjaman
                    $sql = "INSERT INTO peminjaman (id_pemesanan, id_anggota, id_buku, tanggal_pinjam, tanggal_jatuh_tempo, id_pustakawan, status) 
                            VALUES (" . ($id_pemesanan ? $id_pemesanan : "NULL") . ", $id_anggota, $id_buku, '$tanggal_pinjam', '$tanggal_jatuh_tempo', $id_pustakawan, 'dipinjam')";
                    
                    if (query($sql)) {
                        // Update stok buku
                        query("UPDATE buku SET jumlah_tersedia = jumlah_tersedia - 1 WHERE id_buku = $id_buku");
                        
                        // Kirim notifikasi
                        $anggota_user = mysqli_fetch_assoc(query("SELECT id_user FROM anggota WHERE id_anggota = $id_anggota"));
                        $notif_sql = "INSERT INTO notifikasi (id_user, judul, pesan) 
                                     VALUES ({$anggota_user['id_user']}, 'Peminjaman Berhasil', 
                                     'Peminjaman buku \"{$buku['judul']}\" berhasil. Jatuh tempo: " . formatTanggal($tanggal_jatuh_tempo) . "')";
                        query($notif_sql);
                        
                        $success = "Peminjaman berhasil dicatat! Jatuh tempo: " . formatTanggal($tanggal_jatuh_tempo);
                    } else {
                        $error = "Gagal mencatat peminjaman!";
                    }
                }
            }
        }
    }
}

// Ambil pemesanan yang disetujui
$pemesanan_disetujui = query("SELECT p.*, a.nama_lengkap, b.judul 
                              FROM pemesanan p 
                              JOIN anggota a ON p.id_anggota = a.id_anggota 
                              JOIN buku b ON p.id_buku = b.id_buku 
                              WHERE p.status_pemesanan = 'disetujui' 
                              ORDER BY p.tanggal_verifikasi DESC");

include '../includes/header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>📖 Input Peminjaman Baru</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Cari Anggota *</label>
                        <input type="text" id="search_anggota" placeholder="Ketik nama atau email anggota..." onkeyup="cariAnggota()">
                        <div id="result_anggota" style="border: 1px solid #ddd; max-height: 200px; overflow-y: auto; display: none;"></div>
                        <input type="hidden" name="id_anggota" id="id_anggota" required>
                        <small class="form-text" id="nama_anggota"></small>
                    </div>
                    
                    <div class="form-group">
                        <label>Cari Buku *</label>
                        <input type="text" id="search_buku" placeholder="Ketik judul atau pengarang buku..." onkeyup="cariBuku()">
                        <div id="result_buku" style="border: 1px solid #ddd; max-height: 200px; overflow-y: auto; display: none;"></div>
                        <input type="hidden" name="id_buku" id="id_buku" required>
                        <small class="form-text" id="nama_buku"></small>
                    </div>
                    
                    <div class="form-group">
                        <label>Tanggal Peminjaman *</label>
                        <input type="date" name="tanggal_pinjam" value="<?php echo date('Y-m-d'); ?>" required>
                        <small class="form-text">Durasi: <?php echo $pengaturan['durasi_peminjaman']; ?> hari</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Simpan Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>✅ Pemesanan Disetujui</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($pemesanan_disetujui) > 0):
                            while($row = mysqli_fetch_assoc($pemesanan_disetujui)): 
                        ?>
                        <tr>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo substr($row['judul'], 0, 25); ?>...</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="prosesFromPemesanan(<?php echo $row['id_pemesanan']; ?>, <?php echo $row['id_anggota']; ?>, <?php echo $row['id_buku']; ?>, '<?php echo addslashes($row['nama_lengkap']); ?>', '<?php echo addslashes($row['judul']); ?>')">
                                    Proses
                                </button>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #7f8c8d;">Tidak ada pemesanan disetujui</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function cariAnggota() {
    const keyword = document.getElementById('search_anggota').value;
    const resultDiv = document.getElementById('result_anggota');
    
    if (keyword.length < 2) {
        resultDiv.style.display = 'none';
        return;
    }
    
    fetch('../api/cari-anggota.php?keyword=' + encodeURIComponent(keyword))
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '<ul style="list-style: none; margin: 0; padding: 0;">';
                data.forEach(item => {
                    html += `<li style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;" onclick="pilihAnggota(${item.id_anggota}, '${item.nama_lengkap}')">${item.nama_lengkap} - ${item.email}</li>`;
                });
                html += '</ul>';
                resultDiv.innerHTML = html;
                resultDiv.style.display = 'block';
            } else {
                resultDiv.innerHTML = '<p style="padding: 10px; color: #999;">Anggota tidak ditemukan</p>';
                resultDiv.style.display = 'block';
            }
        });
}

function pilihAnggota(id, nama) {
    document.getElementById('id_anggota').value = id;
    document.getElementById('search_anggota').value = nama;
    document.getElementById('nama_anggota').textContent = 'Terpilih: ' + nama;
    document.getElementById('result_anggota').style.display = 'none';
}

function cariBuku() {
    const keyword = document.getElementById('search_buku').value;
    const resultDiv = document.getElementById('result_buku');
    
    if (keyword.length < 2) {
        resultDiv.style.display = 'none';
        return;
    }
    
    fetch('../api/cari-buku-tersedia.php?keyword=' + encodeURIComponent(keyword))
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '<ul style="list-style: none; margin: 0; padding: 0;">';
                data.forEach(item => {
                    html += `<li style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;" onclick="pilihBuku(${item.id_buku}, '${item.judul}')">${item.judul} - ${item.pengarang} (${item.jumlah_tersedia} tersedia)</li>`;
                });
                html += '</ul>';
                resultDiv.innerHTML = html;
                resultDiv.style.display = 'block';
            } else {
                resultDiv.innerHTML = '<p style="padding: 10px; color: #999;">Buku tidak ditemukan</p>';
                resultDiv.style.display = 'block';
            }
        });
}

function pilihBuku(id, judul) {
    document.getElementById('id_buku').value = id;
    document.getElementById('search_buku').value = judul;
    document.getElementById('nama_buku').textContent = 'Terpilih: ' + judul;
    document.getElementById('result_buku').style.display = 'none';
}

function prosesFromPemesanan(id_pemesanan, id_anggota, id_buku, nama_anggota, judul_buku) {
    document.getElementById('id_anggota').value = id_anggota;
    document.getElementById('id_buku').value = id_buku;
    document.getElementById('search_anggota').value = nama_anggota;
    document.getElementById('search_buku').value = judul_buku;
    document.getElementById('nama_anggota').textContent = 'Terpilih: ' + nama_anggota;
    document.getElementById('nama_buku').textContent = 'Terpilih: ' + judul_buku;
    
    // Tambahkan hidden input untuk id_pemesanan
    let pemesananInput = document.createElement('input');
    pemesananInput.type = 'hidden';
    pemesananInput.name = 'id_pemesanan';
    pemesananInput.value = id_pemesanan;
    document.querySelector('form').appendChild(pemesananInput);
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include '../includes/footer.php'; ?>
