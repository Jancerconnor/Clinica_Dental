<?php
include "conexion.php";
include "header.php";

/* GENERAR CODIGO RECIBO */
$codigo = "RC-" . rand(1000, 9999);
$fecha  = date("Y-m-d");
?>

<div class="recibo-box">

    <div style="text-align:center;">
        <img src="img/logo.png" style="height:120px;"><br>
        <strong>RECIBO DE INGRESO</strong>
    </div>

    <br>

    <form action="guardar_recibo.php" method="POST">

        <p><strong>No:</strong> <?= $codigo ?></p>
        <p><strong>Fecha:</strong> <?= date("d/m/Y") ?></p>

        <input type="hidden" name="codigo_recibo" value="<?= $codigo ?>">
        <input type="hidden" name="fecha" value="<?= $fecha ?>">

        <label>He recibido de:</label>
        <input 
            type="text" 
            name="nombre_cliente" 
            placeholder="Nombre del paciente" 
            required
        >

        <label>La suma de (en letras):</label>
        <input type="text" name="monto_letras" required>

        <label>Monto (RD$):</label>
        <input type="number" step="0.01" name="monto_numero" required>

        <label>Con cargo a:</label>
        <input type="text" name="cargo">

        <label>Por concepto de:</label>
        <input type="text" name="concepto" value="Servicios odontológicos" required>

        <label>Forma de pago:</label>
        <select name="metodo_pago" required>
            <option value="">Seleccione</option>
            <option value="Efectivo">Efectivo</option>
            <option value="Cheque">Cheque</option>
            <option value="Transferencia">Transferencia</option>
        </select>

        <br>

        <div class="acciones">
            <button type="submit">Guardar Recibo</button>
            <button type="button" onclick="window.print()">🖨 Imprimir</button>
        </div>

    </form>

    <br><br>

    <div style="text-align:right;">
        ___________________________<br>
        Firma Autorizada
    </div>

</div>

<?php include "footer.php"; ?>
