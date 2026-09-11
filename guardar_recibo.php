<?php
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acceso no permitido");
}

$codigo_recibo  = $_POST['codigo_recibo'];
$nombre_cliente = $_POST['nombre_cliente'];
$fecha          = $_POST['fecha'];
$monto_numero   = $_POST['monto_numero'];
$monto_letras   = $_POST['monto_letras'];
$cargo          = $_POST['cargo'];
$concepto       = $_POST['concepto'];
$metodo_pago    = $_POST['metodo_pago'];

/* GUARDAR RECIBO */
$stmt = $conn->prepare("
    INSERT INTO recibos 
    (codigo_recibo, nombre_cliente, fecha, monto_numero, monto_letras, cargo, concepto, metodo_pago)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssdssss",
    $codigo_recibo,
    $nombre_cliente,
    $fecha,
    $monto_numero,
    $monto_letras,
    $cargo,
    $concepto,
    $metodo_pago
);

$stmt->execute();

/* OBTENER ID DEL RECIBO */
$id_recibo = $stmt->insert_id;

$stmt->close();

/* REDIRIGIR */
header("Location: ver_recibo.php?id_recibo=$id_recibo");
exit;
