<?php
session_start();
require_once "../config/koneksi.php";

if (isset($_POST['simpan'])) {

    $nama   = $_POST['nama_produk'];
    $harga  = $_POST['harga'];
    $stok   = $_POST['stok'];
    $desk   = $_POST['deskripsi'];

    /* ===== UPLOAD GAMBAR OTOMATIS ===== */
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $gambar_baru = "produk_" . time() . "." . $ext;

    $tmp    = $_FILES['gambar']['tmp_name'];
    $folder = "../assets/produk/";

    move_uploaded_file($tmp, $folder.$gambar_baru);

    /* Simpan ke database */
    mysqli_query($conn, "INSERT INTO produk
    (nama_produk, deskripsi, harga, stok, gambar, created_at)
    VALUES
    ('$nama','$desk','$harga','$stok','$gambar_baru',NOW())");

    echo "<script>alert('Produk berhasil ditambahkan');location='tambah_produk.php';</script>";
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Tambah Produk</title>

<style>
body{
    margin:0;
    font-family:Poppins,sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#6DD5FA,#FF9A9E);
}
.card{
    background:#fff;
    padding:30px;
    width:420px;
    border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.2);
}
h2{
    text-align:center;
    color:#FF4D88;
}
input,textarea{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:10px;
    border:1px solid #ddd;
}
button{
    width:100%;
    margin-top:15px;
    padding:12px;
    border:none;
    border-radius:10px;
    font-size:16px;
    color:#fff;
    cursor:pointer;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
}
</style>
</head>

<body>

<div class="card">
<h2>Tambah Produk</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nama_produk" placeholder="Nama Produk" required>
    <textarea name="deskripsi" placeholder="Deskripsi Produk"></textarea>
    <input type="number" name="harga" placeholder="Harga" required>
    <input type="number" name="stok" placeholder="Stok" required>
    <input type="file" name="gambar" required>
    <button name="simpan">Simpan Produk</button>
</form>
</div>

</body>
</html>
