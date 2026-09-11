<?php
include "conexion.php";

$q = $_GET['q'];

$sql = "SELECT * FROM pacientes 
        WHERE nombre LIKE '%$q%' OR apellido LIKE '%$q%' 
        LIMIT 5";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($p = $resultado->fetch_assoc()) {
        echo "<div style='cursor:pointer;padding:5px;border-bottom:1px solid #ccc'
        onclick=\"seleccionarPaciente(
            '{$p['id_paciente']}',
            '{$p['nombre']}',
            '{$p['apellido']}',
            '{$p['contacto']}',
            '{$p['seguro']}'
        )\">
        {$p['nombre']} {$p['apellido']}
        </div>";
    }
} else {
    echo "<div>No encontrado</div>";
}
