<?php
require __DIR__ . '/config/koneksi.php';

$password_mentah = 'password123';
$password_hash = password_hash($password_mentah, PASSWORD_BCRYPT);

$akun_dummy = [
    ['nama' => 'Admin Utama', 'email' => 'admin@donasi.com', 'role' => 'Admin'],
    ['nama' => 'Budi Campaigner', 'email' => 'campaigner@donasi.com', 'role' => 'Campaigner'],
    ['nama' => 'Siti Donatur', 'email' => 'donatur@donasi.com', 'role' => 'Donatur'],
    ['nama' => 'Pemeriksa Tim', 'email' => 'verifikator@donasi.com', 'role' => 'Verifikator']
];

echo "<h2>Membuat Akun Dummy...</h2><ul>";

foreach ($akun_dummy as $akun) {
    try {
        $cek = $pdo->prepare("SELECT id_user FROM users WHERE email = ?");
        $cek->execute([$akun['email']]);
        
        if ($cek->rowCount() > 0) {
            echo "<li><span style='color:orange;'>Akun {$akun['role']} ({$akun['email']}) sudah ada.</span></li>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$akun['nama'], $akun['email'], $password_hash, $akun['role']]);
            echo "<li><span style='color:green;'>Berhasil membuat akun {$akun['role']} ({$akun['email']})</span></li>";
        }
    } catch (Exception $e) {
        echo "<li><span style='color:red;'>Gagal membuat akun {$akun['role']}: " . $e->getMessage() . "</span></li>";
    }
}

echo "</ul>";
echo "<h3>Semua password adalah: <b>password123</b></h3>";
echo "<a href='login.php'>Kembali ke Login</a>";
?>
