<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['admin']);

$page_title = 'Data Anggota';
$success = '';
$error = '';

// Proses aksi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Edit Anggota
        if ($action == 'edit') {
            $id_anggota = (int)$_POST['id_anggota'];
            $nama_lengkap = escape($_POST['nama_lengkap']);
            $email = escape($_POST['email']);
            $no_telepon = escape($_POST['no_telepon']);
            $alamat = escape($_POST['alamat']);
            
            $sql = "UPDATE anggota SET 
                    nama_lengkap = '$nama_lengkap',
                    email = '$email',
                    no_telepon = '$no_telepon',
                    alamat = '$alamat'
                    WHERE id_anggota = $id_anggota";
            
            if (query($sql)) {
                $success = "Data anggota berhasil diupdate!";
            } else {
                $error = "Gagal mengupdate data anggota!";
            }
        }
        
        // Nonaktifkan Anggota
        elseif ($action == 'nonaktifkan') {
            $id_anggota = (int)$_POST['id_anggota'];
            
            $sql = "UPDATE anggota SET status_verifikasi = 'nonaktif' WHERE id_anggota = $id_anggota";
            
            if (query($sql)) {
                // Update status user juga
                $anggota = mysqli_fetch_assoc(query("SELECT id_user FROM anggota WHERE id_anggota = $id_anggota"));
                query("UPDATE users SET status = 'nonaktif' WHERE id_user = {$anggota['id_user']}");
                
                $success = "Anggota berhasil dinonaktifkan!";
            } else {
                $error = "Gagal menonaktifkan anggota!";
            }
        }
        
        // Aktifkan Anggota
        elseif ($action == 'aktifkan') {
            $id_anggota = (int)$_POST['id_anggota'];
            
            $sql = "UPDATE anggota SET status_verifikasi = 'aktif' WHERE id_anggota = $id_anggota";
            
            if (query($sql)) {
                // Update status user juga
                $anggota = mysqli_fetch_assoc(query("SELECT id_user FROM anggota WHERE id_anggota = $id_anggota"));
                query("UPDATE users SET status = 'aktif' WHERE id_user = {$anggota['id_user']}");
                
                $success = "Anggota berhasil diaktifkan!";
            } else {
                $error = "Gagal mengaktifkan anggota!";
            }
        }
    }
}

// Ambil data anggota
$filter = isset($_GET['status']) ? escape($_GET['status']) : 'all';
$keyword = isset($_GET['keyword']) ? escape($_GET['keyword']) : '';

$where = "WHERE 1=1";
if ($filter != 'all') {
    $where .= " AND a.status_verifikasi = '$filter'";
}
if ($keyword) {
    $where .= " AND (a.nama_lengkap LIKE '%$keyword%' OR a.email LIKE '%$keyword%')";
}

$anggota_list = query("SELECT a.*, u.username, u.status as user_status 
                       FROM anggota a 
                       LEFT JOIN users u ON a.id_user = u.id_user 
                       $where 
                       ORDER BY a.tanggal_daftar DESC");

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
        <h3>👥 Kelola Data Anggota</h3>
    </div>
    <div class="card-body">
        <div class="filter-box">
            <form method="GET" action="" class="filter-form">
                <select name="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                    <option value="aktif" <?php echo $filter == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="menunggu" <?php echo $filter == 'menunggu' ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                    <option value="nonaktif" <?php echo $filter == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                </select>
                
                <input type="text" name="keyword" placeholder="Cari anggota..." value="<?php echo $keyword; ?>">
                <button type="submit" class="btn btn-secondary">🔍 Cari</button>
            </form>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($anggota_list)): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nama_lengkap']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['no_telepon']; ?></td>
                    <td><?php echo $row['alamat'] ? mb_substr($row['alamat'], 0, 30) . '...' : '-'; ?></td>

                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_daftar'])); ?></td>
                    <td>
                        <?php if ($row['status_verifikasi'] == 'aktif'): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php elseif ($row['status_verifikasi'] == 'menunggu'): ?>
                            <span class="badge badge-warning">Menunggu</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editAnggota(<?php echo json_encode($row); ?>)'>✏️ Edit</button>
                        
                        <?php if ($row['status_verifikasi'] == 'aktif'): ?>
                            <button class="btn btn-sm btn-danger" onclick="nonaktifkanAnggota(<?php echo $row['id_anggota']; ?>, '<?php echo addslashes($row['nama_lengkap']); ?>')">🚫 Nonaktifkan</button>
                        <?php elseif ($row['status_verifikasi'] == 'nonaktif'): ?>
                            <button class="btn btn-sm btn-success" onclick="aktifkanAnggota(<?php echo $row['id_anggota']; ?>, '<?php echo addslashes($row['nama_lengkap']); ?>')">✅ Aktifkan</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>Edit Data Anggota</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_anggota" id="edit_id_anggota">
            
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" id="edit_nama_lengkap" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" id="edit_email" required>
            </div>
            
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telepon" id="edit_no_telepon">
            </div>
            
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" id="edit_alamat" rows="3"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Hidden -->
<form method="POST" action="" id="statusForm">
    <input type="hidden" name="action" id="status_action">
    <input type="hidden" name="id_anggota" id="status_id_anggota">
</form>

<script>
function editAnggota(data) {
    document.getElementById('edit_id_anggota').value = data.id_anggota;
    document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_no_telepon').value = data.no_telepon;
    document.getElementById('edit_alamat').value = data.alamat;
    showModal('editModal');
}

function nonaktifkanAnggota(id, nama) {
    if (confirm('Apakah Anda yakin ingin menonaktifkan anggota "' + nama + '"?')) {
        document.getElementById('status_action').value = 'nonaktifkan';
        document.getElementById('status_id_anggota').value = id;
        document.getElementById('statusForm').submit();
    }
}

function aktifkanAnggota(id, nama) {
    if (confirm('Apakah Anda yakin ingin mengaktifkan anggota "' + nama + '"?')) {
        document.getElementById('status_action').value = 'aktifkan';
        document.getElementById('status_id_anggota').value = id;
        document.getElementById('statusForm').submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
