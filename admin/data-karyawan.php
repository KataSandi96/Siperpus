<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['admin']);

$page_title = 'Data Karyawan';
$success = '';
$error = '';

// Proses aksi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Tambah Karyawan
        if ($action == 'add') {
            $username = escape($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = escape($_POST['role']);
            $nama_lengkap = escape($_POST['nama_lengkap']);
            $email = escape($_POST['email']);
            $jabatan = escape($_POST['jabatan']);
            $no_telepon = escape($_POST['no_telepon']);
            
            // Cek username sudah ada
            $cek = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM users WHERE username = '$username'"));
            if ($cek['total'] > 0) {
                $error = "Username sudah digunakan!";
            } else {
                // Insert user
                $sql_user = "INSERT INTO users (username, password, role, status) 
                            VALUES ('$username', '$password', '$role', 'aktif')";
                
                if (query($sql_user)) {
                    $id_user = mysqli_insert_id($conn);
                    
                    // Insert karyawan
                    $sql_karyawan = "INSERT INTO karyawan (id_user, nama_lengkap, email, jabatan, no_telepon) 
                                    VALUES ($id_user, '$nama_lengkap', '$email', '$jabatan', '$no_telepon')";
                    
                    if (query($sql_karyawan)) {
                        $success = "Karyawan berhasil ditambahkan!";
                    } else {
                        $error = "Gagal menambahkan data karyawan!";
                    }
                } else {
                    $error = "Gagal membuat user!";
                }
            }
        }
        
        // Edit Karyawan
        elseif ($action == 'edit') {
            $id_karyawan = (int)$_POST['id_karyawan'];
            $nama_lengkap = escape($_POST['nama_lengkap']);
            $email = escape($_POST['email']);
            $jabatan = escape($_POST['jabatan']);
            $no_telepon = escape($_POST['no_telepon']);
            
            $sql = "UPDATE karyawan SET 
                    nama_lengkap = '$nama_lengkap',
                    email = '$email',
                    jabatan = '$jabatan',
                    no_telepon = '$no_telepon'
                    WHERE id_karyawan = $id_karyawan";
            
            if (query($sql)) {
                $success = "Data karyawan berhasil diupdate!";
            } else {
                $error = "Gagal mengupdate data karyawan!";
            }
        }
        
        // Hapus Karyawan
        elseif ($action == 'delete') {
            $id_karyawan = (int)$_POST['id_karyawan'];
            
            // Ambil id_user
            $karyawan = mysqli_fetch_assoc(query("SELECT id_user FROM karyawan WHERE id_karyawan = $id_karyawan"));
            
            // Hapus karyawan
            $sql = "DELETE FROM karyawan WHERE id_karyawan = $id_karyawan";
            if (query($sql)) {
                // Hapus user
                query("DELETE FROM users WHERE id_user = {$karyawan['id_user']}");
                $success = "Karyawan berhasil dihapus!";
            } else {
                $error = "Gagal menghapus karyawan!";
            }
        }
    }
}

// Ambil data karyawan
$karyawan_list = query("SELECT k.*, u.username, u.role, u.status 
                        FROM karyawan k 
                        JOIN users u ON k.id_user = u.id_user 
                        ORDER BY k.nama_lengkap ASC");

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
        <div class="header-flex">
            <h3>👨‍💼 Kelola Data Karyawan</h3>
            <button class="btn btn-primary" onclick="showModal('addModal')">+ Tambah Karyawan</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>No. Telepon</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($karyawan_list)): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nama_lengkap']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['jabatan']; ?></td>
                    <td><?php echo $row['no_telepon']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><span class="badge badge-info"><?php echo ucfirst($row['role']); ?></span></td>
                    <td>
                        <?php if ($row['status'] == 'aktif'): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editKaryawan(<?php echo json_encode($row); ?>)'>✏️ Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteKaryawan(<?php echo $row['id_karyawan']; ?>, '<?php echo addslashes($row['nama_lengkap']); ?>')">🗑️ Hapus</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h2>Tambah Karyawan Baru</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="pustakawan">Pustakawan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan">
            </div>
            
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telepon">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>Edit Data Karyawan</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_karyawan" id="edit_id_karyawan">
            
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" id="edit_nama_lengkap" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" id="edit_email" required>
            </div>
            
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" id="edit_jabatan">
            </div>
            
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telepon" id="edit_no_telepon">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete -->
<form method="POST" action="" id="deleteForm">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_karyawan" id="delete_id_karyawan">
</form>

<script>
function editKaryawan(data) {
    document.getElementById('edit_id_karyawan').value = data.id_karyawan;
    document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_jabatan').value = data.jabatan;
    document.getElementById('edit_no_telepon').value = data.no_telepon;
    showModal('editModal');
}

function deleteKaryawan(id, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus karyawan "' + nama + '"?')) {
        document.getElementById('delete_id_karyawan').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
