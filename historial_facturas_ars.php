<?php
include "conexion.php";
include_once "csrf.php";

$busqueda = "";
$where = "";

if (isset($_GET['buscar']) && $_GET['buscar'] != "") {
    $busqueda = $conn->real_escape_string($_GET['buscar']);
    $where = "WHERE f.numero_factura LIKE '%$busqueda%'
              OR CONCAT(pa.nombre,' ',pa.apellido) LIKE '%$busqueda%'";
}

$facturas = $conn->query("
    SELECT f.id_factura,
           f.numero_factura,
           f.fecha,
           f.total,
           CONCAT(pa.nombre,' ',pa.apellido) AS paciente
    FROM facturas_ars f
    JOIN presupuestos_ars p ON p.id_presupuesto = f.id_presupuesto
    JOIN pacientes pa ON pa.id_paciente = p.id_paciente
    $where
    ORDER BY f.id_factura DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial Facturas ARS</title>

<style>
body{ font-family:Arial; background:#f4f6f8; }
.contenedor{
    width:950px;
    margin:20px auto;
    background:white;
    padding:20px;
    border:1px solid #ccc;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
th, td{
    border:1px solid #999;
    padding:8px;
    text-align:center;
}
th{
    background:#9fcaf0;
}
.btn{
    text-decoration:none;
    color:white;
    padding:6px 12px;
    border-radius:5px;
    font-size:14px;
}
.ver{ background:#0b78c9; }
.eliminar{ background:#d9534f; }
.atras{ background:#6c757d; }
.inicio{ background:#28a745; }

.barra{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
}
input{
    padding:7px;
    width:300px;
}
.acciones-top{
    display:flex;
    gap:8px;
}
</style>
</head>

<body>

<div class="contenedor">

    <div class="barra">
        <form method="GET">
            <input type="text" name="buscar"
                   placeholder="Buscar por factura o paciente..."
                   value="<?= htmlspecialchars($busqueda); ?>">
            <button class="btn ver">Buscar</button>
        </form>

        <div class="acciones-top">
            <a href="index.php" class="btn inicio">🏠 Inicio</a>
            <a href="javascript:history.back()" class="btn atras">⬅ Atrás</a>
        </div>
    </div>

    <h2>📋 Historial de Facturas ARS</h2>

    <table>
    <tr>
        <th>N° FACTURA</th>
        <th>PACIENTE</th>
        <th>FECHA</th>
        <th>TOTAL</th>
        <th>ACCIONES</th>
    </tr>

    <?php if ($facturas->num_rows > 0) { ?>
        <?php while($f = $facturas->fetch_assoc()) { ?>
        <tr>
            <td><?= $f['numero_factura']; ?></td>
            <td><?= $f['paciente']; ?></td>
            <td><?= date("d/m/Y", strtotime($f['fecha'])); ?></td>
            <td>RD$ <?= number_format($f['total'],2); ?></td>
            <td>
                <a class="btn ver"
                   href="ver_factura_ars.php?id_factura=<?= $f['id_factura']; ?>">
                   Ver
                </a>

                <form action="eliminar_factura_ars.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta factura?');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id_factura" value="<?= $f['id_factura']; ?>">
                    <button type="submit" class="btn eliminar">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="5">No se encontraron facturas</td>
        </tr>
    <?php } ?>

    </table>
</div>

</body>
</html>
