<?php
require __DIR__ . '/core/middleware.php';
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/core/security.php';

wajib_login();
cek_role('Admin');

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nama = trim($_POST['nama_kategori'] ?? '');
    if ($nama === '') {
        $errors[] = 'Nama kategori tidak boleh kosong.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO kategori (nama_kategori) VALUES (:nama)');
            $stmt->execute([':nama' => $nama]);
            $success = 'Kategori berhasil ditambahkan.';
        } catch (Exception $e) {
            $errors[] = 'Gagal menambahkan kategori: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id_kategori'] ?? 0);
    $nama = trim($_POST['nama_kategori'] ?? '');
    if ($id <= 0 || $nama === '') {
        $errors[] = 'Data tidak valid.';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE kategori SET nama_kategori = :nama WHERE id_kategori = :id');
            $stmt->execute([':nama' => $nama, ':id' => $id]);
            $success = 'Kategori berhasil diperbarui.';
        } catch (Exception $e) {
            $errors[] = 'Gagal memperbarui kategori: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        try {
            $chk = $pdo->prepare('SELECT COUNT(*) FROM kampanye WHERE id_kategori = :id');
            $chk->execute([':id' => $id]);
            $count = (int) $chk->fetchColumn();
            if ($count > 0) {
                $errors[] = 'Kategori ini masih digunakan oleh kampanye. Hapus atau pindahkan kampanye terkait terlebih dahulu.';
            } else {
                $stmt = $pdo->prepare('DELETE FROM kategori WHERE id_kategori = :id');
                $stmt->execute([':id' => $id]);
                header('Location: kelola_kategori.php');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Gagal menghapus kategori: ' . $e->getMessage();
        }
    }
}

$categories = [];
try {
    $q = $pdo->query('SELECT * FROM kategori ORDER BY id_kategori DESC');
    $categories = $q->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $errors[] = 'Gagal memuat daftar kategori.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Kategori - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial}
        .container{max-width:1000px;margin-top:40px}
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Kelola Kategori</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= escape_html($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= escape_html($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" class="row g-2 align-items-center">
                <input type="hidden" name="action" value="create">
                <div class="col-sm-8">
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Nama kategori baru">
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-primary">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Daftar Kategori</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Belum ada kategori.</td></tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= escape_html($cat['id_kategori']); ?></td>
                                    <td><?= escape_html($cat['nama_kategori']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id_kategori']; ?>">Edit</button>
                                        <a href="kelola_kategori.php?action=delete&id=<?= $cat['id_kategori']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?');">Hapus</a>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?= $cat['id_kategori']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id_kategori" value="<?= $cat['id_kategori']; ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Kategori</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Kategori</label>
                                                        <input type="text" name="nama_kategori" class="form-control" value="<?= escape_html($cat['nama_kategori']); ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
