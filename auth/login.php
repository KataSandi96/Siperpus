<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role == 'admin') {
        header("Location: ../admin/index.php");
    } elseif ($role == 'pustakawan') {
        header("Location: ../pustakawan/index.php");
    } elseif ($role == 'anggota') {
        header("Location: ../anggota/index.php");
    }
    exit();
}

require_once '../config/database.php';

$error = '';
$success = '';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        $sql = "SELECT u.*, 
                COALESCE(a.nama_lengkap, k.nama_lengkap) as nama_lengkap,
                COALESCE(a.status_verifikasi, 'aktif') as status_user
                FROM users u
                LEFT JOIN anggota a ON u.id_user = a.id_user
                LEFT JOIN karyawan k ON u.id_user = k.id_user
                WHERE u.username = '$username' AND u.status = 'aktif'";
        
        $result = query($sql);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                if ($user['role'] == 'anggota' && $user['status_user'] == 'menunggu') {
                    $error = "Akun Anda masih menunggu verifikasi oleh pustakawan.";
                } elseif ($user['role'] == 'anggota' && $user['status_user'] == 'nonaktif') {
                    $error = "Akun Anda sudah dinonaktifkan. Hubungi pustakawan.";
                } else {
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    $_SESSION['login_time'] = time();
                    
                    $notif_sql = "INSERT INTO notifikasi (id_user, judul, pesan) 
                                  VALUES ('{$user['id_user']}', 'Login Berhasil', 
                                  'Anda berhasil login pada " . date('d-m-Y H:i:s') . "')";
                    query($notif_sql);
                    
                    if ($user['role'] == 'admin') {
                        header("Location: ../admin/index.php");
                    } elseif ($user['role'] == 'pustakawan') {
                        header("Location: ../pustakawan/index.php");
                    } elseif ($user['role'] == 'anggota') {
                        header("Location: ../anggota/index.php");
                    }
                    exit();
                }
            } else {
                $error = "Username atau password salah!";
            }
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiPerpus - Universitas Merangin</title>
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
        
        /* Background Image Layer */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            
            /* GANTI GAMBAR DI SINI */
            background-image: url('../assets/img/login-bg.jpg');
            /* atau upload ke: url('../assets/img/login-bg.jpg'); */
            
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        /* Overlay Hitam Transparan untuk Kontras */
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            /* BG UNGU DIHILANGKAN, diganti overlay hitam transparan */
            background: rgba(0, 0, 0, 0.5); 
        }
        
        .login-container {
            /* Dibuat solid putih agar lebih tegas dan mudah dibaca */
            background: white; 
            padding: 45px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 450px;
            animation: slideIn 0.6s ease-out;
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
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header .logo {
            width: 90px;
            height: 90px;
            /* Ganti background logo agar tidak ungu */
            background: #2c3e50;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            color: white;
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.4);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .login-header h2 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 28px;
        }
        
        .login-header p {
            color: #7f8c8d;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 20px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px 15px 55px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #34495e;
            background: white;
            box-shadow: 0 0 0 4px rgba(44, 62, 80, 0.1);
        }
        
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .alert-danger {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            /* Ganti background button agar tidak ungu */
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            background: #34495e;
            box-shadow: 0 8px 25px rgba(44, 62, 80, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #ecf0f1;
        }
        
        .login-footer p {
            color: #7f8c8d;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #34495e;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Background Image -->
    <div class="background-image"></div>
    <div class="background-overlay"></div>
    
    <div class="login-container">
        <div class="login-header">
            <div class="logo">📚</div>
            <h2>Login Sistem</h2>
            <p>SiPerpus - Universitas Merangin</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" id="username" name="username" 
                           placeholder="Masukkan username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required autofocus>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" 
                           placeholder="Masukkan password" required>
                </div>
            </div>
            
            <button type="submit" class="btn-login">
                🔐 Login
            </button>
        </form>
        
        <div class="login-footer">
            <p>Belum punya akun? <a href="../pengunjung/daftar.php">Daftar Anggota</a></p>
            <div class="back-home">
                <a href="../index.php">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>
    
    <script>
        // Auto hide alert after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
