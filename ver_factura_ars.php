<?php
include "conexion.php";

if (!isset($_GET['id_factura'])) {
    die("Factura no válida");
}

$id_factura = intval($_GET['id_factura']);

/* ==========================
   OBTENER FACTURA
========================== */
$factura = $conn->query("
    SELECT f.*, 
           CONCAT(pa.nombre,' ',pa.apellido) AS paciente
    FROM facturas_ars f
    JOIN presupuestos_ars p ON p.id_presupuesto = f.id_presupuesto
    JOIN pacientes pa ON pa.id_paciente = p.id_paciente
    WHERE f.id_factura = $id_factura
")->fetch_assoc();

if (!$factura) {
    die("Factura no encontrada");
}

/* ==========================
   DETALLES FACTURA ARS
========================== */
$detalles = $conn->query("
    SELECT procedimiento, precio, cantidad, subtotal
    FROM factura_detalle_ars
    WHERE id_factura = $id_factura
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Factura ARS</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f8;
}
.contenedor{
    width:900px;
    margin:20px auto;
    background:white;
    padding:25px;
    border:1px solid #ccc;
}
.encabezado{
    display:flex;
    align-items:center;
    gap:20px;
}
/* 🔹 ENCABEZADO CENTRADO */
.encabezado{
    text-align:center;
    margin-bottom:15px;
}
.logo{
    width:680px;
    margin-bottom:10px;
}
.info{
    margin:15px 0;
}
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#9fcaf0;
    padding:8px;
    border:1px solid #999;
}
td{
    padding:8px;
    border:1px solid #999;
    text-align:center;
}
.left{
    text-align:left;
}
.total{
    background:#9fcaf0;
    font-weight:bold;
}
.acciones{
    margin-top:25px;
    text-align:center;
}
.btn{
    padding:10px 20px;
    background:#0b78c9;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
@media print {
    .acciones {
        display:none;
    }
}
</style>
</head>

<body>

<div class="contenedor">

    <div class="encabezado">
        <img src="img/logo.png" class="logo">
        <div>
            <h2>FACTURA ARS</h2>
            <strong>N°:</strong> <?= $factura['numero_factura']; ?><br>
            <strong>Fecha:</strong> <?= date("d/m/Y", strtotime($factura['fecha'])); ?>
        </div>
    </div>

    <div class="info">
        <strong>Paciente:</strong> <?= $factura['paciente']; ?>
    </div>

    <table>
        <tr>
            <th>CANT.</th>
            <th>PROCEDIMIENTO</th>
            <th>PRECIO TOTAL</th>
            <th>PAGA PACIENTE</th>
        </tr>

        <?php while($d = $detalles->fetch_assoc()) { ?>
        <tr>
            <td><?= $d['cantidad']; ?></td>
            <td class="left"><?= $d['procedimiento']; ?></td>
            <td>RD$ <?= number_format($d['precio'], 2); ?></td>
            <td>RD$ <?= number_format($d['subtotal'], 2); ?></td>
        </tr>
        <?php } ?>

        <tr class="total">
            <td colspan="3">TOTAL A PAGAR</td>
            <td>RD$ <?= number_format($factura['total'], 2); ?></td>
        </tr>
    </table>

    <div class="acciones">
        <button onclick="window.print()">🖨 Imprimir</button>
        &nbsp;
        <a class="btn" href="historial_facturas_ars.php">Volver</a>
    </div>

</div>

</body>
</html>
