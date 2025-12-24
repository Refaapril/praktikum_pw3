<?php
session_start();
require_once dirname(__DIR__) . "/config/koneksi.php";



if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $user  = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nama']    = $user['nama_lengkap'];
        header("Location: beranda.php");
        exit;
    } else {
        echo "<script>alert('Username atau password salah');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Pelanggan</title>

<style>
/* ===== TEMA BIRU – PINK ===== */
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#6DD5FA,#FF9A9E);
}

.card{
    background:#ffffff;
    width:380px;
    padding:30px;
    border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.18);
    animation:fade 0.8s ease;
}

h2{
    text-align:center;
    color:#FF4D88;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:14px;
    border-radius:10px;
    border:1px solid #EAEAEA;
    font-size:14px;
}

input:focus{
    outline:none;
    border-color:#6DD5FA;
    box-shadow:0 0 6px rgba(109,213,250,.5);
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    color:#fff;
    background:linear-gradient(135deg,#6DD5FA,#FF4D88);
}

button:hover{
    opacity:.95;
}

p{
    text-align:center;
    margin-top:12px;
    color:#555;
}

a{
    color:#6DD5FA;
    font-weight:600;
    text-decoration:none;
}

@keyframes fade{
    from{opacity:0; transform:translateY(15px);}
    to{opacity:1; transform:translateY(0);}
}
</style>
</head>

<body>

<div class="card">
    <h2>Login Pelanggan</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Belum punya akun? <a href="daftar.php">Daftar</a></p>
</div>

</body>
</html>
