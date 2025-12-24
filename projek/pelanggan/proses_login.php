<?php
session_start();
require_once dirname(__DIR__) . "/config/koneksi.php";



$u=$_POST['username'];
$p=$_POST['password'];

$q=mysqli_query($conn,"SELECT * FROM users WHERE username='$u'");
$d=mysqli_fetch_assoc($q);

if($d && password_verify($p,$d['password'])){
    $_SESSION['user_id']=$d['user_id'];
    $_SESSION['nama']=$d['nama_lengkap'];
    header("Location: beranda.php");
}else{
    echo "<script>alert('Login gagal');location='login.php';</script>";
}
?>
