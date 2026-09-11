<?php
include "conexion.php";

if (!isset($_GET['id'])) {
    die("ID no válido");
}

$id = intval($_GET['id']);

$conn->begin_transaction();

try {

    /* ELIMINAR RECIBOS */
    $conn->query("
        DELETE FROM recibos 
        WHERE id_paciente = $id
    ");

    /* ELIMINAR FACTURAS */
    $conn->query("
        DELETE FROM facturas 
        WHERE id_paciente = $id
    ");

    /* OBTENER PRESUPUESTOS DEL PACIENTE */
    $presupuestos = $conn->query("
        SELECT id_presupuesto 
        FROM presupuestos 
        WHERE id_paciente = $id
    ");

    while ($p = $presupuestos->fetch_assoc()) {
        $id_pres = $p['id_presupuesto'];

        /* ELIMINAR DETALLE PRESUPUESTO */
        $conn->query("
            DELETE FROM detalle_presupuesto 
            WHERE id_presupuesto = $id_pres
        ");
    }

    /* ELIMINAR PRESUPUESTOS */
    $conn->query("
        DELETE FROM presupuestos 
        WHERE id_paciente = $id
    ");

    /* ELIMINAR CITAS */
    $conn->query("
        DELETE FROM citas 
        WHERE id_paciente = $id
    ");

    /* ELIMINAR PACIENTE */
    $conn->query("
        DELETE FROM pacientes 
        WHERE id_paciente = $id
    ");

    $conn->commit();

    header("Location: pacientes.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Error al eliminar paciente: " . $e->getMessage());
}
