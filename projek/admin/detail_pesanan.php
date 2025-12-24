<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_GET['id'])) {
    die("ID pesanan tidak ditemukan");
}

$pesanan_id = $_GET['id'];

// Ambil data pesanan
$query = "
SELECT 
    p.*,
    u.nama_lengkap,
    u.email,
    u.no_hp,
    u.alamat
FROM pesanan p
JOIN users u ON p.user_id = u.user_id
WHERE p.pesanan_id = '$pesanan_id'
";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("Pesanan tidak ditemukan");
}
$pesanan = mysqli_fetch_assoc($result);

// Coba ambil detail, tapi jika kosong tetap tampilkan nota
$detail_query = "
SELECT 
    dp.*,
    pr.nama_produk
FROM detail_pesanan dp
JOIN produk pr ON dp.produk_id = pr.produk_id
WHERE dp.pesanan_id = '$pesanan_id'
";

$detail = mysqli_query($conn, $detail_query);
$has_detail = ($detail && mysqli_num_rows($detail) > 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota Pesanan - <?= htmlspecialchars($pesanan['no_pesanan']); ?></title>
<style>
body{
    font-family:'Courier New', monospace;
    font-size:12px;
    max-width:400px;
    margin:0 auto;
    padding:20px;
}
h2{
    text-align:center;
    margin-bottom:20px;
}
.info{
    margin-bottom:15px;
    line-height:1.5;
}
.produk{
    border-collapse:collapse;
    width:100%;
    margin-bottom:15px;
}
.produk th, .produk td{
    border:1px solid #000;
    padding:5px;
    text-align:left;
}
.total{
    text-align:right;
    font-weight:bold;
    margin-top:10px;
    font-size:14px;
}
.no-data{
    text-align:center;
    padding:15px;
    border:1px dashed #ccc;
    margin:15px 0;
    color:#666;
}
.header{
    text-align:center;
    border-bottom:2px solid #000;
    padding-bottom:10px;
    margin-bottom:20px;
}
.footer{
    text-align:center;
    margin-top:30px;
    font-style:italic;
}
@media print {
    body { margin: 0; padding: 10px; }
    .no-print { display: none; }
}
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:20px; padding:10px; background:#f0f0f0;">
    <strong>INFO:</strong> Tabel detail_pesanan kosong. Tampilkan data pesanan saja.
</div>

<div class="header">
    <h2>SKINCARE SHOP</h2>
    <div>Jl. Contoh No. 123, Kota Contoh</div>
    <div>Telp: (021) 123-4567</div>
</div>

<div class="info">
    <strong>No. Pesanan:</strong> <?= htmlspecialchars($pesanan['no_pesanan']); ?><br>
    <strong>Tanggal:</strong> <?= date('d-m-Y H:i', strtotime($pesanan['tanggal_pesan'])); ?><br>
    <strong>Pelanggan:</strong> <?= htmlspecialchars($pesanan['nama_lengkap']); ?><br>
    <strong>Email:</strong> <?= htmlspecialchars($pesanan['email']); ?><br>
    <strong>Telepon:</strong> <?= htmlspecialchars($pesanan['no_hp']); ?><br>
    <strong>Alamat Pengiriman:</strong><br>
    <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?>
</div>

<?php if ($has_detail): ?>
<table class="produk">
<tr>
    <th>Produk</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>
<?php 
$total = 0;
while($d = mysqli_fetch_assoc($detail)): 
    $subtotal = $d['harga_satuan'] * $d['quantity'];
    $total += $subtotal;
?>
<tr>
    <td><?= htmlspecialchars($d['nama_produk']); ?></td>
    <td>Rp <?= number_format($d['harga_satuan']); ?></td>
    <td><?= $d['quantity']; ?></td>
    <td>Rp <?= number_format($subtotal); ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<div class="no-data">
    <strong>Detail produk tidak tersedia</strong><br>
    Data detail transaksi tidak ditemukan di sistem.
</div>
<?php 
// Gunakan total dari tabel pesanan jika detail tidak ada
$total = $pesanan['total_bayar'];
?>
<?php endif; ?>

<div class="total">
    <strong>TOTAL: Rp <?= number_format($total); ?></strong>
</div>

<div class="info">
    <strong>Metode Pembayaran:</strong> <?= !empty($pesanan['metode_pembayaran']) ? $pesanan['metode_pembayaran'] : 'Transfer Bank'; ?><br>
    <strong>Status Pesanan:</strong> <?= $pesanan['status_pesanan']; ?><br>
    <?php if (!empty($pesanan['catatan'])): ?>
    <strong>Catatan:</strong> <?= htmlspecialchars($pesanan['catatan']); ?>
    <?php endif; ?>
</div>

<div class="footer">
    Terima kasih atas pembelian Anda!<br>
    Barang yang sudah dibeli tidak dapat dikembalikan.
</div>

<div class="no-print" style="margin-top:20px; text-align:center;">
    <button onclick="window.print()">Cetak Nota</button>
    <button onclick="window.close()">Tutup</button>
</div>

<script>
window.onload = function() {
    // Auto print setelah halaman load
    setTimeout(function() {
        window.print();
    }, 1000);
}
</script>

</body>
</html>
