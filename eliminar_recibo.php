<?php
include "conexion.php";

if (!isset($_GET['id_recibo'])) {
    die("Recibo no válido");
}

$id_recibo = intval($_GET['id_recibo']);

/* ELIMINAR RECIBO */
$stmt = $conn->prepare("DELETE FROM recibos WHERE id_recibo = ?");
$stmt->bind_param("i", $id_recibo);
$stmt->execute();
$stmt->close();

/* VOLVER AL HISTORIAL */
header("Location: historial_recibos.php");
exit;
