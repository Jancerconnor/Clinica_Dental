<?php
include "conexion.php";
include_once "csrf.php";
csrf_require_valid_post();

if (!isset($_POST['id_presupuesto'])) {
    die("Presupuesto no válido");
}

$id_presupuesto = intval($_POST['id_presupuesto']);

/* OBTENER DATOS DEL PRESUPUESTO */
$pres = $conn->query("
    SELECT id_paciente, id_cita, total
    FROM presupuestos
    WHERE id_presupuesto = $id_presupuesto
")->fetch_assoc();

if (!$pres) {
    die("Presupuesto no encontrado");
}

/* VERIFICAR SI YA ESTÁ FACTURADO */
$existe = $conn->query("
    SELECT id_factura
    FROM facturas
    WHERE id_presupuesto = $id_presupuesto
")->fetch_assoc();

if ($existe) {
    header("Location: historial_facturas.php");
    exit;
}

/* GENERAR FACTURA */
$codigo = 'FAC' . date('YmdHis');

$conn->query("
    INSERT INTO facturas (id_paciente, codigo, fecha, total, id_presupuesto)
    VALUES ({$pres['id_paciente']}, '$codigo', CURDATE(), {$pres['total']}, $id_presupuesto)
");

$id_factura = $conn->insert_id;

/* COPIAR DETALLES (CON PRECIO TOTAL) */
$detalles = $conn->query("
    SELECT procedimiento, cantidad, precio_unitario, precio_total
    FROM detalle_presupuesto
    WHERE id_presupuesto = $id_presupuesto
");

while ($row = $detalles->fetch_assoc()) {

    $precio_total = $row['precio_total'];

    $conn->query("
        INSERT INTO factura_detalle
        (id_factura, procedimiento, precio, cantidad, precio_total)
        VALUES
        ($id_factura, '{$row['procedimiento']}', {$row['precio_unitario']}, {$row['cantidad']}, $precio_total)
    ");
}

/* MARCAR CITA COMO FACTURADA */
$conn->query("
    UPDATE citas
    SET estado = 'facturada'
    WHERE id_cita = {$pres['id_cita']}
");

/* 🔁 REDIRIGIR AL HISTORIAL */
header("Location: historial_facturas.php");
exit;
