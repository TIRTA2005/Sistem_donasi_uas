<?php
require __DIR__ . '/core/middleware.php';
require __DIR__ . '/config/koneksi.php';
require __DIR__ . '/core/security.php';

wajib_login();
cek_role('admin');

if (isset($_GET['aksi']) && isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    if ($_GET['aksi'] == 'setujui') {

        $stmt = $pdo->prepare("
            UPDATE kampanye
            SET status_kampanye = 'Active'
            WHERE id_kampanye = ?
        ");

        $stmt->execute([$id]);

        header("Location: dashboard_verifikator.php");
        exit;
    }

    if ($_GET['aksi'] == 'tolak') {

        $stmt = $pdo->prepare("
            UPDATE kampanye
            SET status_kampanye = 'Rejected'
            WHERE id_kampanye = ?
        ");

        $stmt->execute([$id]);

        header("Location: dashboard_verifikator.php");
        exit;
    }
}

$queryStatistik = $pdo->query("
    SELECT COUNT(*) AS total
    FROM kampanye
    WHERE status_kampanye='Pending'
");

$totalPending = $queryStatistik->fetch()['total'];

$query = $pdo->query("
SELECT
    k.id_kampanye,
    k.judul,
    k.deskripsi,
    k.target_dana,
    k.status_kampanye,
    k.created_at,
    u.nama_lengkap,

    COALESCE(
        SUM(
            CASE
                WHEN t.status_pembayaran='Success'
                THEN t.nominal
                ELSE 0
            END
        ),
    0) AS total_donasi

FROM kampanye k

JOIN users u
ON k.id_user=u.id_user

LEFT JOIN transaksi t
ON k.id_kampanye=t.id_kampanye

WHERE k.status_kampanye='Pending'

GROUP BY
k.id_kampanye,
k.judul,
k.deskripsi,
k.target_dana,
k.status_kampanye,
k.created_at,
u.nama_lengkap

ORDER BY
k.created_at DESC
");

$kampanye = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dasbor Verifikator</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link
href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<style>

body{

font-family:'Inter',sans-serif;
background:#f8fafc;
color:#1e293b;
display:flex;
min-height:100vh;

}

.sidebar{

width:260px;
background:linear-gradient(180deg,#1e293b,#0f172a);
position:fixed;
top:0;
left:0;
height:100%;
display:flex;
flex-direction:column;
color:white;

}

.sidebar-brand{

padding:25px;
font-size:22px;
font-weight:700;
border-bottom:1px solid rgba(255,255,255,.1);

}

.sidebar-brand i{

color:#38bdf8;
margin-right:10px;

}
.sidebar-menu{

list-style:none;
padding:20px 0;
margin:0;
flex:1;

}

.sidebar-menu li{

margin:0;

}

.sidebar-menu li a{

display:flex;
align-items:center;
gap:12px;
padding:15px 25px;
color:#cbd5e1;
text-decoration:none;
font-weight:500;
transition:.2s;

}

.sidebar-menu li a:hover{

background:rgba(255,255,255,.08);
color:#fff;
border-left:4px solid #38bdf8;
padding-left:21px;

}

.sidebar-menu li.active a{

background:rgba(255,255,255,.08);
color:#fff;
border-left:4px solid #38bdf8;
padding-left:21px;

}

.sidebar-footer{

padding:20px;
border-top:1px solid rgba(255,255,255,.1);

}

.logout-btn{

display:flex;
align-items:center;
gap:10px;
text-decoration:none;
color:#fca5a5;
font-weight:600;

}

.logout-btn:hover{

color:#ef4444;

}

.main-content{

margin-left:260px;
padding:40px;
width:100%;

}

.header{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;

}

.header h2{

font-weight:700;
margin:0;

}

.header p{

color:#64748b;
margin-top:5px;

}

.info-card{

background:#fff;
border-radius:15px;
padding:25px;
margin-bottom:30px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.info-card h3{

font-size:40px;
font-weight:bold;
margin:0;
color:#0f172a;

}

.info-card p{

margin:8px 0 0;
color:#64748b;

}

.table-card{

background:#fff;
border-radius:15px;
padding:25px;
box-shadow:0 5px 15px rgba(0,0,0,.08);

}

.table th{

background:#f8fafc;

}

.badge-pending{

background:#fef3c7;
color:#92400e;
padding:8px 14px;
border-radius:20px;

}

.btn-success{

font-weight:600;

}

.btn-danger{

font-weight:600;

}

@media(max-width:992px){

.sidebar{

width:70px;

}

.sidebar-brand span,
.sidebar-menu span,
.sidebar-footer span{

display:none;

}

.main-content{

margin-left:70px;

}

}

</style>

</head>

<body>

<div class="sidebar">

<div class="sidebar-brand">

<i class="fa-solid fa-hand-holding-heart"></i>

<span>PeduliKasih</span>

</div>

<ul class="sidebar-menu">

<li class="active">

<a href="dashboard_verifikator.php">

<i class="fa-solid fa-circle-check"></i>

<span>Dasbor Verifikator</span>

</a>

</li>

</ul>

<div class="sidebar-footer">

<a href="logout.php" class="logout-btn">

<i class="fa-solid fa-right-from-bracket"></i>

<span>Keluar</span>

</a>

</div>

</div>

<div class="main-content">

<div class="header">

<div>

<h2>Dasbor Verifikator</h2>

<p>Kelola seluruh kampanye yang menunggu proses verifikasi.</p>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="info-card">

<h3><?= $totalPending ?></h3>

<p>Kampanye Menunggu Verifikasi</p>

</div>

</div>

</div>

<div class="table-card">

<h4 class="mb-4">

<i class="fa-solid fa-list-check"></i>

Daftar Kampanye

</h4>
<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead>

<tr>

<th>ID</th>

<th>Judul Kampanye</th>

<th>Pembuat Kampanye</th>

<th>Target Dana</th>

<th>Total Donasi</th>

<th>Status</th>

<th>Tanggal Dibuat</th>

<th width="180">Aksi</th>

</tr>

</thead>

<tbody>

<?php if(count($kampanye) > 0): ?>

<?php foreach($kampanye as $row): ?>

<tr>

<td>

<?= $row['id_kampanye']; ?>

</td>

<td>

<strong>

<?= htmlspecialchars($row['judul']); ?>

</strong>

<br>

<small class="text-muted">

<?= htmlspecialchars(substr($row['deskripsi'],0,80)); ?>

<?php if(strlen($row['deskripsi'])>80): ?>

...

<?php endif; ?>

</small>

</td>

<td>

<?= htmlspecialchars($row['nama_lengkap']); ?>

</td>

<td>

Rp <?= number_format($row['target_dana'],0,",","."); ?>

</td>

<td>

<strong class="text-success">

Rp <?= number_format($row['total_donasi'],0,",","."); ?>

</strong>

</td>

<td>

<span class="badge-pending">

<?= $row['status_kampanye']; ?>

</span>

</td>

<td>

<?= date('d M Y H:i',strtotime($row['created_at'])); ?>

</td>

<td>

<div class="d-flex gap-2">

<a

href="?aksi=setujui&id=<?= $row['id_kampanye']; ?>"

class="btn btn-success btn-sm"

onclick="return confirm('Apakah Anda yakin ingin menyetujui kampanye ini?');">

<i class="fa-solid fa-check"></i>

Setujui

</a>

<a

href="?aksi=tolak&id=<?= $row['id_kampanye']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Apakah Anda yakin ingin menolak kampanye ini?');">

<i class="fa-solid fa-xmark"></i>

Tolak

</a>

</div>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="8" class="text-center py-5">

<i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>

<br><br>

<strong>

Tidak ada kampanye yang menunggu verifikasi.

</strong>

</td>

</tr>

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