<?php
include "conexion.php";

if (!isset($_GET['id_presupuesto'])) {
    die("Presupuesto no válido");
}

$id_presupuesto = intval($_GET['id_presupuesto']);

$presupuesto = $conn->query("
    SELECT p.*,
           CONCAT(pa.nombre,' ',pa.apellido) AS paciente,
           pa.seguro
    FROM presupuestos_ars p
    JOIN pacientes pa ON pa.id_paciente = p.id_paciente
    WHERE p.id_presupuesto = $id_presupuesto
")->fetch_assoc();

if (!$presupuesto) {
    die("Presupuesto no encontrado");
}

$detalles = $conn->query("
    SELECT *
    FROM presupuestos_ars_detalle
    WHERE id_presupuesto = $id_presupuesto
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Presupuesto ARS</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f8;
}
.contenedor{
    width:950px;
    margin:20px auto;
    background:white;
    padding:25px;
    border:1px solid #ccc;
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
.encabezado h2{
    color:#00a8c6;
    margin:5px 0;
}

.info{
    margin:15px 0;
    font-size:14px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
th{
    background:#8ec6f0;
    padding:8px;
    border:1px solid #999;
}
td{
    border:1px solid #999;
    padding:8px;
    text-align:center;
}
.left{
    text-align:left;
}
.totales{
    background:#8ec6f0;
    font-weight:bold;
}
.acciones{
    margin-top:25px;
    text-align:center;
}
.btn{
    padding:10px 20px;
    background:green;
    color:white;
    text-decoration:none;
    border-radius:5px;
}
.btn-disabled{
    background:gray;
    color:white;
    padding:10px 20px;
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

    <!-- 🔹 LOGO CENTRADO -->
    <div class="encabezado">
        <img src="img/logo.png" class="logo">
        
        <strong>Fecha:</strong> <?= date("d/m/Y", strtotime($presupuesto['fecha'])); ?>
    </div>

    <div class="info">
        <strong>Nombre Paciente:</strong> <?= $presupuesto['paciente']; ?><br>
        <strong>Seguro Médico:</strong> <?= $presupuesto['seguro']; ?>
    </div>

    <table>
        <tr>
            <th>CANT.</th>
            <th>PROCEDIMIENTO</th>
            <th>PRECIO UNIT.</th>
            <th>PRECIO TOTAL</th>
            <th>COBERTURA ARS</th>
            <th>TOTAL A PAGAR</th>
        </tr>

        <?php while($d = $detalles->fetch_assoc()) {

            $cantidad = intval($d['cantidad']);
            if ($cantidad <= 0) $cantidad = 1;

            $precio_total = floatval($d['precio']);
            $precio_unit  = $precio_total / $cantidad;
        ?>
        <tr>
            <td><?= $cantidad ?></td>
            <td class="left"><?= $d['procedimiento']; ?></td>
            <td>RD$ <?= number_format($precio_unit, 2); ?></td>
            <td>RD$ <?= number_format($precio_total, 2); ?></td>
            <td>RD$ <?= number_format($d['cobertura'], 2); ?></td>
            <td>RD$ <?= number_format($d['total'], 2); ?></td>
        </tr>
        <?php } ?>

        <tr class="totales">
            <td colspan="3">TOTALES RD</td>
            <td>RD$ <?= number_format($presupuesto['total_servicio'], 2); ?></td>
            <td>RD$ <?= number_format($presupuesto['total_cobertura'], 2); ?></td>
            <td>RD$ <?= number_format($presupuesto['total_paciente'], 2); ?></td>
        </tr>
    </table>

    <div class="acciones">
        <?php if ($presupuesto['estado'] == 'pendiente') { ?>
            <a class="btn"
               href="facturar_presupuesto_ars.php?id_presupuesto=<?= $id_presupuesto ?>"
               onclick="return confirm('¿Deseas facturar este presupuesto?')">
               FACTURAR PRESUPUESTO
            </a>
        <?php } else { ?>
            <span class="btn-disabled">YA FACTURADO</span>
        <?php } ?>
        &nbsp;
        <button onclick="window.print()">🖨 Imprimir</button>
    </div>

</div>

</body>
</html>
