<?php
require __DIR__ . '/core/middleware.php';
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/core/security.php';

wajib_login();
cek_role('Admin');

$errors = [];
$success = '';

// Filter role
$filter_role = isset($_GET['role']) ? trim($_GET['role']) : '';

// Update user (nama, email, role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id_user'] ?? 0);
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($id <= 0 || $nama === '' || $email === '' || $role === '') {
        $errors[] = 'Data tidak valid.';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE users SET nama_lengkap = :nama, email = :email, role = :role WHERE id_user = :id');
            $stmt->execute([':nama' => $nama, ':email' => $email, ':role' => $role, ':id' => $id]);
            $success = 'Akun berhasil diperbarui.';
        } catch (Exception $e) {
            $errors[] = 'Gagal memperbarui akun: ' . $e->getMessage();
        }
    }
}

// Delete user
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Prevent deleting self
    if ($id === ($_SESSION['id_user'] ?? 0)) {
        $errors[] = 'Anda tidak dapat menghapus akun Anda sendiri.';
    } else {
        try {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id_user = :id');
            $stmt->execute([':id' => $id]);
            header('Location: kelola_user.php');
            exit;
        } catch (Exception $e) {
            $errors[] = 'Gagal menghapus akun: ' . $e->getMessage();
        }
    }
}

// Build query
$sql = 'SELECT id_user, nama_lengkap, email, role, created_at FROM users';
if ($filter_role !== '') {
    $sql .= ' WHERE role = :role';
}
$sql .= ' ORDER BY id_user DESC';

try {
    if ($filter_role !== '') {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':role' => $filter_role]);
    } else {
        $stmt = $pdo->query($sql);
    }
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $errors[] = 'Gagal memuat daftar pengguna.';
    $users = [];
}

// Available roles (keep in sync with DB enum)
$available_roles = ['Admin', 'Campaigner', 'Donatur', 'Verifikator'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola User - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:1100px;margin-top:40px;">
    <h1 class="mb-4">Kelola Pengguna</h1>

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

    <div class="mb-3 d-flex gap-2 align-items-center">
        <form method="get" class="d-flex gap-2">
            <label class="me-2">Filter Role:</label>
            <select name="role" class="form-select form-select-sm" style="width:200px;">
                <option value="">Semua</option>
                <?php foreach ($available_roles as $r): ?>
                    <option value="<?= $r; ?>" <?= $filter_role === $r ? 'selected' : ''; ?>><?= $r; ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>
        <div class="ms-auto"><a href="dashboard_admin.php" class="btn btn-sm btn-secondary">Kembali</a></div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Belum ada pengguna.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= escape_html($u['id_user']); ?></td>
                                    <td><?= escape_html($u['nama_lengkap']); ?></td>
                                    <td><?= escape_html($u['email']); ?></td>
                                    <td><?= escape_html($u['role']); ?></td>
                                    <td><?= escape_html($u['created_at']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#editUser<?= $u['id_user']; ?>">Edit</button>
                                        <?php if (($u['id_user'] ?? 0) !== ($_SESSION['id_user'] ?? 0)): ?>
                                            <a href="kelola_user.php?action=delete&id=<?= $u['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus akun ini?');">Hapus</a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled>Hapus</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUser<?= $u['id_user']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id_user" value="<?= $u['id_user']; ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Pengguna</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Lengkap</label>
                                                        <input type="text" name="nama_lengkap" class="form-control" value="<?= escape_html($u['nama_lengkap']); ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" value="<?= escape_html($u['email']); ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Role</label>
                                                        <select name="role" class="form-select">
                                                            <?php foreach ($available_roles as $r): ?>
                                                                <option value="<?= $r; ?>" <?= $u['role'] === $r ? 'selected' : ''; ?>><?= $r; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button class="btn btn-primary">Simpan</button>
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
