<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['pustakawan']);

$page_title = 'Verifikasi Pemesanan';
$success = '';
$error = '';

// Ambil ID pustakawan
$karyawan = mysqli_fetch_assoc(query("SELECT id_karyawan FROM karyawan WHERE id_user = {$_SESSION['user_id']}"));
$id_pustakawan = $karyawan['id_karyawan'];

// Proses verifikasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pemesanan = (int)$_POST['id_pemesanan'];
    $action = $_POST['action'];
    
    if ($action == 'setujui') {
        // Update status pemesanan
        $sql = "UPDATE pemesanan SET 
                status_pemesanan = 'disetujui',
                id_pustakawan = $id_pustakawan,
                tanggal_verifikasi = NOW()
                WHERE id_pemesanan = $id_pemesanan";
        
        if (query($sql)) {
            // Ambil data pemesanan untuk notifikasi
            $pemesanan = mysqli_fetch_assoc(query("SELECT * FROM pemesanan WHERE id_pemesanan = $id_pemesanan"));
            
            // Kirim notifikasi ke anggota
            $anggota = mysqli_fetch_assoc(query("SELECT id_user FROM anggota WHERE id_anggota = {$pemesanan['id_anggota']}"));
            $notif_sql = "INSERT INTO notifikasi (id_user, judul, pesan) 
                         VALUES ({$anggota['id_user']}, 'Pemesanan Disetujui', 
                         'Pemesanan buku Anda telah disetujui. Silakan datang ke perpustakaan untuk meminjam buku.')";
            query($notif_sql);
            
            $success = "Pemesanan berhasil disetujui!";
        } else {
            $error = "Gagal menyetujui pemesanan!";
        }
    }
    
    elseif ($action == 'tolak') {
        $alasan = escape($_POST['alasan_penolakan']);
        
        if (empty($alasan)) {
            $error = "Alasan penolakan harus diisi!";
        } else {
            // Update status pemesanan
            $sql = "UPDATE pemesanan SET 
                    status_pemesanan = 'ditolak',
                    alasan_penolakan = '$alasan',
                    id_pustakawan = $id_pustakawan,
                    tanggal_verifikasi = NOW()
                    WHERE id_pemesanan = $id_pemesanan";
            
            if (query($sql)) {
                // Kirim notifikasi ke anggota
                $pemesanan = mysqli_fetch_assoc(query("SELECT * FROM pemesanan WHERE id_pemesanan = $id_pemesanan"));
                $anggota = mysqli_fetch_assoc(query("SELECT id_user FROM anggota WHERE id_anggota = {$pemesanan['id_anggota']}"));
                
                $notif_sql = "INSERT INTO notifikasi (id_user, judul, pesan) 
                             VALUES ({$anggota['id_user']}, 'Pemesanan Ditolak', 
                             'Pemesanan buku Anda ditolak. Alasan: $alasan')";
                query($notif_sql);
                
                $success = "Pemesanan berhasil ditolak!";
            } else {
                $error = "Gagal menolak pemesanan!";
            }
        }
    }
}

// Ambil data pemesanan menunggu
$pemesanan_list = query("SELECT p.*, a.nama_lengkap, a.email, a.no_telepon, 
                         b.judul, b.pengarang, b.jumlah_tersedia 
                         FROM pemesanan p 
                         JOIN anggota a ON p.id_anggota = a.id_anggota 
                         JOIN buku b ON p.id_buku = b.id_buku 
                         WHERE p.status_pemesanan = 'menunggu' 
                         ORDER BY p.tanggal_pesan ASC");

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
        <h3>✅ Verifikasi Pemesanan Buku</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pesan</th>
                    <th>Nama Anggota</th>
                    <th>Email/No. HP</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Stok</th>
                    <th>Aksi</th>
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
                    <td><?php echo $row['nama_lengkap']; ?></td>
                    <td><?php echo $row['email']; ?><br><?php echo $row['no_telepon']; ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td>
                        <?php if ($row['jumlah_tersedia'] > 0): ?>
                            <span class="badge badge-success"><?php echo $row['jumlah_tersedia']; ?> tersedia</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Habis</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="setujuiPemesanan(<?php echo $row['id_pemesanan']; ?>)">✅ Setujui</button>
                        <button class="btn btn-sm btn-danger" onclick="tolakPemesanan(<?php echo $row['id_pemesanan']; ?>)">❌ Tolak</button>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #7f8c8d; padding: 30px;">
                        Tidak ada pemesanan yang menunggu verifikasi
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Form Setujui -->
<form method="POST" action="" id="formSetujui">
    <input type="hidden" name="action" value="setujui">
    <input type="hidden" name="id_pemesanan" id="setujui_id">
</form>

<!-- Modal Tolak -->
<div id="tolakModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('tolakModal')">&times;</span>
        <h2>Tolak Pemesanan</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="tolak">
            <input type="hidden" name="id_pemesanan" id="tolak_id">
            
            <div class="form-group">
                <label>Alasan Penolakan *</label>
                <textarea name="alasan_penolakan" rows="4" required placeholder="Masukkan alasan penolakan pemesanan..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">❌ Tolak Pemesanan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('tolakModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function setujuiPemesanan(id) {
    if (confirm('Apakah Anda yakin ingin menyetujui pemesanan ini?')) {
        document.getElementById('setujui_id').value = id;
        document.getElementById('formSetujui').submit();
    }
}

function tolakPemesanan(id) {
    document.getElementById('tolak_id').value = id;
    showModal('tolakModal');
}
</script>

<?php include '../includes/footer.php'; ?>
