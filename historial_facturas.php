<?php
include "conexion.php";
include "header.php";
?>

<h2>📄 Historial de Facturas</h2>

<!-- 🔍 BUSCADOR -->
<input 
    type="text" 
    id="buscarFactura" 
    placeholder="🔍 Buscar por código o nombre del paciente"
    style="width:100%; padding:10px; margin:15px 0;"
>

<table class="tabla-presupuesto" id="tablaFacturas">
    <thead>
        <tr>
            <th>CÓDIGO</th>
            <th>PACIENTE</th>
            <th>FECHA</th>
            <th>TOTAL</th>
            <th>ACCIÓN</th>
        </tr>
    </thead>
    <tbody>

<?php
$query = "
SELECT 
    f.id_factura,
    f.codigo,
    f.fecha,
    f.total,
    p.nombre,
    p.apellido
FROM facturas f
INNER JOIN pacientes p ON f.id_paciente = p.id_paciente
ORDER BY f.fecha DESC
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?= $row['codigo']; ?></td>
            <td><?= $row['nombre']." ".$row['apellido']; ?></td>
            <td><?= date('d/m/Y', strtotime($row['fecha'])); ?></td>
            <td>RD$ <?= number_format($row['total'], 2); ?></td>
            <td>
                <a 
                    href="ver_factura.php?id_factura=<?= $row['id_factura']; ?>" 
                    class="btn-atender"
                >
                    👁 Ver
                </a>

                <form action="eliminar_factura.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta factura del historial?');">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id_factura" value="<?= $row['id_factura']; ?>">
                    <button type="submit" class="btn-eliminar">🗑 Eliminar</button>
                </form>
            </td>
        </tr>
        <?php
    }
} else {
    ?>
    <tr>
        <td colspan="5" style="text-align:center;">
            No hay facturas registradas
        </td>
    </tr>
    <?php
}
?>

    </tbody>
</table>

<script>
document.getElementById("buscarFactura").addEventListener("keyup", function () {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#tablaFacturas tbody tr");

    filas.forEach(fila => {
        fila.style.display = fila.innerText.toLowerCase().includes(filtro)
            ? ""
            : "none";
    });
});
</script>

<?php include "footer.php"; ?>
