<?php
session_start();
require __DIR__ . '/config/koneksi.php';

$stmt = $pdo->prepare("
    SELECT k.*, kat.nama_kategori, 
           COALESCE((SELECT SUM(nominal) FROM transaksi WHERE id_kampanye = k.id_kampanye AND status_pembayaran = 'Success'), 0) AS dana_terkumpul
    FROM kampanye k
    JOIN kategori kat ON k.id_kategori = kat.id_kategori
    WHERE k.status_kampanye = 'Active'
    ORDER BY k.created_at DESC
    LIMIT 6
");
$stmt->execute();
$kampanye_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonasiKita - Platform Crowdfunding Terpercaya</title>
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            DonasiKita
        </a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="#kampanye">Kampanye</a>
            <?php if (isset($_SESSION['id_user'])): ?>
                <a href="dashboard_<?= strtolower($_SESSION['role']) ?>.php" class="btn-login">Dashboard</a>
                <a href="logout.php" class="btn-register" style="background:#EF4444; border-color:#EF4444;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Masuk</a>
                <a href="register.php" class="btn-register">Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Mari Bersama Wujudkan <span>Harapan Mereka</span></h1>
            <p>Platform crowdfunding terpercaya untuk membantu sesama. Mulai langkah kecilmu hari ini, karena setiap donasi sangat berarti bagi mereka yang membutuhkan.</p>
            <a href="#kampanye" class="btn-hero">Mulai Berdonasi</a>
        </div>
    </section>

    <section id="kampanye" class="campaigns-section">
        <div class="section-header">
            <h2>Kampanye Mendesak</h2>
            <p>Pilih kampanye dan bantu ringankan beban saudara-saudara kita. Sedikit bantuan Anda adalah harapan besar bagi mereka.</p>
        </div>

        <div class="campaign-grid">
            <?php if (count($kampanye_list) > 0): ?>
                <?php foreach ($kampanye_list as $row): 
                    $persentase = ($row['target_dana'] > 0) ? ($row['dana_terkumpul'] / $row['target_dana']) * 100 : 0;
                    if ($persentase > 100) $persentase = 100;
                    
                    $gambar_path = "uploads/" . htmlspecialchars($row['gambar_banner']);
                    if (!file_exists($gambar_path) || empty($row['gambar_banner'])) {
                        $gambar_path = "https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?auto=format&fit=crop&q=80&w=800"; 
                    } else {
                    }
                ?>
                <div class="campaign-card">
                    <div class="card-img-wrapper">
                        <img src="<?= empty($row['gambar_banner']) ? 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?auto=format&fit=crop&q=80&w=800' : 'uploads/' . $row['gambar_banner'] ?>" alt="<?= $row['judul'] ?>" class="card-img" onerror="this.src='https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?auto=format&fit=crop&q=80&w=800'">
                        <span class="kategori-badge"><?= $row['nama_kategori'] ?></span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= $row['judul'] ?></h3>
                        <p class="card-desc"><?= substr(strip_tags($row['deskripsi']), 0, 100) ?>...</p>
                        
                        <div class="progress-container">
                            <div class="progress-stats">
                                <span class="terkumpul">Rp <?= number_format($row['dana_terkumpul'], 0, ',', '.') ?></span>
                                <span class="target">Target: Rp <?= number_format($row['target_dana'], 0, ',', '.') ?></span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: <?= $persentase ?>%"></div>
                            </div>
                            <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] == 'Donatur'): ?>
                                <a href="dashboard_donatur.php" class="btn-card">Donasi Sekarang</a>
                            <?php else: ?>
                                <a href="login.php" class="btn-card">Donasi Sekarang</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #6B7280;">
                    <h3>Belum ada kampanye aktif saat ini.</h3>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>&copy; <?= date('Y') ?> DonasiKita./p>
    </footer>

</body>
</html>
