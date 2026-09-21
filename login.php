<?php
session_start();
include_once "csrf.php";

/* Si ya hay sesión, mandar al dashboard */
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - Clínica Dental</title>

<style>
body{
    font-family: Arial;
    background: linear-gradient(135deg,#1e90ff,#0a3d62);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-box{
    background:white;
    padding:30px;
    width:350px;
    border-radius:10px;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
    text-align:center;
}

.login-box h2{
    margin-bottom:20px;
    color:#0a3d62;
}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
    border-radius:5px;
    border:1px solid #ccc;
}

button{
    width:100%;
    padding:10px;
    background:#1e90ff;
    border:none;
    color:white;
    font-size:16px;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#0b5ed7;
}

.error{
    color:red;
    margin-bottom:10px;
}
</style>
</head>

<body>

<div class="login-box">

    <h2>🔐 Iniciar Sesión</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">Usuario o contraseña incorrectos</div>
    <?php endif; ?>

    <form action="validar_login.php" method="POST">
        <?= csrf_field(); ?>
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>

</div>

</body>
</html>
