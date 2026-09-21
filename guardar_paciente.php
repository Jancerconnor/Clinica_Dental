<?php
include "conexion.php";
include_once "csrf.php";
csrf_require_valid_post();

$id_paciente = $_POST['id_paciente'] ?? '';

$cedula         = trim($_POST['cedula']);
$nombre         = trim($_POST['nombre']);
$apellido       = trim($_POST['apellido']);
$contacto       = trim($_POST['contacto']);
$seguro         = $_POST['seguro'];
$cobertura_ars  = $_POST['cobertura_ars'] ?? 0;
$fecha_cita     = $_POST['fecha_cita'];
$hora_cita      = $_POST['hora_cita'];
$fecha_hoy      = date("Y-m-d");

/* =========================
   BUSCAR / GUARDAR PACIENTE
========================= */

if ($id_paciente == "") {

    // 🔒 VALIDAR PACIENTE DUPLICADO POR CÉDULA
    $validar = $conn->query("
        SELECT id_paciente 
        FROM pacientes 
        WHERE cedula = '$cedula'
        LIMIT 1
    ");

    if ($validar->num_rows > 0) {
        echo "<script>
            alert('Ya existe un paciente registrado con esta cédula.');
            window.location.href='pacientes.php';
        </script>";
        exit;
    }

    // Registrar nuevo paciente
    $sqlPaciente = "
        INSERT INTO pacientes 
        (cedula, nombre, apellido, contacto, seguro, cobertura_ars, fecha_registro)
        VALUES 
        ('$cedula','$nombre','$apellido','$contacto','$seguro','$cobertura_ars','$fecha_hoy')
    ";

    if ($conn->query($sqlPaciente)) {
        $id_paciente = $conn->insert_id;
    } else {
        die('Error al registrar paciente: ' . $conn->error);
    }

} else {

    // 🔥 Actualizar paciente existente
    $conn->query("
        UPDATE pacientes 
        SET cedula        = '$cedula',
            nombre        = '$nombre',
            apellido      = '$apellido',
            contacto      = '$contacto',
            seguro        = '$seguro',
            cobertura_ars = '$cobertura_ars'
        WHERE id_paciente = $id_paciente
    ");
}

/* =========================
   GUARDAR CITA
========================= */

$conn->query("
    INSERT INTO citas (id_paciente, fecha_cita, hora_cita, estado)
    VALUES ($id_paciente, '$fecha_cita', '$hora_cita', 'pendiente')
");

/* =========================
   REDIRIGIR
========================= */

header("Location: pacientes.php");
exit;
