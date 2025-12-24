<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_SESSION['owner_login'])) {
    header("Location: login.php");
    exit;
}

/* Total Pesanan */
$total = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total FROM pesanan"
))['total'];

/* Total Pendapatan */
$pendapatan = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(total_bayar) AS total 
     FROM pesanan 
     WHERE status_pesanan != 'pending'"
))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Owner</title>
<style>
body{
    font-family:Poppins,sans-serif;
    background:#f7faff;
    padding:30px;
}
.card{
    background:#fff;
    padding:20px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.12);
    margin-bottom:20px;
}
h2{color:#FF4D88;}
a{
    display:inline-block;
    margin-top:15px;
    padding:10px 20px;
    background:#FF4D88;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
}
</style>
</head>
<body>

<h2>Dashboard Owner</h2>

<div class="card">
    <p>Total Pesanan: <strong><?= $total ?></strong></p>
    <p>Total Pendapatan: <strong>Rp <?= number_format($pendapatan ?? 0) ?></strong></p>
</div>

<a href="kelola_laporan.php">📊 Kelola Laporan</a>
<a href="logout.php" style="background:#e74c3c">Logout</a>

</body>
</html>
