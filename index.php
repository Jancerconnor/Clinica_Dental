<?php
include "verificar_sesion.php";
?>


<?php
include "header.php";
?>

<div class="panel-clinica">

    <h2 class="titulo-panel">Sistema de Gestión Clínica</h2>
    <p class="subtitulo-panel">Seleccione una opción</p>

    <div class="botones-clinica">

        <a href="pacientes.php" class="btn-clinica">
            🧑‍⚕️
            <span>Registro de Pacientes</span>
        </a>

        <a href="citas_pendientes.php" class="btn-clinica">
            📅
            <span>Citas Pendientes</span>
        </a>

        <a href="historial_recibos.php" class="btn-clinica">
            🧾
            <span>Recibos</span>
        </a>

    </div>

</div>

<?php include "footer.php"; ?>
