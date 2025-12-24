<?php
session_start();
require_once "../config/koneksi.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "
        SELECT * FROM admin 
        WHERE username='$username' 
        AND password='$password'
    ");

    if (!$query) {
        die("Query Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($query) > 0) {
        $admin = mysqli_fetch_assoc($query);

        $_SESSION['admin'] = true;
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['nama_admin'] = $admin['nama_admin'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Admin</title>

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#f7faff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box{
    background:#fff;
    padding:35px;
    border-radius:20px;
    width:360px;
    box-shadow:0 15px 30px rgba(0,0,0,.12);
}
h2{
    text-align:center;
    color:#FF4D88;
}
input{
    width:100%;
    padding:12px;
    margin-top:15px;
    border-radius:10px;
    border:1px solid #ddd;
}
button{
    width:100%;
    margin-top:20px;
    padding:12px;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
    border:none;
    color:#fff;
    border-radius:12px;
    cursor:pointer;
}
.error{
    background:#ffe5e5;
    color:red;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    text-align:center;
}
</style>
</head>

<body>

<div class="box">
    <h2>Login Admin</h2>

    <?php if($error){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>
