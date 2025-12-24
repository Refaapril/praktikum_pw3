<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login Owner</title>

<style>
*{
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#FFD1E8,#FFF);
}
.login-box{
    background:#fff;
    padding:35px;
    width:350px;
    border-radius:18px;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}
.login-box h2{
    text-align:center;
    color:#FF4D88;
    margin-bottom:25px;
}
.login-box input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #ddd;
    outline:none;
    font-size:14px;
}
.login-box input:focus{
    border-color:#FF4D88;
}
.login-box button{
    width:100%;
    padding:12px;
    background:#FF4D88;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:15px;
    cursor:pointer;
}
.login-box button:hover{
    opacity:.9;
}
.footer{
    text-align:center;
    margin-top:15px;
    font-size:12px;
    color:#999;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>Login Owner</h2>
    <form method="POST" action="proses_login.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <div class="footer">
        Skincare Shop © 2025
    </div>
</div>

</body>
</html>
