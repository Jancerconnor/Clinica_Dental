<?php
include "conexion.php";
include "header.php";

if (!isset($_GET['id_presupuesto'])) {
    die("Presupuesto no válido");
}

$id_presupuesto = intval($_GET['id_presupuesto']);

/* DATOS PRESUPUESTO Y PACIENTE */
$pres = $conn->query("
    SELECT p.fecha, p.total, p.cobertura_ars,
           pa.nombre, pa.apellido, pa.seguro
    FROM presupuestos p
    JOIN pacientes pa ON p.id_paciente = pa.id_paciente
    WHERE p.id_presupuesto = $id_presupuesto
")->fetch_assoc();

if (!$pres) {
    die("Presupuesto no encontrado");
}

/* DETALLES */
$detalles = $conn->query("
    SELECT procedimiento, cantidad, precio_unitario, precio_total
    FROM detalle_presupuesto
    WHERE id_presupuesto = $id_presupuesto
");

/* TOTALES */
$total_servicio = floatval($pres['total']);
$cobertura_ars  = floatval($pres['cobertura_ars'] ?? 0);

if ($cobertura_ars > $total_servicio) {
    $cobertura_ars = $total_servicio;
}

$diferencia_pagar = $total_servicio - $cobertura_ars;
?>

<h2>Presupuesto</h2>

<div class="info-box">
    <strong>Paciente:</strong> <?= $pres['nombre']." ".$pres['apellido']; ?><br>
    <strong>Fecha:</strong> <?= date("d/m/Y", strtotime($pres['fecha'])); ?><br>
    <strong>Seguro:</strong> <?= $pres['seguro']; ?>
</div>

<table class="tabla-presupuesto">
    <tr>
        <th>PROCEDIMIENTO</th>
        <th>CANTIDAD</th>
        <th>PRECIO UNITARIO</th>
        <th>PRECIO TOTAL</th>
    </tr>

<?php while ($row = $detalles->fetch_assoc()): ?>
    <tr>
        <td><?= $row['procedimiento']; ?></td>
        <td><?= $row['cantidad']; ?></td>
        <td>RD$ <?= number_format($row['precio_unitario'], 2); ?></td>
        <td>RD$ <?= number_format($row['precio_total'], 2); ?></td>
    </tr>
<?php endwhile; ?>

    <tr>
        <th colspan="3">TOTAL DEL SERVICIO</th>
        <th>RD$ <?= number_format($total_servicio, 2); ?></th>
    </tr>
</table>

<div class="acciones">
    <a href="generar_factura.php?id_presupuesto=<?= $id_presupuesto; ?>"
       onclick="return confirm('¿Deseas generar la factura de este presupuesto?');">
        <button>🧾 Generar Factura</button>
    </a>

    <a href="historial_facturas.php">
        <button>📜 Historial de Facturas</button>
    </a>

    <button onclick="window.print()">🖨 Imprimir</button>
</div>

<?php include "footer.php"; ?>
