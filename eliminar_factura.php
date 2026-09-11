<?php
include "conexion.php";

if (!isset($_GET['id_factura'])) {
    die("Factura no válida");
}

$id_factura = intval($_GET['id_factura']);

/* ELIMINAR DETALLE DE FACTURA */
$conn->query("
    DELETE FROM factura_detalle
    WHERE id_factura = $id_factura
");

/* ELIMINAR FACTURA */
$conn->query("
    DELETE FROM facturas
    WHERE id_factura = $id_factura
");

/* REDIRIGIR AL HISTORIAL */
header("Location: historial_facturas.php");
exit;
