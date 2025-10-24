<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    Header('Location: ../index.php');
    exit;
}
include_once('../conf/conf.php');

// Consulta para la gráfica INICIAL
$sql = "SELECT departamento, COUNT(*) AS cantidad FROM personal GROUP BY departamento";
$result = mysqli_query($con, $sql);
$csvData = "Departamento,Cantidad\n";
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $csvData .= "{$row['departamento']},{$row['cantidad']}\n";
    }
} else {
    $csvData .= "Sin datos,0\n";
}
$con->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/homeStyle.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/dashboards/dashboards.js"></script>
    <script src="https://code.highcharts.com/dashboards/modules/layout.js"></script>
    <title>Panel Contenido</title>
</head>
<body>
    <?php include_once('./nav.php'); ?>

    <div class="container my-3">
        <form id="filtroForm" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="estadoCivil" class="form-label">Estado Civil</label>
                <select id="estadoCivil" name="estadoCivil" class="form-select">
                    <option value="">Todos</option>
                    <option value="Soltero">Soltero(a)</option>
                    <option value="Casado">Casado(a)</option>
                    <option value="Divorciado">Divorciado(a)</option>
                    <option value="Viudo">Viudo(a)</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="departamento" class="form-label">Departamento</label>
                <select id="departamento" name="departamento" class="form-select">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Actualizar</button>
            </div>
        </form>
    </div>

    <div id="container"></div>
    <pre id="csv" style="display:none;"><?php echo trim($csvData); ?></pre>
</body>
</html>
<script src="../public/js/graf.js"></script>