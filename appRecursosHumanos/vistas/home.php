<?php
session_start();
// echo "Bienvenido ".$_SESSION['usuario'];

if(isset($_SESSION['usuario'])== null){
    Header('Location: ../index.php');
}
?>

<?php
include_once('../conf/conf.php'); // Asegúrate de tener tu conexión aquí

// Consulta para contar personal por departamento
$sql = "SELECT departamento, COUNT(*) AS cantidad FROM personal GROUP BY departamento";
$result = mysqli_query($con, $sql);

// Construir CSV
$csvData = "Departamento,Cantidad\n";
while ($row = mysqli_fetch_assoc($result)) {
    $csvData .= "{$row['departamento']},{$row['cantidad']}\n";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="../public/css/homeStyle.css">
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/dashboards/dashboards.js"></script>
        <script src="https://code.highcharts.com/dashboards/modules/layout.js"></script>
        <title>Panel Contentido</title>
    </head>
    <body>
        <?php
        include_once('./nav.php');
        ?>

        /* EL FORMULARIO NO SIRVE */
        <div class="container my-3">
            <form id="filtroForm" class="row g-3">
                <div class="col-md-6">
                    <label for="estadoCivil" class="form-label">Estado Civil</label>
                    <select id="estadoCivil" name="estadoCivil" class="form-select">
                        <option value="">Todos</option>
                        <option value="Soltero">Soltero</option>
                        <option value="Casado">Casado</option>
                        <option value="Divorciado">Divorciado</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="departamento" class="form-label">Departamento</label>
                    <select id="departamento" name="departamento" class="form-select">
                        <option value="">Todos</option>
                    </select>

                </div>
            </form>
        </div>


        /* EL CONTAINER SI TRAE LOS DATOS */
        <div id="container"></div>
        <pre id="csv" style="display:none;">
            <?php echo trim($csvData); ?>
        </pre>
    </body>
</html>
<script src="../public/js/graf.js"></script>
