<?php
include "conexion.php";
include "header.php";

$id_paciente = intval($_POST['id_paciente']);
$id_cita     = intval($_POST['id_cita']);
$fecha       = date("Y-m-d");

$procedimientos = $_POST['procedimiento'] ?? [];
$precios        = $_POST['precio'] ?? [];
$cantidades     = $_POST['cantidad'] ?? [];

/* ================================
   TOTAL GENERAL
================================ */
$total = 0;

/* ================================
   CREAR PRESUPUESTO VACÍO
================================ */
$conn->query("
    INSERT INTO presupuestos 
    (id_paciente, fecha, total, id_cita)
    VALUES 
    ($id_paciente, '$fecha', 0, $id_cita)
");

$id_presupuesto = $conn->insert_id;

/* ================================
   DETALLE DEL PRESUPUESTO
================================ */
for ($i = 0; $i < count($procedimientos); $i++) {

    if (
        !empty($procedimientos[$i]) &&
        !empty($precios[$i]) &&
        !empty($cantidades[$i])
    ) {

        $procedimiento = $conn->real_escape_string($procedimientos[$i]);
        $precio_unit   = floatval($precios[$i]);
        $cantidad      = intval($cantidades[$i]);

        $precio_total  = $precio_unit * $cantidad;

        $total += $precio_total;

        $conn->query("
            INSERT INTO detalle_presupuesto
            (id_presupuesto, procedimiento, cantidad, precio_unitario, precio_total)
            VALUES
            ($id_presupuesto, '$procedimiento', $cantidad, $precio_unit, $precio_total)
        ");
    }
}

/* ================================
   ACTUALIZAR TOTAL GENERAL
================================ */
$conn->query("
    UPDATE presupuestos
    SET total = $total
    WHERE id_presupuesto = $id_presupuesto
");

/* ================================
   MARCAR CITA COMO ATENDIDA
================================ */
$conn->query("
    UPDATE citas
    SET estado = 'atendida'
    WHERE id_cita = $id_cita
");
?>

<div class="contenedor">
    <h2>✅ Presupuesto guardado correctamente</h2>

    <p>
        <strong>Total del servicio:</strong>
        RD$ <?= number_format($total, 2); ?>
    </p>

    <div class="acciones">
        <a href="ver_presupuesto_detallado.php?id_presupuesto=<?= $id_presupuesto; ?>">
            <button>👁 Ver Presupuesto</button>
        </a>

        <button onclick="window.print()">🖨 Imprimir</button>
    </div>
</div>

<?php include "footer.php"; ?>
