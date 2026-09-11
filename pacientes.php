<?php
include "conexion.php";
include "header.php";

/* OBTENER PACIENTES */
$pacientes = $conn->query("
    SELECT id_paciente, cedula, nombre, apellido, contacto, seguro
    FROM pacientes
    ORDER BY nombre ASC
");
?>

<h2>Registro de Pacientes</h2>

<div class="info-box">
    Si el paciente ya existe, búscalo abajo, selecciónalo y solo agenda la cita.
</div>

<form action="guardar_paciente.php" method="POST" class="formulario" id="formPaciente">

    <input type="hidden" name="id_paciente" id="id_paciente">

    <label>Cédula</label>
    <input 
        type="text" 
        name="cedula" 
        id="cedula" 
        placeholder="000-0000000-0"
        required
    >

    <label>Nombre</label>
    <input type="text" name="nombre" id="nombre" required>

    <label>Apellido</label>
    <input type="text" name="apellido" id="apellido" required>

    <label>Contacto</label>
    <input type="text" name="contacto" id="contacto" required>

    <label>Seguro Médico</label>
    <input 
        type="text" 
        name="seguro" 
        id="seguro" 
        placeholder="Ej: ARS SENASA, PRIVADO, MAPFRE" 
        required
    >

    <label>Fecha de cita</label>
    <input type="date" name="fecha_cita" required>

    <label>Hora de la cita</label>
    <input type="time" name="hora_cita" required>

    <button type="submit">Guardar / Agendar Cita</button>

</form>

<hr>

<h3>🔍 Buscar Paciente</h3>
<input 
    type="text" 
    id="buscarPaciente" 
    placeholder="Escriba nombre, apellido o cédula..." 
    style="width:100%; padding:10px;"
>

<table class="tabla-presupuesto" id="tablaPacientes">
    <tr>
        <th>Cédula</th>
        <th>Paciente</th>
        <th>Contacto</th>
        <th>Seguro</th>
        <th>Acción</th>
    </tr>

    <?php while($p = $pacientes->fetch_assoc()): ?>
    <tr>
        <td><?= $p['cedula']; ?></td>
        <td><?= $p['nombre']." ".$p['apellido']; ?></td>
        <td><?= $p['contacto']; ?></td>
        <td><?= $p['seguro']; ?></td>
        <td>
            <button 
                type="button" 
                onclick="seleccionarPaciente(
                    '<?= $p['id_paciente']; ?>',
                    '<?= $p['cedula']; ?>',
                    '<?= $p['nombre']; ?>',
                    '<?= $p['apellido']; ?>',
                    '<?= $p['contacto']; ?>',
                    '<?= $p['seguro']; ?>'
                )">
                Seleccionar
            </button>

            <button 
                type="button" 
                style="margin-left:10px; background:#dc3545; color:white;"
                onclick="eliminarPaciente(<?= $p['id_paciente']; ?>)">
                Eliminar
            </button>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
/* FILTRO EN VIVO */
document.getElementById("buscarPaciente").addEventListener("keyup", function () {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#tablaPacientes tr");

    filas.forEach((fila, index) => {
        if (index === 0) return;
        fila.style.display = fila.innerText.toLowerCase().includes(filtro)
            ? ""
            : "none";
    });
});

/* PASAR DATOS AL FORM */
function seleccionarPaciente(id, cedula, nombre, apellido, contacto, seguro) {
    document.getElementById("id_paciente").value = id;
    document.getElementById("cedula").value = cedula;
    document.getElementById("nombre").value = nombre;
    document.getElementById("apellido").value = apellido;
    document.getElementById("contacto").value = contacto;
    document.getElementById("seguro").value = seguro;
}

/* ELIMINAR PACIENTE */
function eliminarPaciente(id) {
    if (!confirm("⚠️ ¿Seguro que deseas eliminar este paciente?\n\nSe eliminará todo el historial e información.")) {
        return;
    }
    window.location.href = "eliminar_paciente.php?id=" + id;
}
</script>

<?php include "footer.php"; ?>
