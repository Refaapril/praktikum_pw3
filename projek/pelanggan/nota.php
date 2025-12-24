<?php
session_start();
require_once "../config/koneksi.php";

/* Cek login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['pesanan_id'])) {
    die("Nota tidak ditemukan - pesanan_id tidak diset");
}

$pesanan_id = $_GET['pesanan_id'];
$user_id = $_SESSION['user_id'];

$data = mysqli_query($conn, "
SELECT
    p.no_pesanan,
    p.tanggal_pesan,
    p.total_bayar,
    p.alamat_pengiriman,
    u.nama_lengkap
FROM pesanan p
JOIN users u ON p.user_id = u.user_id
WHERE p.pesanan_id='$pesanan_id' AND p.user_id='$user_id'
");

if (!$data) {
    die("Error query pesanan: " . mysqli_error($conn));
}

if (mysqli_num_rows($data) == 0) {
    die("Data pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.");
}

$nota = mysqli_fetch_assoc($data);

$detail = mysqli_query($conn, "
SELECT dp.*, pr.nama_produk
FROM detail_pesanan dp
JOIN produk pr ON dp.produk_id = pr.produk_id
WHERE dp.pesanan_id='$pesanan_id'
");

if (!$detail) {
    echo "<div style='background:red;color:white;padding:15px;margin:10px;border-radius:5px;'>";
    echo "<strong>❌ Error:</strong> Tidak dapat memuat detail produk dari database.<br>";
    echo "<small>" . mysqli_error($conn) . "</small>";
    echo "</div>";
    echo "<div style='background:yellow;color:black;padding:15px;margin:10px;border-radius:5px;'>";
    echo "<strong>ℹ️ Fallback Mode:</strong> Menampilkan informasi dasar pesanan saja.";
    echo "</div>";
    $detail = [];
} elseif (mysqli_num_rows($detail) == 0) {
    echo "<div style='background:orange;color:black;padding:15px;margin:10px;border-radius:5px;'>";
    echo "<strong>⚠️ Peringatan:</strong> Detail produk tidak tersedia di database. Menampilkan info dasar pesanan.";
    echo "</div>";
    $detail = [];
} else {
    echo "<div style='background:green;color:white;padding:10px;margin:10px;border-radius:5px;'>";
    echo "<strong>✅ Status:</strong> Semua informasi lengkap ditampilkan.";
    echo "</div>";
    $detail = mysqli_fetch_all($detail, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Nota Pembayaran</title>

<style>
body{font-family:Poppins;background:#f7faff;padding:40px}
.container{
    max-width:700px;
    margin:auto;
    background:#fff;
    padding:30px;
    border-radius:16px;
}
h2{text-align:center;color:#FF4D88}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:10px;border-bottom:1px solid #eee}
th{background:#FF4D88;color:#fff}
.total{text-align:right;font-size:18px;margin-top:15px}
.btn{
    display:inline-block;
    margin-top:20px;
    padding:10px 18px;
    background:#6DD5FA;
    color:#fff;
    border-radius:10px;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="container">

<h2>🧾 Nota Pembayaran</h2>

<p><b>No Pesanan:</b> <?= htmlspecialchars($nota['no_pesanan']); ?></p>
<p><b>Nama:</b> <?= htmlspecialchars($nota['nama_lengkap']); ?></p>
<p><b>Tanggal:</b> <?= date('d-m-Y',strtotime($nota['tanggal_pesan'])); ?></p>
<p><b>Alamat:</b><br><?= nl2br(htmlspecialchars($nota['alamat_pengiriman'])); ?></p>

<?php if (empty($detail)): ?>
    <div style="background:#f0f0f0;padding:15px;border-radius:5px;margin:20px 0;border-left:4px solid #FF4D88;">
        <h3 style="margin-top:0;color:#FF4D88;">ℹ️ Informasi Pesanan Dasar</h3>
        <p><strong>ID Pesanan:</strong> <?= htmlspecialchars($nota['pesanan_id'] ?? $pesanan_id); ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($nota['status'] ?? 'Tidak diketahui'); ?></p>
        <p><strong>Total Pembayaran:</strong> Rp <?= number_format($nota['total_bayar']); ?></p>
        <p><em>Detail produk tidak tersedia - kemungkinan ada masalah dengan database.</em></p>
    </div>
<?php else: ?>
    <table>
    <tr>
        <th>Produk</th>
        <th>Harga</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>

    <?php $total_calc = 0; ?>
    <?php foreach($detail as $d) { 
        $subtotal = ($d['harga'] ?? 0) * ($d['qty'] ?? 1);
        $total_calc += $subtotal;
    ?>
    <tr>
        <td><?= htmlspecialchars($d['nama_produk'] ?? 'Produk tidak ditemukan'); ?></td>
        <td>Rp <?= number_format($d['harga'] ?? 0); ?></td>
        <td><?= $d['qty'] ?? 'N/A'; ?></td>
        <td>Rp <?= number_format($subtotal); ?></td>
    </tr>
    <?php } ?>
    </table>
<?php endif; ?>

<div class="total">
    <b>Total Bayar: Rp <?= number_format($nota['total_bayar']); ?></b>
</div>

<a href="#" onclick="window.print()" class="btn">🖨 Cetak Nota</a>
<a href="beranda.php" class="btn" style="background:#FF4D88;margin-left:10px;">← Kembali ke Beranda</a>

</div>

</body>
</html>
