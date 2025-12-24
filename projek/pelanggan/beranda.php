<?php
session_start();
require_once "../config/koneksi.php";

/* Cek login */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* Ambil data produk */
$query = mysqli_query($conn, "SELECT * FROM produk ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Beranda | Skincare Shop</title>

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background:#f7faff;
}

/* Navbar */
.navbar{
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    padding:15px 30px;
    color:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.navbar a{
    color:#fff;
    text-decoration:none;
    margin-left:15px;
}

/* Hero */
.hero{
    background:linear-gradient(135deg,#6DD5FA,#FF9A9E);
    padding:60px 20px;
    color:#fff;
    text-align:center;
}
.hero h2{margin:0;font-size:32px;}

/* Produk */
.container{
    max-width:1200px;
    margin:auto;
    padding:40px 20px;
}
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:25px;
}
.card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
    overflow:hidden;
    transition:.3s;
}
.card:hover{transform:translateY(-6px);}
.card img{
    width:100%;
    height:200px;
    object-fit:cover;
}
.card-body{
    padding:18px;
}
.card-body h3{
    margin:0;
    color:#FF4D88;
}
.price{
    color:#6DD5FA;
    font-weight:600;
    margin:8px 0;
}
.stok{
    font-size:13px;
    color:#777;
}
.btn{
    display:inline-block;
    margin-top:10px;
    padding:10px 15px;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
    border-radius:10px;
    text-decoration:none;
    font-size:14px;
}
.btn.disabled{
    background:#ccc;
    pointer-events:none;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <b>Skincare Shop</b>
    <div>
        Hai, <?= $_SESSION['nama']; ?> |
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h2>Produk Skincare Terbaik</h2>
    <p>Rawat kulitmu mulai hari ini ✨</p>
</div>

<!-- PRODUK -->
<div class="container">
    <div class="grid">
    <?php while($p = mysqli_fetch_assoc($query)) { ?>
        <div class="card">
            <img src="../assets/produk/<?= $p['gambar']; ?>" alt="<?= $p['nama_produk']; ?>">
            <div class="card-body">
                <h3><?= $p['nama_produk']; ?></h3>
                <div class="price">Rp <?= number_format($p['harga']); ?></div>
                <div class="stok">Stok: <?= $p['stok']; ?></div>
                <p><?= $p['deskripsi']; ?></p>

                <?php if ($p['stok'] > 0) { ?>
                    <a href="checkout.php?id=<?= $p['produk_id']; ?>" class="btn">Checkout</a>
                <?php } else { ?>
                    <span class="btn disabled">Stok Habis</span>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    </div>
</div>

</body>
</html>
