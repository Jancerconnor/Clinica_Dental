<?php
include "conexion.php";

if (!isset($_GET['id_factura'])) {
    die("Factura no válida");
}

$id_factura = intval($_GET['id_factura']);

/* eliminar detalles */
$conn->query("
    DELETE FROM factura_detalle_ars
    WHERE id_factura = $id_factura
");

/* eliminar factura */
$conn->query("
    DELETE FROM facturas_ars
    WHERE id_factura = $id_factura
");

header("Location: historial_facturas_ars.php");
exit;
