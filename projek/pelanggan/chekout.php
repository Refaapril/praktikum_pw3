<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Produk tidak ditemukan");
}

$user_id   = $_SESSION['user_id'];
$produk_id = $_GET['id'];

/* Ambil produk */
$q = mysqli_query($conn, "SELECT * FROM produk WHERE produk_id='$produk_id'");
$p = mysqli_fetch_assoc($q);

if (!$p) {
    die("Produk tidak valid");
}

/* ================= PROSES CHECKOUT ================= */
if (isset($_POST['checkout'])) {

    $alamat     = $_POST['alamat']; // ✅ ALAMAT
    $total      = $p['harga'];
    $no_pesanan = "ORD-" . date("YmdHis");

    /* Simpan ke tabel pesanan (ALAMAT DISIMPAN) */
    mysqli_query($conn, "
        INSERT INTO pesanan
        (user_id, no_pesanan, tanggal_pesan, total_bayar, alamat_pengiriman, status_pesanan)
        VALUES
        ('$user_id','$no_pesanan',CURDATE(),'$total','$alamat','pending')
    ");

    $pesanan_id = mysqli_insert_id($conn);

    /* Simpan detail pesanan */
    $detail_query = mysqli_query($conn, "
        INSERT INTO detail_pesanan
        (pesanan_id, produk_id, harga, qty)
        VALUES
        ('$pesanan_id','$produk_id','{$p['harga']}',1)
    ");

    if (!$detail_query) {
        echo "<div style='background:orange;color:black;padding:10px;margin:10px;border-radius:5px;'>";
        echo "⚠️ Peringatan: Detail produk tidak dapat disimpan ke database. Pesanan tetap dibuat.";
        echo "<br>Error: " . mysqli_error($conn);
        echo "</div>";
    } else {
        echo "<div style='background:green;color:white;padding:10px;margin:10px;border-radius:5px;'>";
        echo "✅ Detail produk berhasil disimpan.";
        echo "</div>";
    }

    /* Arahkan ke pembayaran */
    header("Location: pembayaran.php?pesanan_id=$pesanan_id&total=$total");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Checkout</title>

<style>
body{
    font-family:Poppins;
    background:linear-gradient(135deg,#6DD5FA,#FF9A9E);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}
.card{
    background:#fff;
    padding:30px;
    border-radius:18px;
    width:420px;
    box-shadow:0 15px 30px rgba(0,0,0,.2);
}
.card img{
    width:100%;
    border-radius:12px;
}
.btn{
    width:100%;
    margin-top:15px;
    padding:12px;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
    border-radius:10px;
    border:none;
    cursor:pointer;
    font-size:15px;
}
textarea{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #ddd;
    margin-top:8px;
    resize:none;
}
label{
    font-weight:600;
    display:block;
    margin-top:15px;
}
</style>
</head>

<body>

<div class="card">
    <h2><?= $p['nama_produk']; ?></h2>
    <img src="../assets/produk/<?= $p['gambar']; ?>">
    <p><?= $p['deskripsi']; ?></p>
    <p><b>Harga:</b> Rp <?= number_format($p['harga']); ?></p>

    <form method="POST">

        <!-- ✅ ALAMAT PENGIRIMAN -->
        <label>Alamat Pengiriman</label>
        <textarea name="alamat" required
        placeholder="Masukkan alamat lengkap pengiriman"></textarea>

        <button type="submit" name="checkout" class="btn">
            Checkout & Bayar
        </button>

    </form>
</div>

</body>
</html>
