<?php
session_start();
require 'config/koneksi.php';
require 'core/security.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = escape_html($_POST['email']);
    $password_input = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password_input, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

        if ($user['role'] == 'Admin') header("Location: dashboard_admin.php");
        else if ($user['role'] == 'Donatur') header("Location: dashboard_donatur.php");
        else if ($user['role'] == 'Campaigner') header("Location: dashboard_campaigner.php");
        else if ($user['role'] == 'Verifikator') header("Location: dashboard_verifikator.php");
        exit;
    } else {
        echo "<script>alert('Email atau Password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DonasiKita</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <a href="index.php" class="back-home">&larr; Kembali ke Beranda</a>
        <div class="auth-card">
            <div class="auth-header">
                <h2>Selamat Datang</h2>
                <p>Silakan masuk ke akun Anda</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                </div>
                
                <button type="submit" class="btn-auth">Masuk</button>
            </form>
            
            <div class="auth-footer">
                Belum punya akun? <a href="register.php">Daftar sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>
