<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['admin']);

$page_title = 'Pengaturan Sistem';
$success = '';
$error = '';

// Proses update pengaturan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $durasi_peminjaman = (int)$_POST['durasi_peminjaman'];
    $batas_peminjaman = (int)$_POST['batas_peminjaman'];
    $denda_per_hari = (int)$_POST['denda_per_hari'];
    $email_notifikasi = escape($_POST['email_notifikasi']);
    
    // Validasi
    if ($durasi_peminjaman < 1 || $batas_peminjaman < 1 || $denda_per_hari < 0) {
        $error = "Nilai parameter tidak valid!";
    } else {
        // Update pengaturan
        query("UPDATE pengaturan_sistem SET nilai_parameter = '$durasi_peminjaman' WHERE nama_parameter = 'durasi_peminjaman'");
        query("UPDATE pengaturan_sistem SET nilai_parameter = '$batas_peminjaman' WHERE nama_parameter = 'batas_peminjaman'");
        query("UPDATE pengaturan_sistem SET nilai_parameter = '$denda_per_hari' WHERE nama_parameter = 'denda_per_hari'");
        query("UPDATE pengaturan_sistem SET nilai_parameter = '$email_notifikasi' WHERE nama_parameter = 'email_notifikasi'");
        
        $success = "Pengaturan sistem berhasil diupdate!";
    }
}

// Ambil pengaturan
$pengaturan = [];
$result = query("SELECT * FROM pengaturan_sistem");
while ($row = mysqli_fetch_assoc($result)) {
    $pengaturan[$row['nama_parameter']] = $row['nilai_parameter'];
}

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
        <h3>⚙️ Pengaturan Sistem Perpustakaan</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-group">
                <label>Durasi Peminjaman (hari) *</label>
                <input type="number" name="durasi_peminjaman" class="form-control" 
                       value="<?php echo $pengaturan['durasi_peminjaman']; ?>" 
                       min="1" required>
                <small class="form-text">Lama waktu peminjaman buku dalam hari</small>
            </div>
            
            <div class="form-group">
                <label>Batas Maksimal Peminjaman *</label>
                <input type="number" name="batas_peminjaman" class="form-control" 
                       value="<?php echo $pengaturan['batas_peminjaman']; ?>" 
                       min="1" required>
                <small class="form-text">Jumlah maksimal buku yang dapat dipinjam per anggota</small>
            </div>
            
            <div class="form-group">
                <label>Denda Per Hari (Rp) *</label>
                <input type="number" name="denda_per_hari" class="form-control" 
                       value="<?php echo $pengaturan['denda_per_hari']; ?>" 
                       min="0" required>
                <small class="form-text">Besaran denda keterlambatan per hari</small>
            </div>
            
            <div class="form-group">
                <label>Email Notifikasi *</label>
                <input type="email" name="email_notifikasi" class="form-control" 
                       value="<?php echo $pengaturan['email_notifikasi']; ?>" required>
                <small class="form-text">Email untuk notifikasi sistem</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
                <button type="reset" class="btn btn-secondary">🔄 Reset</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>ℹ️ Informasi Sistem</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Nama Sistem</th>
                <td>Sistem Informasi Perpustakaan SDN259 Bukit Subur</td>
            </tr>
            <tr>
                <th>Versi</th>
                <td>1.0.0</td>
            </tr>
            <tr>
                <th>Database</th>
                <td><?php echo DB_NAME; ?></td>
            </tr>
            <tr>
                <th>Server</th>
                <td><?php echo $_SERVER['SERVER_NAME']; ?></td>
            </tr>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
