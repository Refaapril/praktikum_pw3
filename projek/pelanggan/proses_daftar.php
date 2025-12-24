<?php
require_once dirname(__DIR__) . "/config/koneksi.php";


$username=$_POST['username'];
$password=password_hash($_POST['password'],PASSWORD_DEFAULT);
$nama=$_POST['nama'];
$email=$_POST['email'];
$alamat=$_POST['alamat'];

mysqli_query($conn,"INSERT INTO users
(username,password,role,nama_lengkap,email,alamat)
VALUES
('$username','$password','costomer','$nama','$email','$alamat')");

header("Location: login.php");
?>
