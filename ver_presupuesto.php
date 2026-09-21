<?php
include "conexion.php";
include "header.php";

if (!isset($_GET['id_presupuesto'])) {
    die("Presupuesto no válido");
}

$id_presupuesto = intval($_GET['id_presupuesto']);

/* OBTENER PRESUPUESTO */
$pres = $conn->query("
    SELECT 
        pr.id_presupuesto,
        pr.fecha,
        pr.total,
        pa.id_paciente,
        pa.nombre,
        pa.apellido,
        pa.seguro
    FROM presupuestos pr
    INNER JOIN pacientes pa ON pr.id_paciente = pa.id_paciente
    WHERE pr.id_presupuesto = $id_presupuesto
")->fetch_assoc();

if (!$pres) {
    die("Presupuesto no encontrado");
}
?>

<h2>Presupuesto</h2>

<div class="info-box">
    <p><strong>Paciente:</strong> <?= $pres['nombre']." ".$pres['apellido']; ?></p>
    <p><strong>Seguro:</strong> <?= $pres['seguro']; ?></p>
    <p><strong>Fecha:</strong> <?= date("d/m/Y", strtotime($pres['fecha'])); ?></p>
</div>

<h3 style="margin-top:20px;">
    TOTAL DEL SERVICIO: RD$ <?= number_format($pres['total'], 2); ?>
</h3>

<div class="acciones" style="margin-top:15px;">
    <button onclick="window.print()">🖨 Imprimir</button>

    <form action="generar_factura.php" method="POST" style="display:inline;">
        <?= csrf_field(); ?>
        <input type="hidden" name="id_presupuesto" value="<?= $id_presupuesto; ?>">
        <button type="submit">🧾 Generar Factura</button>
    </form>
</div>

<?php include "footer.php"; ?>

<script>
    document.body.classList.add('presupuesto-normal');
</script>

