<?php
include "conexion.php";
include "header.php";

$id_paciente = $_GET['id_paciente'] ?? 0;
$id_cita     = $_GET['id_cita'] ?? 0;

$paciente = $conn->query("
    SELECT nombre, apellido, seguro
    FROM pacientes
    WHERE id_paciente = $id_paciente
")->fetch_assoc();

$fecha = date("d/m/Y");
?>

<style>
.presupuesto{
    width:950px;
    margin:auto;
    background:#fff;
    padding:20px;
    border:1px solid #ccc;
    font-size:14px;
}
.encabezado{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}
.encabezado h2{
    color:#0b78c9;
    margin:0;
}
.info p{ margin:3px 0; }
table{
    width:100%;
    border-collapse:collapse;
}
th{
    background:#9fcaf0;
    border:1px solid #666;
    padding:6px;
    text-align:center;
}
td{
    border:1px solid #666;
    padding:5px;
    text-align:center;
}
td input{
    width:100%;
    border:none;
    background:transparent;
    text-align:center;
}
.text-right{ text-align:right; }
.total-row{
    background:#9fcaf0;
    font-weight:bold;
}
.boton{
    margin-top:20px;
    text-align:center;
}
button{
    padding:10px 20px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    background:#0b78c9;
    color:white;
}
</style>

<div class="presupuesto">

    <div class="encabezado">
        
        <div class="info">
            <p><strong>Fecha:</strong> <?= $fecha ?></p>
            <p><strong>Seguro Médico:</strong> <?= $paciente['seguro'] ?></p>
        </div>
    </div>

    <div class="info">
        <p><strong>Nombre Paciente:</strong> <?= $paciente['nombre']." ".$paciente['apellido'] ?></p>
    </div>

    <form action="guardar_presupuesto_ars.php" method="POST">

        <input type="hidden" name="id_paciente" value="<?= $id_paciente ?>">
        <input type="hidden" name="id_cita" value="<?= $id_cita ?>">

        <table id="tabla">
            <tr>
                <th>CANT.</th>
                <th>PROCEDIMIENTO</th>
                <th>PRECIO UNIT.</th>
                <th>PRECIO TOTAL</th>
                <th>COBERTURA ARS</th>
                <th>TOTAL A PAGAR</th>
            </tr>

            <?php for($i=0;$i<10;$i++): ?>
            <tr class="fila">
                <td>
                    <input type="number" name="cantidad[]" value="1" min="1" class="cantidad">
                </td>
                <td>
                    <input type="text" name="procedimiento[]">
                </td>
                <td>
                    <input type="number" step="0.01" name="precio[]" class="precio">
                </td>
                <td class="precio-total">0.00</td>
                <td>
                    <input type="number" step="0.01" name="ars[]" class="ars">
                </td>
                <td class="pagar text-right">0.00</td>
            </tr>
            <?php endfor; ?>

            <tr class="total-row">
                <td colspan="3" class="text-right">TOTALES RD</td>
                <td class="text-right" id="totalPrecio">0.00</td>
                <td class="text-right" id="totalARS">0.00</td>
                <td class="text-right" id="totalPagar">0.00</td>
            </tr>
        </table>

        <div class="boton">
            <button type="submit">💾 Guardar Presupuesto</button>
            <button type="button" onclick="window.print()">🖨 Imprimir</button>
        </div>

    </form>
</div>

<script>
function calcular() {
    let totalPrecio = 0;
    let totalARS = 0;
    let totalPagar = 0;

    document.querySelectorAll(".fila").forEach(fila => {
        let precio   = parseFloat(fila.querySelector(".precio").value) || 0;
        let cantidad = parseInt(fila.querySelector(".cantidad").value) || 1;
        let ars      = parseFloat(fila.querySelector(".ars").value) || 0;

        let precioTotal = precio * cantidad;
        if (ars > precioTotal) ars = precioTotal;

        let pagar = precioTotal - ars;

        fila.querySelector(".precio-total").innerText = precioTotal.toFixed(2);
        fila.querySelector(".pagar").innerText = pagar.toFixed(2);

        totalPrecio += precioTotal;
        totalARS += ars;
        totalPagar += pagar;
    });

    document.getElementById("totalPrecio").innerText = totalPrecio.toFixed(2);
    document.getElementById("totalARS").innerText = totalARS.toFixed(2);
    document.getElementById("totalPagar").innerText = totalPagar.toFixed(2);
}

document.querySelectorAll(".precio, .ars, .cantidad").forEach(i => {
    i.addEventListener("input", calcular);
});
</script>

<?php include "footer.php"; ?>
