<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$success = '';
$error = '';

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $email = escape($_POST['email']);
    $no_telepon = escape($_POST['no_telepon']);
    $alamat = escape($_POST['alamat']);
    
    // Validasi
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email)) {
        $error = "Semua field yang bertanda * wajib diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek username sudah ada
        $cek_username = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM users WHERE username = '$username'"));
        if ($cek_username['total'] > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Cek email sudah ada
            $cek_email = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM anggota WHERE email = '$email'"));
            if ($cek_email['total'] > 0) {
                $error = "Email sudah terdaftar!";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $sql_user = "INSERT INTO users (username, password, role, status) 
                            VALUES ('$username', '$hashed_password', 'anggota', 'nonaktif')";
                
                if (query($sql_user)) {
                    $id_user = mysqli_insert_id($conn);
                    
                    // Insert anggota
                    $sql_anggota = "INSERT INTO anggota (id_user, nama_lengkap, email, no_telepon, alamat, status_verifikasi, tanggal_daftar) 
                                   VALUES ($id_user, '$nama_lengkap', '$email', '$no_telepon', '$alamat', 'menunggu', CURDATE())";
                    
                    if (query($sql_anggota)) {
                        $success = "Pendaftaran berhasil! Silakan tunggu verifikasi dari pustakawan. Anda akan dihubungi melalui email.";
                        $_POST = array();
                    } else {
                        query("DELETE FROM users WHERE id_user = $id_user");
                        $error = "Gagal mendaftar! Silakan coba lagi.";
                    }
                } else {
                    $error = "Gagal membuat akun! Silakan coba lagi.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Perpustakaan SDN259 Bukit Subur</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .register-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .register-header .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .register-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step {
            position: relative;
            text-align: center;
            flex: 1;
            z-index: 1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            background: white;
            border: 3px solid #e0e0e0;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .step.active .step-circle {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }
        
        .step.completed .step-circle {
            background: #27ae60;
            border-color: #27ae60;
            color: white;
        }
        
        .step-label {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title .icon {
            font-size: 24px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
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
        
        .form-group label .required {
            color: #e74c3c;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 18px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-group input.error,
        .form-group textarea.error {
            border-color: #e74c3c;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
            display: block;
        }
        
        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }
        
        .password-strength-bar.weak {
            width: 33%;
            background: #e74c3c;
        }
        
        .password-strength-bar.medium {
            width: 66%;
            background: #f39c12;
        }
        
        .password-strength-bar.strong {
            width: 100%;
            background: #27ae60;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert .icon {
            font-size: 24px;
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
            justify-content: center;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .form-footer {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #ecf0f1;
            text-align: center;
        }
        
        .form-footer p {
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin: 0 10px;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .success-container {
            text-align: center;
            padding: 40px;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .success-container h2 {
            color: #27ae60;
            margin-bottom: 15px;
        }
        
        .success-container p {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .register-header {
                padding: 30px 20px;
            }
            
            .register-body {
                padding: 30px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .progress-steps {
                margin-bottom: 30px;
            }
            
            .step-label {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-container">
            <div class="register-header">
                <div class="logo">📚</div>
                <h1>Pendaftaran Anggota Baru</h1>
                <p>Perpustakaan SDN259 Bukit Subur</p>
            </div>
            
            <div class="register-body">
                <?php if ($success): ?>
                    <div class="success-container">
                        <div class="success-icon">✓</div>
                        <h2>Pendaftaran Berhasil!</h2>
                        <p>
                            Terima kasih telah mendaftar sebagai anggota perpustakaan.<br>
                            Akun Anda saat ini menunggu verifikasi dari pustakawan.<br>
                            Anda akan dihubungi melalui email setelah akun diverifikasi.
                        </p>
                        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <a href="../auth/login.php" class="btn btn-primary">🔐 Login</a>
                            <a href="../index.php" class="btn btn-secondary">🏠 Kembali ke Beranda</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <span class="icon">⚠️</span>
                            <span><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="progress-steps">
                        <div class="step active">
                            <div class="step-circle">1</div>
                            <div class="step-label">Akun</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">2</div>
                            <div class="step-label">Data Pribadi</div>
                        </div>
                        <div class="step">
                            <div class="step-circle">3</div>
                            <div class="step-label">Selesai</div>
                        </div>
                    </div>
                    
                    <form method="POST" action="" id="registerForm">
                        <div class="form-section">
                            <div class="section-title">
                                <span class="icon">🔐</span>
                                <span>Informasi Akun</span>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Username <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <span class="input-icon">👤</span>
                                        <input type="text" name="username" id="username" required 
                                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                               placeholder="Pilih username untuk login">
                                    </div>
                                    <small class="form-text">Username akan digunakan untuk login</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Email <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <span class="input-icon">📧</span>
                                        <input type="email" name="email" id="email" required 
                                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                               placeholder="contoh@email.com">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Password <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <span class="input-icon">🔒</span>
                                        <input type="password" name="password" id="password" required 
                                               placeholder="Minimal 6 karakter" onkeyup="checkPasswordStrength()">
                                    </div>
                                    <div class="password-strength">
                                        <div class="password-strength-bar" id="strengthBar"></div>
                                    </div>
                                    <small class="form-text" id="strengthText">Masukkan password yang kuat</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Konfirmasi Password <span class="required">*</span></label>
                                    <div class="input-wrapper">
                                        <span class="input-icon">🔒</span>
                                        <input type="password" name="confirm_password" id="confirm_password" required 
                                               placeholder="Ulangi password" onkeyup="checkPasswordMatch()">
                                    </div>
                                    <small class="form-text" id="matchText"></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <div class="section-title">
                                <span class="icon">👨‍🎓</span>
                                <span>Data Pribadi</span>
                            </div>
                            
                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">✍️</span>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" required 
                                           value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>"
                                           placeholder="Masukkan nama lengkap sesuai identitas">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>No. Telepon</label>
                                <div class="input-wrapper">
                                    <span class="input-icon">📱</span>
                                    <input type="tel" name="no_telepon" id="no_telepon" 
                                           value="<?php echo isset($_POST['no_telepon']) ? htmlspecialchars($_POST['no_telepon']) : ''; ?>"
                                           placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <div class="input-wrapper">
                                    <span class="input-icon" style="top: 20px;">🏠</span>
                                    <textarea name="alamat" id="alamat" 
                                              placeholder="Masukkan alamat lengkap"><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <span>✍️</span>
                            <span>Daftar Sekarang</span>
                        </button>
                    </form>
                    
                    <div class="form-footer">
                        <p>Sudah punya akun? <a href="../auth/login.php">Login di sini</a></p>
                        <div class="back-home">
                            <a href="../index.php">🏠 Kembali ke Beranda</a>
                            <span style="color: #e0e0e0;">•</span>
                            <a href="cari-buku.php">📚 Lihat Katalog Buku</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Password strength checker
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Password lemah';
                strengthText.style.color = '#e74c3c';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Password sedang';
                strengthText.style.color = '#f39c12';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Password kuat';
                strengthText.style.color = '#27ae60';
            }
        }
        
        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('matchText');
            
            if (confirm.length > 0) {
                if (password === confirm) {
                    matchText.textContent = '✓ Password cocok';
                    matchText.style.color = '#27ae60';
                    document.getElementById('confirm_password').classList.remove('error');
                } else {
                    matchText.textContent = '✗ Password tidak cocok';
                    matchText.style.color = '#e74c3c';
                    document.getElementById('confirm_password').classList.add('error');
                }
            } else {
                matchText.textContent = '';
            }
        }
        
        // Form validation
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>
