<?php
include "conexion.php";
include "header.php";

if (!isset($_GET['id_paciente']) || !isset($_GET['id_cita'])) {
    die("Cita no válida");
}

$id_paciente = $_GET['id_paciente'];
$id_cita     = $_GET['id_cita'];

$paciente = $conn->query("
    SELECT nombre, apellido, seguro
    FROM pacientes
    WHERE id_paciente = $id_paciente
")->fetch_assoc();

$fecha = date("d/m/Y");
?>

<style>
@media print {
    .acciones,
    input {
        display: none !important;
    }

    .fila-vacia {
        display: none !important;
    }

    .print-text {
        display: inline !important;
    }
}

.print-text {
    display: none;
    font-weight: bold;
}
</style>

<h2>Presupuesto</h2>

<div class="info-box">
    <strong>Paciente:</strong> <?= $paciente['nombre']." ".$paciente['apellido']; ?><br>
    <strong>Fecha:</strong> <?= $fecha; ?><br>
    <strong>Seguro:</strong> <?= $paciente['seguro']; ?>
</div>

<form action="guardar_presupuesto.php" method="POST" id="formPresupuesto">

<input type="hidden" name="id_paciente" value="<?= $id_paciente; ?>">
<input type="hidden" name="id_cita" value="<?= $id_cita; ?>">

<table class="tabla-presupuesto" id="tablaPresupuesto">

<tr>
    <th>CANTIDAD</th>
    <th>PROCEDIMIENTO</th>
    <th>PRECIO UNITARIO</th>
    <th>PRECIO TOTAL</th>
</tr>

<?php for ($i = 0; $i < 10; $i++): ?>
<tr class="fila-presupuesto fila-vacia">
    <td>
        <input type="number" name="cantidad[]" class="cantidad" value="1">
        <span class="print-text cantidad-text"></span>
    </td>

    <td>
        <input type="text" name="procedimiento[]" placeholder="Descripción">
        <span class="print-text procedimiento-text"></span>
    </td>

    <td>
        <input type="number" name="precio[]" class="precio">
        <span class="print-text precio-text"></span>
    </td>

    <td>
        <input type="text" class="precio-total" readonly value="0.00">
        <span class="print-text precio-total-text"></span>
    </td>
</tr>
<?php endfor; ?>

<tr>
    <th colspan="3">TOTAL DEL SERVICIO</th>
    <th id="totalServicio">RD$ 0.00</th>
</tr>

</table>

<div class="acciones">
    <button type="submit">Guardar Presupuesto</button>
    <button type="button" onclick="window.print()">🖨 Imprimir</button>
</div>

</form>

<script>
function recalcular() {
    let totalServicio = 0;

    document.querySelectorAll('.fila-presupuesto').forEach(fila => {
        const cantidad = parseInt(fila.querySelector('.cantidad').value) || 0;
        const precio   = parseFloat(fila.querySelector('.precio').value) || 0;
        const proc     = fila.querySelector('input[name="procedimiento[]"]').value.trim();

        const total = cantidad * precio;

        fila.querySelector('.precio-total').value = total.toFixed(2);

        if (proc === '' || precio <= 0) {
            fila.classList.add('fila-vacia');
        } else {
            fila.classList.remove('fila-vacia');
            totalServicio += total;
        }
    });

    document.getElementById('totalServicio').innerText =
        'RD$ ' + totalServicio.toFixed(2);
}

document.querySelectorAll('.cantidad, .precio')
    .forEach(el => el.addEventListener('input', recalcular));

window.onbeforeprint = () => {
    document.querySelectorAll('.fila-presupuesto').forEach(fila => {
        fila.querySelector('.cantidad-text').innerText =
            fila.querySelector('.cantidad').value;

        fila.querySelector('.procedimiento-text').innerText =
            fila.querySelector('input[name="procedimiento[]"]').value;

        fila.querySelector('.precio-text').innerText =
            'RD$ ' + (fila.querySelector('.precio').value || '0');

        fila.querySelector('.precio-total-text').innerText =
            'RD$ ' + fila.querySelector('.precio-total').value;
    });
};
</script>

<?php include "footer.php"; ?>
