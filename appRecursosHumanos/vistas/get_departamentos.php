<?php
session_start();
header('Content-Type: application/json');
// ✅ Cabeceras Anti-Caché
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

include_once __DIR__ . '/../conf/conf.php';

$departamentos = [];
// ✅ Usamos TRIM() por si hay espacios en los datos
$sql = "SELECT DISTINCT TRIM(departamento) AS departamento FROM personal WHERE departamento IS NOT NULL AND departamento != '' ORDER BY departamento ASC";
$result = mysqli_query($con, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departamentos[] = $row['departamento'];
    }
}

echo json_encode($departamentos);
$con->close();
?>