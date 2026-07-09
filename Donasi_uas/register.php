<?php
require 'config/koneksi.php';
require 'core/security.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = escape_html($_POST['nama_lengkap']);
    $email = escape_html($_POST['email']);
    $password_mentah = $_POST['password'];
    $role = 'Donatur'; 

    $password_hash = password_hash($password_mentah, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $email, $password_hash, $role]);
        echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
    } catch (\PDOException $e) {
        echo "Error: Email mungkin sudah terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Donatur - DonasiKita</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <a href="index.php" class="back-home">&larr; Kembali ke Beranda</a>
        <div class="auth-card">
            <div class="auth-header">
                <h2>Buat Akun Baru</h2>
                <p>Bergabunglah menjadi pahlawan kebaikan hari ini.</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama Anda" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email aktif" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat password yang kuat" required>
                </div>
                
                <button type="submit" class="btn-auth">Daftar Sekarang</button>
            </form>
            
            <div class="auth-footer">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
