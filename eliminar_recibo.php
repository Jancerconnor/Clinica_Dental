<?php
include "conexion.php";
include_once "csrf.php";
csrf_require_valid_post();

if (!isset($_POST['id_recibo'])) {
    die("Recibo no válido");
}

$id_recibo = intval($_POST['id_recibo']);

/* ELIMINAR RECIBO */
$stmt = $conn->prepare("DELETE FROM recibos WHERE id_recibo = ?");
$stmt->bind_param("i", $id_recibo);
$stmt->execute();
$stmt->close();

/* VOLVER AL HISTORIAL */
header("Location: historial_recibos.php");
exit;
