<?php
session_start();
require_once "../config/koneksi.php";
/* Cek login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* Ambil total dan pesanan_id dari checkout */
$total = $_GET['total'] ?? 0;
$pesanan_id = $_GET['pesanan_id'] ?? 0;

if (!$pesanan_id) {
    die("Pesanan tidak valid");
}

/* Proses pembayaran */
if (isset($_POST['bayar'])) {

    $user_id = $_SESSION['user_id'];
    $metode  = $_POST['metode'];

    /* Upload bukti */
    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $bukti_baru = "bukti_" . time() . "." . $ext;

    move_uploaded_file(
        $_FILES['bukti']['tmp_name'],
        "../assets/bukti/" . $bukti_baru
    );

    mysqli_query($conn, "INSERT INTO pembayaran
    (pesanan_id, user_id, metode, bukti_pembayaran, created_at)
    VALUES
    ('$pesanan_id','$user_id','$metode','$bukti_baru',NOW())");

    echo "<script>
        alert('✅ Pembayaran berhasil dikirim! Anda akan diarahkan ke nota pembelian.');
        location='nota.php?pesanan_id=$pesanan_id';
    </script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran</title>

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:#f7faff;
}
.navbar{
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    padding:15px 30px;
    color:#fff;
    display:flex;
    justify-content:space-between;
}
.container{
    max-width:500px;
    margin:50px auto;
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
}
h2{
    text-align:center;
    color:#FF4D88;
}
.total{
    font-size:22px;
    text-align:center;
    margin:20px 0;
    color:#6DD5FA;
    font-weight:600;
}
label{
    display:block;
    margin-top:15px;
    font-weight:500;
}
select,input[type=file]{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:10px;
    border:1px solid #ddd;
}
button{
    width:100%;
    margin-top:25px;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
    font-size:16px;
    cursor:pointer;
}
button:hover{opacity:.9;}
</style>
</head>

<body>

<div class="navbar">
    <b>Skincare Shop</b>
    <span><?= $_SESSION['nama']; ?></span>
</div>

<div class="container">
    <h2>Pembayaran</h2>

    <div class="total">
        Total Bayar: Rp <?= number_format($total); ?>
    </div>

    <form method="POST" enctype="multipart/form-data">

        <label>Metode Pembayaran</label>
        <select name="metode" required>
            <option value="">-- Pilih Metode --</option>
            <option value="Transfer Bank">Transfer Bank</option>
            <option value="E-Wallet">E-Wallet</option>
            <option value="COD">COD</option>
        </select>

        <label>Upload Bukti Pembayaran</label>
        <input type="file" name="bukti" required>

        <button type="submit" name="bayar">
            Kirim Pembayaran
        </button>

    </form>
</div>

</body>
</html>
