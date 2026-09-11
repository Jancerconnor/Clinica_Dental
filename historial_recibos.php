<?php
include "conexion.php";
include "header.php";

/* BUSCADOR */
$filtro = "";
$where = "";

if (isset($_GET['buscar']) && $_GET['buscar'] != "") {
    $filtro = $conn->real_escape_string($_GET['buscar']);
    $where = "WHERE 
                codigo_recibo LIKE '%$filtro%' OR
                nombre_cliente LIKE '%$filtro%' OR
                fecha LIKE '%$filtro%'";
}

/* OBTENER RECIBOS */
$recibos = $conn->query("
    SELECT *
    FROM recibos
    $where
    ORDER BY fecha DESC
");
?>

<h2>Historial de Recibos</h2>

<div class="info-box">
    <form method="GET">
        <input 
            type="text" 
            name="buscar" 
            placeholder="Buscar por código, nombre o fecha"
            value="<?= htmlspecialchars($filtro) ?>"
            style="padding:8px; width:260px;"
        >
        <button type="submit">🔍 Buscar</button>

        <a href="recibo.php">
            <button type="button">➕ Nuevo Recibo</button>
        </a>
    </form>
</div>

<table class="tabla-presupuesto">
    <tr>
        <th>No.</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Monto</th>
        <th>Pago</th>
        <th>Acciones</th>
    </tr>

    <?php if ($recibos->num_rows > 0): ?>
        <?php while ($r = $recibos->fetch_assoc()): ?>
            <tr>
                <td><?= $r['codigo_recibo']; ?></td>
                <td><?= date("d/m/Y", strtotime($r['fecha'])); ?></td>
                <td><?= $r['nombre_cliente']; ?></td>
                <td>RD$ <?= number_format($r['monto_numero'], 2); ?></td>
                <td><?= $r['metodo_pago']; ?></td>
                <td>
                    <a href="ver_recibo.php?id_recibo=<?= $r['id_recibo']; ?>">
                        <button>🖨 Ver / Imprimir</button>
                    </a>

                    <a 
                        href="eliminar_recibo.php?id_recibo=<?= $r['id_recibo']; ?>"
                        onclick="return confirm('¿Seguro que deseas eliminar este recibo?');"
                    >
                        <button class="btn-eliminar">🗑 Eliminar</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align:center;">
                No hay recibos registrados
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php include "footer.php"; ?>
