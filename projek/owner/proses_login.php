<?php
session_start();
require_once "../config/koneksi.php";

/* Cek apakah form dikirim */
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: login.php");
    exit;
}

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']); // samakan dengan password di DB

$query = mysqli_query($conn, "
    SELECT * FROM owner 
    WHERE username = '$username' 
    AND password = '$password'
");

if (mysqli_num_rows($query) === 1) {
    $owner = mysqli_fetch_assoc($query);

    $_SESSION['owner_id'] = $owner['owner_id'];
    $_SESSION['owner_username'] = $owner['username'];
    $_SESSION['owner_login'] = true;

    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
