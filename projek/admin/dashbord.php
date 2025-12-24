<?php
session_start();
require_once "../config/koneksi.php";

/* (Opsional) cek admin login */
// if (!isset($_SESSION['admin'])) {
//     header("Location: login.php");
//     exit;
// }

/* Hitung data */
$produk = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM produk"));
$pesanan = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pesanan"));
$pembayaran = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM pembayaran"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>

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
    font-size:18px;
}
.container{
    padding:40px;
}
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
}
.card{
    background:#fff;
    border-radius:20px;
    padding:30px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
    text-align:center;
}
.card h2{
    margin:0;
    font-size:40px;
    color:#FF4D88;
}
.card p{
    margin:10px 0 0;
    font-size:18px;
    color:#555;
}
.menu{
    margin-top:40px;
    display:flex;
    gap:20px;
}
.menu a{
    padding:14px 25px;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
    text-decoration:none;
    border-radius:14px;
    font-weight:500;
}
.menu a:hover{opacity:.9;}
</style>
</head>

<body>

<div class="navbar">
    Dashboard Admin Skincare Shop
</div>

<div class="container">

    <div class="grid">
        <div class="card">
            <h2><?= $produk; ?></h2>
            <p>Produk</p>
        </div>

        <div class="card">
            <h2><?= $pesanan; ?></h2>
            <p>Pesanan</p>
        </div>

        <div class="card">
            <h2><?= $pembayaran; ?></h2>
            <p>Pembayaran</p>
        </div>
    </div>

    <div class="menu">
        <a href="kelola_pembayaran.php">Kelola Pembayaran</a>
        <a href="pesanan.php">Kelola Pesanan</a>
        <a href="tambah_produk.php">Tambah Produk</a>
        <a href="kelola_laporan.php">Laporan Penjualan</a>
    </div>

</div>

</body>
</html>
