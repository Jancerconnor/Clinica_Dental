<?php
session_start();
include_once "csrf.php";
include "conexion.php";

csrf_require_valid_post();

$usuario  = trim($_POST['usuario']);
$password = trim($_POST['password']);

/* Buscar usuario */
$stmt = $conn->prepare("
    SELECT id_usuario, usuario, password 
    FROM usuarios 
    WHERE usuario = ?
    LIMIT 1
");

$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

/* Verificar si existe */
if ($result && $result->num_rows === 1) {

    $row = $result->fetch_assoc();

    /* Comparar contraseña */
    if (password_verify($password, $row['password'])) {

        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['usuario']    = $row['usuario'];

        header("Location: index.php");
        exit;
    }
}

/* Si falla */
header("Location: login.php?error=1");
exit;
