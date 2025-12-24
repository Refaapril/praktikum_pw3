<?php
session_start();
require_once "../config/koneksi.php";

/* =====================
   FILTER TANGGAL
===================== */
$dari   = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$where = "WHERE p.status_pesanan != 'pending'";

if (!empty($dari) && !empty($sampai)) {
    $where .= " AND DATE(p.tanggal_pesan) BETWEEN '$dari' AND '$sampai'";
}

/* =====================
   RINGKASAN LAPORAN
===================== */
$ringkasan_query = "
SELECT 
    COUNT(*) AS total_transaksi,
    COALESCE(SUM(p.total_bayar),0) AS total_pendapatan
FROM pesanan p
$where
";

$ringkasan = mysqli_query($conn, $ringkasan_query);
if (!$ringkasan) {
    die("Query Ringkasan Error: " . mysqli_error($conn));
}
$r = mysqli_fetch_assoc($ringkasan);

/* =====================
   DATA LAPORAN
===================== */
$data_query = "
SELECT 
    p.pesanan_id,
    p.no_pesanan,
    p.tanggal_pesan,
    p.total_bayar,
    p.status_pesanan,
    u.nama_lengkap,
    b.bukti_pembayaran AS bukti
FROM pesanan p
JOIN users u ON p.user_id = u.user_id
LEFT JOIN pembayaran b ON p.pesanan_id = b.pesanan_id
$where
ORDER BY p.tanggal_pesan DESC
";

$data = mysqli_query($conn, $data_query);
if (!$data) {
    die("Query Data Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Laporan Penjualan</title>

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f7faff;
    padding:30px;
}
h2{color:#FF4D88;}

.filter{
    background:#fff;
    padding:20px;
    border-radius:16px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,.12);
}
.filter input,button{
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
}
button{
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
    border:none;
    cursor:pointer;
}

.summary{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-bottom:25px;
}
.card{
    background:#fff;
    padding:25px;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
}
.card h3{color:#6DD5FA;margin:0;}
.card p{
    font-size:24px;
    font-weight:bold;
    color:#FF4D88;
    margin-top:10px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
}
th,td{
    padding:14px;
    text-align:center;
}
th{
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
}
tr:nth-child(even){background:#f9fbff;}

img{width:60px;border-radius:8px;}

.btn{
    padding:6px 12px;
    border-radius:8px;
    text-decoration:none;
    color:#fff;
    font-size:13px;
}
.detail{background:#3498db;}
.cetak{background:#2ecc71;}
</style>
</head>

<body>

<h2>📊 Kelola Laporan Penjualan</h2>

<!-- FILTER -->
<div class="filter">
<form method="GET">
    Dari: <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>">
    Sampai: <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>">
    <button type="submit">Filter</button>
</form>
</div>

<!-- RINGKASAN -->
<div class="summary">
    <div class="card">
        <h3>Total Transaksi</h3>
        <p><?= $r['total_transaksi']; ?></p>
    </div>
    <div class="card">
        <h3>Total Pendapatan</h3>
        <p>Rp <?= number_format($r['total_pendapatan']); ?></p>
    </div>
</div>

<!-- TABEL -->
<table>
<tr>
    <th>No</th>
    <th>No Pesanan</th>
    <th>Pelanggan</th>
    <th>Tanggal</th>
    <th>Total</th>
    <th>Bukti</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if(mysqli_num_rows($data) > 0): ?>
<?php $no=1; while($d = mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= htmlspecialchars($d['no_pesanan']); ?></td>
    <td><?= htmlspecialchars($d['nama_lengkap']); ?></td>
    <td><?= date('d-m-Y', strtotime($d['tanggal_pesan'])); ?></td>
    <td>Rp <?= number_format($d['total_bayar']); ?></td>
    <td>
        <?php if(!empty($d['bukti'])): ?>
            <a href="../assets/bukti/<?= htmlspecialchars($d['bukti']); ?>" target="_blank">
                <img src="../assets/bukti/<?= htmlspecialchars($d['bukti']); ?>">
            </a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
    <td><?= ucfirst(str_replace('_',' ',$d['status_pesanan'])); ?></td>
    <td>
        <a href="detail_pesanan.php?id=<?= $d['pesanan_id']; ?>" class="btn detail">Detail</a>
        <a href="cetak_nota.php?id=<?= $d['pesanan_id']; ?>" target="_blank" class="btn cetak">Cetak</a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="8">Data tidak ditemukan</td>
</tr>
<?php endif; ?>

</table>

</body>
</html>
