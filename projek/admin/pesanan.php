<?php
session_start();
require_once "../config/koneksi.php";

/* Ambil data pesanan + pembayaran */
$data = mysqli_query($conn, "
SELECT 
    p.pesanan_id,
    p.no_pesanan,
    p.tanggal_pesan,
    p.total_bayar,
    p.status_pesanan,
    p.alamat_pengiriman,
    u.nama_lengkap,
    IFNULL(b.bukti_pembayaran,'') AS bukti
FROM pesanan p
JOIN users u ON p.user_id = u.user_id
LEFT JOIN pembayaran b ON p.pesanan_id = b.pesanan_id
ORDER BY p.created_at DESC
");

/* Debug jika query error */
if (!$data) {
    die('Query Error: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Pesanan | Admin</title>

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:#f7faff;
    padding:30px;
}
h2{
    color:#FF4D88;
    margin-bottom:20px;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
}
th,td{
    padding:14px;
    text-align:center;
    font-size:14px;
}
th{
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    color:#fff;
}
tr:nth-child(even){background:#f9fbff;}
img{
    width:90px;
    border-radius:10px;
}
.status{
    padding:6px 14px;
    border-radius:20px;
    font-size:12px;
    color:#fff;
    display:inline-block;
}
.pending{background:#f39c12;}
.diproses{background:#3498db;}
.dikirim{background:#9b59b6;}
.selesai{background:#2ecc71;}

.btn{
    padding:8px 14px;
    background:#FF4D88;
    color:#fff;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
}
.btn:hover{opacity:.9;}

.alamat{
    max-width:220px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}
</style>
</head>

<body>

<h2>📦 Data Pesanan Pelanggan</h2>

<table>
<tr>
    <th>ID</th>
    <th>No Pesanan</th>
    <th>Pelanggan</th>
    <th>Tanggal</th>
    <th>Total Bayar</th>
    <th>Alamat</th>
    <th>Bukti Pembayaran</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php if(mysqli_num_rows($data) > 0){ ?>
<?php while($r = mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?= $r['pesanan_id']; ?></td>
    <td><?= $r['no_pesanan']; ?></td>
    <td><?= $r['nama_lengkap']; ?></td>
    <td><?= date('d-m-Y', strtotime($r['tanggal_pesan'])); ?></td>
    <td>Rp <?= number_format($r['total_bayar']); ?></td>
    <td class="alamat" title="<?= $r['alamat_pengiriman']; ?>">
        <?= $r['alamat_pengiriman']; ?>
    </td>
    <td>
        <?php if($r['bukti']){ ?>
            <img src="../assets/bukti/<?= $r['bukti']; ?>" alt="Bukti Pembayaran">
        <?php } else { ?>
            -
        <?php } ?>
    </td>
    <td>
        <span class="status <?= $r['status_pesanan']; ?>">
            <?= ucfirst($r['status_pesanan']); ?>
        </span>
    </td>
    <td>
        <a href="verifikasi.php?id=<?= $r['pesanan_id']; ?>" class="btn">
            Verifikasi
        </a>
    </td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
    <td colspan="9">Belum ada data pesanan</td>
</tr>
<?php } ?>

</table>

</body>
</html>
