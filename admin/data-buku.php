<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

checkRole(['admin']);

$page_title = 'Data Buku';
$success = '';
$error = '';

// Proses tambah/edit/hapus
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        // Tambah Buku
        if ($action == 'add') {
            $judul = escape($_POST['judul']);
            $pengarang = escape($_POST['pengarang']);
            $penerbit = escape($_POST['penerbit']);
            $isbn = escape($_POST['isbn']);
            $kategori = escape($_POST['kategori']);
            $tahun_terbit = escape($_POST['tahun_terbit']);
            $jumlah = (int)$_POST['jumlah_total'];
            
            $sql = "INSERT INTO buku (judul, pengarang, penerbit, isbn, kategori, tahun_terbit, jumlah_total, jumlah_tersedia) 
                    VALUES ('$judul', '$pengarang', '$penerbit', '$isbn', '$kategori', '$tahun_terbit', $jumlah, $jumlah)";
            
            if (query($sql)) {
                $success = "Buku berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan buku!";
            }
        }
        
        // Edit Buku
        elseif ($action == 'edit') {
            $id_buku = (int)$_POST['id_buku'];
            $judul = escape($_POST['judul']);
            $pengarang = escape($_POST['pengarang']);
            $penerbit = escape($_POST['penerbit']);
            $isbn = escape($_POST['isbn']);
            $kategori = escape($_POST['kategori']);
            $tahun_terbit = escape($_POST['tahun_terbit']);
            $jumlah_total = (int)$_POST['jumlah_total'];
            
            // Hitung jumlah tersedia
            $buku_lama = mysqli_fetch_assoc(query("SELECT * FROM buku WHERE id_buku = $id_buku"));
            $selisih = $jumlah_total - $buku_lama['jumlah_total'];
            $jumlah_tersedia = $buku_lama['jumlah_tersedia'] + $selisih;
            
            $sql = "UPDATE buku SET 
                    judul = '$judul',
                    pengarang = '$pengarang',
                    penerbit = '$penerbit',
                    isbn = '$isbn',
                    kategori = '$kategori',
                    tahun_terbit = '$tahun_terbit',
                    jumlah_total = $jumlah_total,
                    jumlah_tersedia = $jumlah_tersedia
                    WHERE id_buku = $id_buku";
            
            if (query($sql)) {
                $success = "Buku berhasil diupdate!";
            } else {
                $error = "Gagal mengupdate buku!";
            }
        }
        
        // Hapus Buku
        elseif ($action == 'delete') {
            $id_buku = (int)$_POST['id_buku'];
            
            // Cek apakah buku sedang dipinjam
            $cek = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM peminjaman WHERE id_buku = $id_buku AND status = 'dipinjam'"));
            
            if ($cek['total'] > 0) {
                $error = "Buku tidak bisa dihapus karena sedang dipinjam!";
            } else {
                $sql = "DELETE FROM buku WHERE id_buku = $id_buku";
                if (query($sql)) {
                    $success = "Buku berhasil dihapus!";
                } else {
                    $error = "Gagal menghapus buku!";
                }
            }
        }
    }
}

// Ambil data buku
$keyword = isset($_GET['keyword']) ? escape($_GET['keyword']) : '';
$where = $keyword ? "WHERE judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%' OR kategori LIKE '%$keyword%'" : '';
$buku_list = query("SELECT * FROM buku $where ORDER BY judul ASC");

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
            <h3>📚 Kelola Data Buku</h3>
            <button class="btn btn-primary" onclick="showModal('addModal')">+ Tambah Buku</button>
        </div>
    </div>
    <div class="card-body">
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="keyword" placeholder="Cari buku..." value="<?php echo $keyword; ?>">
                <button type="submit" class="btn btn-secondary">🔍 Cari</button>
            </form>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>ISBN</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while($row = mysqli_fetch_assoc($buku_list)): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td><?php echo $row['penerbit']; ?></td>
                    <td><?php echo $row['isbn']; ?></td>
                    <td><?php echo $row['kategori']; ?></td>
                    <td><?php echo $row['tahun_terbit']; ?></td>
                    <td><?php echo $row['jumlah_tersedia'] . '/' . $row['jumlah_total']; ?></td>
                    <td>
                        <?php if ($row['jumlah_tersedia'] > 0): ?>
                            <span class="badge badge-success">Tersedia</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Dipinjam</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick='editBuku(<?php echo json_encode($row); ?>)'>✏️ Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteBuku(<?php echo $row['id_buku']; ?>, '<?php echo addslashes($row['judul']); ?>')">🗑️ Hapus</button>
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
        <h2>Tambah Buku Baru</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label>Judul Buku *</label>
                <input type="text" name="judul" required>
            </div>
            
            <div class="form-group">
                <label>Pengarang *</label>
                <input type="text" name="pengarang" required>
            </div>
            
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit">
            </div>
            
            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn">
            </div>
            
            <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Fiksi">Fiksi</option>
                    <option value="Non-Fiksi">Non-Fiksi</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Sains">Sains</option>
                    <option value="Sejarah">Sejarah</option>
                    <option value="Agama">Agama</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" min="1900" max="<?php echo date('Y'); ?>">
            </div>
            
            <div class="form-group">
                <label>Jumlah Buku *</label>
                <input type="number" name="jumlah_total" min="1" required>
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
        <h2>Edit Buku</h2>
        <form method="POST" action="" id="editForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_buku" id="edit_id_buku">
            
            <div class="form-group">
                <label>Judul Buku *</label>
                <input type="text" name="judul" id="edit_judul" required>
            </div>
            
            <div class="form-group">
                <label>Pengarang *</label>
                <input type="text" name="pengarang" id="edit_pengarang" required>
            </div>
            
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" id="edit_penerbit">
            </div>
            
            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" id="edit_isbn">
            </div>
            
            <div class="form-group">
                <label>Kategori *</label>
                <select name="kategori" id="edit_kategori" required>
                    <option value="Fiksi">Fiksi</option>
                    <option value="Non-Fiksi">Non-Fiksi</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Sains">Sains</option>
                    <option value="Sejarah">Sejarah</option>
                    <option value="Agama">Agama</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun_terbit" id="edit_tahun_terbit" min="1900" max="<?php echo date('Y'); ?>">
            </div>
            
            <div class="form-group">
                <label>Jumlah Total *</label>
                <input type="number" name="jumlah_total" id="edit_jumlah_total" min="1" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete (Hidden) -->
<form method="POST" action="" id="deleteForm">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id_buku" id="delete_id_buku">
</form>

<script>
function editBuku(data) {
    document.getElementById('edit_id_buku').value = data.id_buku;
    document.getElementById('edit_judul').value = data.judul;
    document.getElementById('edit_pengarang').value = data.pengarang;
    document.getElementById('edit_penerbit').value = data.penerbit;
    document.getElementById('edit_isbn').value = data.isbn;
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_tahun_terbit').value = data.tahun_terbit;
    document.getElementById('edit_jumlah_total').value = data.jumlah_total;
    showModal('editModal');
}

function deleteBuku(id, judul) {
    if (confirm('Apakah Anda yakin ingin menghapus buku "' + judul + '"?')) {
        document.getElementById('delete_id_buku').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
