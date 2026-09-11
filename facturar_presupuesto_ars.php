<?php
include "conexion.php";

if (!isset($_GET['id_presupuesto'])) {
    die("Presupuesto no válido");
}

$id_presupuesto = intval($_GET['id_presupuesto']);
$fecha = date("Y-m-d");

/* ============================
   OBTENER PRESUPUESTO
============================ */
$presupuesto = $conn->query("
    SELECT * 
    FROM presupuestos_ars
    WHERE id_presupuesto = $id_presupuesto
")->fetch_assoc();

if (!$presupuesto) {
    die("Presupuesto no encontrado");
}

if ($presupuesto['estado'] === 'facturado') {
    die("Este presupuesto ya fue facturado");
}

/* ============================
   CREAR FACTURA ARS
============================ */
$numero_factura = 'ARS-' . time();

$conn->query("
    INSERT INTO facturas_ars
    (id_presupuesto, fecha, total, numero_factura)
    VALUES
    ($id_presupuesto, '$fecha', {$presupuesto['total_paciente']}, '$numero_factura')
");

$id_factura = $conn->insert_id;

if ($id_factura <= 0) {
    die("Error al crear la factura");
}

/* ============================
   OBTENER DETALLES PRESUPUESTO
============================ */
$detalles = $conn->query("
    SELECT *
    FROM presupuestos_ars_detalle
    WHERE id_presupuesto = $id_presupuesto
");

/* ============================
   INSERTAR DETALLE FACTURA
============================ */
while ($d = $detalles->fetch_assoc()) {

    $procedimiento = $conn->real_escape_string($d['procedimiento']);
    $cantidad      = intval($d['cantidad']);
    $precio_total  = floatval($d['precio']); // precio total (cantidad × unitario)
    $subtotal      = floatval($d['total']);  // lo que paga el paciente

    $conn->query("
        INSERT INTO factura_detalle_ars
        (id_factura, procedimiento, cantidad, precio, subtotal)
        VALUES
        ($id_factura, '$procedimiento', $cantidad, $precio_total, $subtotal)
    ");
}

/* ============================
   MARCAR PRESUPUESTO FACTURADO
============================ */
$conn->query("
    UPDATE presupuestos_ars
    SET estado = 'facturado'
    WHERE id_presupuesto = $id_presupuesto
");

/* ============================
   REDIRIGIR A LA FACTURA
============================ */
header("Location: ver_factura_ars.php?id_factura=$id_factura");
exit;
