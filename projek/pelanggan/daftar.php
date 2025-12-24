<?php
require_once dirname(__DIR__) . "/config/koneksi.php";


if (isset($_POST['daftar'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $alamat   = $_POST['alamat'];

    mysqli_query($conn, "INSERT INTO users
    (username,password,role,nama_lengkap,email,alamat)
    VALUES
    ('$username','$password','costomer','$nama','$email','$alamat')");

    echo "<script>alert('Pendaftaran berhasil');location='login.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Daftar Akun</title>
<style>
body{
    background:linear-gradient(135deg,#6dd5fa,#ff9a9e);
    font-family:Poppins,sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
.card{
    background:#fff;
    width:400px;
    padding:30px;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,.2);
}
h2{text-align:center;color:#ff4d88;}
input,textarea{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #ddd;
}
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#6dd5fa,#ff4d88);
    color:#fff;
    font-size:16px;
    cursor:pointer;
}
p{text-align:center;margin-top:10px;}
a{color:#6dd5fa;font-weight:600;text-decoration:none;}
</style>
</head>
<body>

<div class="card">
<h2>Daftar Akun</h2>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<input type="text" name="nama" placeholder="Nama Lengkap" required>
<input type="email" name="email" placeholder="Email" required>
<textarea name="alamat" placeholder="Alamat"></textarea>
<button name="daftar">Daftar</button>
</form>
<p>Sudah punya akun? <a href="login.php">Login</a></p>
</div>

</body>
</html>
