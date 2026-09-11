<?php
include "conexion.php";
include "header.php";

/* CONSULTAR CITAS PENDIENTES */
$sql = "
SELECT 
    c.id_cita,
    c.fecha_cita,
    c.hora_cita,
    p.id_paciente,
    p.cedula,                -- ✅ AGREGADO
    p.nombre,
    p.apellido,
    p.contacto,
    p.seguro
FROM citas c
INNER JOIN pacientes p ON c.id_paciente = p.id_paciente
WHERE c.estado = 'pendiente'
ORDER BY c.fecha_cita ASC, c.hora_cita ASC
";

$resultado = $conn->query($sql);
?>

<h2>Citas Pendientes</h2>
<p>Seleccione una cita para atender al paciente y crear el presupuesto.</p>

<input 
    type="text" 
    id="buscarCita" 
    placeholder="Buscar por nombre, cédula, fecha u hora"
    style="width:100%; padding:10px; margin-bottom:15px;"
>

<table class="tabla-citas" id="tablaCitas">
    <thead>
        <tr>
            <th>Cédula</th> <!-- ✅ NUEVA -->
            <th>Paciente</th>
            <th>Contacto</th>
            <th>Seguro</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($resultado->num_rows > 0): ?>
            <?php while($row = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $row['cedula']; ?></td> <!-- ✅ NUEVO -->

                <td><?= $row['nombre']." ".$row['apellido']; ?></td>
                <td><?= $row['contacto']; ?></td>
                <td><?= $row['seguro']; ?></td>

                <td>
                    <?= date("d/m/Y", strtotime($row['fecha_cita'])); ?>
                </td>

                <td>
                    <?= date("h:i A", strtotime($row['hora_cita'])); ?>
                </td>

                <td style="display:flex; gap:6px;">
                    <a 
                        class="btn-atender"
                        href="crear_presupuesto.php?id_paciente=<?= $row['id_paciente']; ?>&id_cita=<?= $row['id_cita']; ?>"
                    >
                        Presupuesto Normal
                    </a>

                    <a 
                        class="btn-atender"
                        style="background:#0d6efd;"
                        href="crear_presupuesto_ars.php?id_paciente=<?= $row['id_paciente']; ?>&id_cita=<?= $row['id_cita']; ?>"
                    >
                        Presupuesto ARS
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align:center;">
                    No hay citas pendientes
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
document.getElementById("buscarCita").addEventListener("keyup", function () {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#tablaCitas tbody tr");

    filas.forEach(fila => {
        fila.style.display = fila.innerText.toLowerCase().includes(filtro)
            ? ""
            : "none";
    });
});
</script>

<?php include "footer.php"; ?>
