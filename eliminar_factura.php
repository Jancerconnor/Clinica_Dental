<?php
include "conexion.php";
include_once "csrf.php";
csrf_require_valid_post();

if (!isset($_POST['id_factura'])) {
    die("Factura no válida");
}

$id_factura = intval($_POST['id_factura']);

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
