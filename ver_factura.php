<?php
include "conexion.php";
include "header.php";

if (!isset($_GET['id_factura'])) {
    die("Factura no válida");
}

$id_factura = intval($_GET['id_factura']);

/* DATOS FACTURA */
$factura = $conn->query("
    SELECT 
        f.codigo,
        f.fecha,
        f.total,
        p.nombre,
        p.apellido,
        p.seguro
    FROM facturas f
    INNER JOIN pacientes p ON f.id_paciente = p.id_paciente
    WHERE f.id_factura = $id_factura
")->fetch_assoc();

if (!$factura) {
    die("Factura no encontrada");
}
?>

<h2>Factura</h2>

<div class="info-box">
    <strong>Paciente:</strong> <?= $factura['nombre']." ".$factura['apellido']; ?><br>
    <strong>Seguro:</strong> <?= $factura['seguro']; ?><br>
    <strong>Fecha:</strong> <?= date("d/m/Y", strtotime($factura['fecha'])); ?><br>
    <strong>Factura:</strong> <?= $factura['codigo']; ?>
</div>

<table class="tabla-presupuesto">
    <tr>
        <th>PROCEDIMIENTO</th>
        <th>CANTIDAD</th>
        <th>PRECIO UNITARIO</th>
        <th>SUBTOTAL</th>
    </tr>

<?php
$detalles = $conn->query("
    SELECT procedimiento, precio, cantidad, precio_total
    FROM factura_detalle
    WHERE id_factura = $id_factura
");

while ($row = $detalles->fetch_assoc()):
    $subtotal = $row['precio_total'];
?>
    <tr>
        <td><?= $row['procedimiento']; ?></td>
        <td><?= $row['cantidad']; ?></td>
        <td>RD$ <?= number_format($row['precio'], 2); ?></td>
        <td>RD$ <?= number_format($subtotal, 2); ?></td>
    </tr>
<?php endwhile; ?>

    <!-- TOTAL FINAL -->
    <tr>
        <th colspan="3">TOTAL A PAGAR</th>
        <th>RD$ <?= number_format($factura['total'], 2); ?></th>
    </tr>
</table>

<div class="acciones">
    <button onclick="window.print()">🖨 Imprimir</button>

    <a href="historial_facturas.php">
        <button type="button">📜 Volver al Historial</button>
    </a>
</div>

<?php include "footer.php"; ?>
