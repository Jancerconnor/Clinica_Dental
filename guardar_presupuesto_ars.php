<?php
include "conexion.php";
include_once "csrf.php";
csrf_require_valid_post();

/* ===========================
   VALIDAR DATOS
=========================== */
if (
    !isset($_POST['id_paciente']) ||
    !isset($_POST['id_cita']) ||
    !isset($_POST['procedimiento']) ||
    !isset($_POST['cantidad']) ||
    !isset($_POST['precio']) ||
    !isset($_POST['ars'])
) {
    die("Datos incompletos");
}

$id_paciente = intval($_POST['id_paciente']);
$id_cita     = intval($_POST['id_cita']);
$fecha       = date("Y-m-d");

/* ===========================
   ARRAYS DEL FORMULARIO
=========================== */
$procedimientos = $_POST['procedimiento'];
$cantidades     = $_POST['cantidad'];
$precios        = $_POST['precio'];
$coberturas     = $_POST['ars'];

/* ===========================
   TOTALES
=========================== */
$total_servicio  = 0; // suma precios totales
$total_cobertura = 0; // suma ARS
$total_paciente  = 0; // lo que paga el paciente

/* ===========================
   INSERTAR PRESUPUESTO VACÍO
=========================== */
$conn->query("
    INSERT INTO presupuestos_ars
    (id_paciente, id_cita, fecha, total_servicio, total_cobertura, total_paciente, estado)
    VALUES
    ($id_paciente, $id_cita, '$fecha', 0, 0, 0, 'pendiente')
");

$id_presupuesto = $conn->insert_id;

if ($id_presupuesto <= 0) {
    die("Error al crear el presupuesto");
}

/* ===========================
   DETALLE + CÁLCULOS
=========================== */
for ($i = 0; $i < count($procedimientos); $i++) {

    if (trim($procedimientos[$i]) == "") continue;

    $proc     = $conn->real_escape_string($procedimientos[$i]);
    $cantidad = intval($cantidades[$i]);
    $precio   = floatval($precios[$i]);
    $ars      = floatval($coberturas[$i]);

    if ($cantidad <= 0) $cantidad = 1;

    // precio total = precio unitario * cantidad
    $precio_total = $precio * $cantidad;

    // ARS no puede ser mayor que el precio total
    if ($ars > $precio_total) $ars = $precio_total;

    $paga = $precio_total - $ars;

    $total_servicio  += $precio_total;
    $total_cobertura += $ars;
    $total_paciente  += $paga;

    $conn->query("
        INSERT INTO presupuestos_ars_detalle
        (id_presupuesto, procedimiento, cantidad, precio, cobertura, total)
        VALUES
        ($id_presupuesto, '$proc', $cantidad, $precio_total, $ars, $paga)
    ");
}

/* ===========================
   ACTUALIZAR TOTALES
=========================== */
$conn->query("
    UPDATE presupuestos_ars SET
        total_servicio  = $total_servicio,
        total_cobertura = $total_cobertura,
        total_paciente  = $total_paciente
    WHERE id_presupuesto = $id_presupuesto
");

/* ===========================
   MARCAR CITA COMO ATENDIDA
=========================== */
$conn->query("
    UPDATE citas
    SET estado = 'atendida'
    WHERE id_cita = $id_cita
");

/* ===========================
   REDIRECCIÓN
=========================== */
header("Location: ver_presupuesto_ars.php?id_presupuesto=$id_presupuesto");
exit;
