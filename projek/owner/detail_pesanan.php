<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: kelola_laporan.php");
    exit;
}

$pesanan_id = $_GET['id'];

// Ambil data pesanan
$query = "
SELECT 
    p.*,
    u.nama_lengkap,
    u.email,
    u.no_hp,
    b.bukti_pembayaran
FROM pesanan p
JOIN users u ON p.user_id = u.user_id
LEFT JOIN pembayaran b ON p.pesanan_id = b.pesanan_id
WHERE p.pesanan_id = '$pesanan_id'
";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Pesanan tidak ditemukan");
}
$pesanan = mysqli_fetch_assoc($result);

// Ambil detail produk
$detail_query = "
SELECT 
    dp.*,
    pr.nama_produk,
    pr.harga
FROM detail_pesanan dp
JOIN produk pr ON dp.produk_id = pr.produk_id
WHERE dp.pesanan_id = '$pesanan_id'
";
$detail = mysqli_query($conn, $detail_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Pesanan - Owner</title>
<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f7faff;
    padding:30px;
}
.detail-card{
    background:#fff;
    padding:25px;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
    margin-bottom:20px;
}
.detail-card h3{
    color:#FF4D88;
    margin-top:0;
}
.detail-card p{
    margin:5px 0;
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
    text-align:left;
}
th{
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
}
tr:nth-child(even){background:#f9fbff;}
.btn{
    padding:10px 20px;
    background:#FF4D88;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    display:inline-block;
    margin-top:20px;
}
.btn:hover{opacity:.9;}
</style>
</head>
<body>

<h2>📋 Detail Pesanan #<?= htmlspecialchars($pesanan['no_pesanan']); ?></h2>

<div class="detail-card">
    <h3>Informasi Pesanan</h3>
    <p><strong>No Pesanan:</strong> <?= htmlspecialchars($pesanan['no_pesanan']); ?></p>
    <p><strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($pesanan['tanggal_pesan'])); ?></p>
    <p><strong>Status:</strong> <?= ucfirst($pesanan['status_pesanan']); ?></p>
    <p><strong>Total Bayar:</strong> Rp <?= number_format($pesanan['total_bayar']); ?></p>
</div>

<div class="detail-card">
    <h3>Informasi Pelanggan</h3>
    <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama_lengkap']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($pesanan['email']); ?></p>
    <p><strong>No HP:</strong> <?= htmlspecialchars($pesanan['no_hp']); ?></p>
    <p><strong>Alamat:</strong> <?= htmlspecialchars($pesanan['alamat_pengiriman']); ?></p>
</div>

<?php if(!empty($pesanan['bukti_pembayaran'])): ?>
<div class="detail-card">
    <h3>Bukti Pembayaran</h3>
    <img src="../assets/bukti/<?= htmlspecialchars($pesanan['bukti_pembayaran']); ?>" style="max-width:300px;border-radius:8px;">
</div>
<?php endif; ?>

<div class="detail-card">
    <h3>Detail Produk</h3>
    <table>
    <tr>
        <th>Produk</th>
        <th>Harga</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>
    <?php while($d = mysqli_fetch_assoc($detail)): ?>
    <tr>
        <td><?= htmlspecialchars($d['nama_produk']); ?></td>
        <td>Rp <?= number_format($d['harga']); ?></td>
        <td><?= $d['qty']; ?></td>
        <td>Rp <?= number_format($d['harga'] * $d['qty']); ?></td>
    </tr>
    <?php endwhile; ?>
    </table>
</div>

<a href="kelola_laporan.php" class="btn">← Kembali ke Laporan</a>

</body>
</html>
