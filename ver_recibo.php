<?php
include "conexion.php";

if (!isset($_GET['id_recibo'])) {
    die("Recibo no válido");
}

$id_recibo = intval($_GET['id_recibo']);

/* ==========================
   OBTENER RECIBO
========================== */
$recibo = $conn->query("
    SELECT *
    FROM recibos
    WHERE id_recibo = $id_recibo
")->fetch_assoc();

if (!$recibo) {
    die('Recibo no encontrado');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Recibo de Ingreso</title>

<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    background:#f4f6f9;
}

.recibo-box{
    background:white;
    max-width:700px;
    margin:20px auto;
    padding:30px;
    border:1px solid #ccc;
}

/* LOGO GRANDE */
.logo-recibo{
    text-align:center;
    margin-bottom:10px;
}
.logo-recibo img{
    height:150px;
}

/* TITULO */
.titulo{
    text-align:center;
    font-size:22px;
    font-weight:bold;
    margin-bottom:20px;
}

/* INFO */
.info{
    display:flex;
    justify-content:space-between;
    margin-bottom:15px;
    font-size:14px;
}

/* TEXTO */
.texto{
    font-size:15px;
    margin:10px 0;
}

/* FIRMAS */
.firmas{
    display:flex;
    justify-content:space-between;
    margin-top:40px;
}

.firma{
    text-align:center;
    width:45%;
    border-top:1px solid #000;
    padding-top:5px;
    font-size:14px;
}

/* BOTONES */
.acciones{
    margin-top:25px;
    text-align:center;
}
button{
    padding:10px 20px;
    background:#1e90ff;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

/* IMPRESIÓN */
@media print{
    body{
        background:white;
    }
    .acciones{
        display:none;
    }
}
</style>
</head>

<body>

<div class="recibo-box">

    <div class="logo-recibo">
        <img src="img/logo.png" alt="Logo Clínica">
    </div>

    <div class="titulo">
        RECIBO DE INGRESO
    </div>

    <div class="info">
        <div>
            <strong>No.:</strong> <?= $recibo['codigo_recibo']; ?>
        </div>
        <div>
            <strong>Fecha:</strong> <?= date("d/m/Y", strtotime($recibo['fecha'])); ?>
        </div>
    </div>

    <div class="texto">
        He recibido de <strong><?= $recibo['nombre_cliente']; ?></strong>
    </div>

    <div class="texto">
        La suma de <strong><?= $recibo['monto_letras']; ?></strong>
    </div>

    <div class="texto">
        Por concepto de <strong><?= $recibo['concepto']; ?></strong>
    </div>

    <div class="texto">
        Con cargo a <strong><?= $recibo['cargo']; ?></strong>
    </div>

    <div class="texto">
        Forma de pago: <strong><?= $recibo['metodo_pago']; ?></strong>
    </div>

    <div class="texto" style="margin-top:15px;">
        <strong>Monto:</strong> RD$ <?= number_format($recibo['monto_numero'], 2); ?>
    </div>

    <div class="firmas">
        <div class="firma">
            Recibido por
        </div>
        <div class="firma">
            Clínica Dental
        </div>
    </div>

    <div class="acciones">
    <button onclick="window.print()">🖨 Imprimir</button>
    <button onclick="history.back()" class="btn-volver">⬅ Volver</button>
</div>

</div>

</body>
</html>
