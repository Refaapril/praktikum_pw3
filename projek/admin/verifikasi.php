<?php
session_start();
require_once "../config/koneksi.php";

if (!isset($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}

$pesanan_id = $_GET['id'];

// Update status pesanan menjadi 'selesai'
$query = "UPDATE pesanan SET status_pesanan = 'selesai' WHERE pesanan_id = '$pesanan_id'";
if (mysqli_query($conn, $query)) {
    echo "<script>
        alert('Pesanan berhasil diverifikasi dan diselesaikan!');
        window.location.href = 'kelola_pembayaran.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal verifikasi pesanan: " . mysqli_error($conn) . "');
        window.location.href = 'kelola_pembayaran.php';
    </script>";
}
?>
