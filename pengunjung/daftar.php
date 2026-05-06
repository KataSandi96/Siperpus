<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$success = '';
$error = '';
$show_full_form = false;

// Proses Quick Register
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quick_register'])) {
    $email = escape($_POST['email']);
    $nama_lengkap = escape($_POST['nama_lengkap']);
    
    if (empty($email) || empty($nama_lengkap)) {
        $error = "Email dan nama wajib diisi!";
    } else {
        $cek_email = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM anggota WHERE email = '$email'"));
        if ($cek_email['total'] > 0) {
            $error = "Email sudah terdaftar! Silakan login.";
        } else {
            $username_base = strtolower(str_replace(' ', '', $nama_lengkap));
            $username = preg_replace('/[^a-z0-9]/', '', $username_base);
            $counter = 1;
            while (mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM users WHERE username = '$username'"))['total'] > 0) {
                $username = $username_base . $counter;
                $counter++;
            }
            
            $default_password = 'perpus' . rand(1000, 9999);
            $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
            
            $sql_user = "INSERT INTO users (username, password, role, status) 
                        VALUES ('$username', '$hashed_password', 'anggota', 'nonaktif')";
            
            if (query($sql_user)) {
                $id_user = mysqli_insert_id($conn);
                
                $sql_anggota = "INSERT INTO anggota (id_user, nama_lengkap, email, status_verifikasi, tanggal_daftar) 
                               VALUES ($id_user, '$nama_lengkap', '$email', 'menunggu', CURDATE())";
                
                if (query($sql_anggota)) {
                    $success = "Pendaftaran berhasil!||Username: <strong>$username</strong>||Password: <strong>$default_password</strong>||Harap simpan informasi ini!";
                    $_POST = array();
                } else {
                    query("DELETE FROM users WHERE id_user = $id_user");
                    $error = "Gagal mendaftar! Silakan coba lagi.";
                }
            }
        }
    }
}

// Proses Full Register
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['full_register'])) {
    $username = escape($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $email = escape($_POST['email']);
    $no_telepon = escape($_POST['no_telepon']);
    $alamat = escape($_POST['alamat']);
    
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email)) {
        $error = "Semua field yang bertanda * wajib diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        // (Logika pendaftaran lengkap)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_user = "INSERT INTO users (username, password, role, status) VALUES ('$username', '$hashed_password', 'anggota', 'nonaktif')";
        if (query($sql_user)) {
            $id_user = mysqli_insert_id($conn);
            $sql_anggota = "INSERT INTO anggota (id_user, nama_lengkap, email, no_telepon, alamat, status_verifikasi, tanggal_daftar) VALUES ($id_user, '$nama_lengkap', '$email', '$no_telepon', '$alamat', 'menunggu', CURDATE())";
            if (query($sql_anggota)) {
                $success = "Pendaftaran berhasil!||Username: <strong>$username</strong>||Silakan tunggu verifikasi pustakawan.";
                $_POST = array();
            } else {
                query("DELETE FROM users WHERE id_user = $id_user");
                $error = "Gagal mendaftar!";
            }
        }
    }
}

if (isset($_GET['full'])) {
    $show_full_form = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - SiPerpus - Universitas Merangin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* --- PERUBAHAN UTAMA DIMULAI DI SINI --- */
        
        /* Background Image Layer */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            
            /* GANTI GAMBAR DI SINI */
            background-image: url('../assets/img/register-bg.jpg');
            /* atau upload ke: url('../assets/img/register-bg.jpg'); */
            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        /* Overlay Hitam Transparan */
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: rgba(0, 0, 0, 0.6);
        }
        
        /* --- PERUBAHAN UTAMA SELESAI DI SINI --- */
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        .register-container.full-form {
            max-width: 700px;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .register-header {
            /* Header dibuat transparan agar menyatu dengan background */
            background: rgba(44, 62, 80, 0.9);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        
        .register-header h1 {
            font-size: 26px;
            margin: 0;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
        }
        
        .register-body {
            padding: 40px;
        }
        
        .quick-register-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .quick-register-info h3 {
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
            font-size: 14px;
        }
        
        input, textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #34495e;
            background: white;
            box-shadow: 0 0 0 4px rgba(44, 62, 80, 0.1);
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #2c3e50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #34495e;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(44, 62, 80, 0.3);
        }
        
        .btn-link {
            background: transparent;
            color: #34495e;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .btn-link:hover {
            background: #f8f9fa;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .form-footer {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
        }
        
        .form-footer a { color: #34495e; font-weight: 600; text-decoration: none; }
        .form-footer a:hover { text-decoration: underline; }
        
        .success-box { text-align: center; padding: 30px; }
        .success-icon {
            width: 80px; height: 80px;
            background: #27ae60;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; color: white;
            margin: 0 auto 20px; animation: scaleIn 0.5s;
        }
        @keyframes scaleIn { from { transform: scale(0); } to { transform: scale(1); } }
        
        .credentials-box {
            background: #fff3cd; border: 2px dashed #f39c12;
            padding: 20px; border-radius: 10px; margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Background Image & Overlay -->
    <div class="background-image"></div>
    <div class="background-overlay"></div>

    <div class="register-container <?php echo $show_full_form ? 'full-form' : ''; ?>">
        <div class="register-header">
            <h1>✍️<br>Daftar Anggota</h1>
        </div>
        
        <div class="register-body">
            <?php if ($success): ?>
                <?php $success_parts = explode('||', $success); ?>
                <div class="success-box">
                    <div class="success-icon">✓</div>
                    <h2 style="color: #27ae60;"><?php echo $success_parts[0]; ?></h2>
                    <?php if (count($success_parts) > 1): ?>
                        <div class="credentials-box">
                            <h3 style="color: #856404;">📝 Simpan Informasi Login Anda!</h3>
                            <?php for($i = 1; $i < count($success_parts); $i++): ?>
                                <div style="background: white; padding: 10px; margin: 8px 0; border-radius: 5px;"><?php echo $success_parts[$i]; ?></div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <p style="color: #7f8c8d; margin: 20px 0;">Akun Anda menunggu verifikasi pustakawan.</p>
                    <a href="../auth/login.php" class="btn btn-primary">🔐 Login Sekarang</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!$show_full_form): ?>
                    <!-- QUICK REGISTER -->
                    <div class="quick-register-info">
                        <h3>Daftar Cepat!</h3>
                        <p>Cukup isi email dan nama untuk jadi anggota.</p>
                    </div>
                    <form method="POST" action="">
                        <input type="hidden" name="quick_register" value="1">
                        <div class="form-group"><label>📧 Email Anda</label><input type="email" name="email" required></div>
                        <div class="form-group"><label>✍️ Nama Lengkap</label><input type="text" name="nama_lengkap" required></div>
                        <button type="submit" class="btn btn-primary">🚀 Daftar Sekarang</button>
                        <a href="?full=1" class="btn btn-link">Isi Form Lengkap</a>
                    </form>
                <?php else: ?>
                    <!-- FULL REGISTER -->
                    <form method="POST" action="">
                        <input type="hidden" name="full_register" value="1">
                        <h3>📝 Form Pendaftaran Lengkap</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group"><label>Username *</label><input type="text" name="username" required></div>
                            <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                            <div class="form-group"><label>Password *</label><input type="password" name="password" required></div>
                            <div class="form-group"><label>Konfirmasi Password *</label><input type="password" name="confirm_password" required></div>
                        </div>
                        <div class="form-group"><label>Nama Lengkap *</label><input type="text" name="nama_lengkap" required></div>
                        <div class="form-group"><label>No. Telepon</label><input type="tel" name="no_telepon"></div>
                        <div class="form-group"><label>Alamat</label><textarea name="alamat"></textarea></div>
                        <button type="submit" class="btn btn-primary">✍️ Daftar dengan Data Lengkap</button>
                        <a href="daftar.php" class="btn btn-link">Kembali ke Daftar Cepat</a>
                    </form>
                <?php endif; ?>
                
                <div class="form-footer">
                    <p>Sudah punya akun? <a href="../auth/login.php">Login di sini</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
