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

// ✅ Volvemos a usar $_POST
$estadoCivil = $_POST['estadoCivil'] ?? '';
$departamento = $_POST['departamento'] ?? '';

// --- Consulta con TRIM() para ser extra seguros ---
$clausulasWhere = [];
$parametros = [];
$tipos = '';

if (!empty($estadoCivil)) {
    $clausulasWhere[] = "TRIM(estado_civil) = ?";
    $parametros[] = $estadoCivil; // Ej: "Casado"
    $tipos .= 's';
}
if (!empty($departamento)) {
    $clausulasWhere[] = "TRIM(departamento) = ?";
    $parametros[] = $departamento; // Ej: "La Unión"
    $tipos .= 's';
}

// --- Lógica Dinámica (Si filtra Depto, muestra Estado Civil, y viceversa) ---
if (!empty($departamento)) {
    $sql = "SELECT TRIM(estado_civil) AS Categoria, COUNT(*) AS Cantidad FROM personal";
    $groupBy = " GROUP BY TRIM(estado_civil)";
    $csvHeader = "EstadoCivil,Cantidad\n";
    $tituloGrafica = 'Estado Civil en ' . $departamento;
    if (!empty($estadoCivil)) {
        $tituloGrafica = 'Personal (' . $estadoCivil . ') en ' . $departamento;
    }
} else {
    $sql = "SELECT TRIM(departamento) AS Categoria, COUNT(*) AS Cantidad FROM personal";
    $groupBy = " GROUP BY TRIM(departamento)";
    $csvHeader = "Departamento,Cantidad\n";
    $tituloGrafica = 'Personal por Departamento';
    if (!empty($estadoCivil)) {
        $tituloGrafica = 'Personal (' . $estadoCivil . ') por Departamento';
    }
}

// Unimos la consulta
if (count($clausulasWhere) > 0) {
    $sql .= " WHERE " . implode(' AND ', $clausulasWhere);
}
$sql .= $groupBy . " ORDER BY Categoria";

$stmt = $con->prepare($sql);
if (count($parametros) > 0) {
    $stmt->bind_param($tipos, ...$parametros);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Construimos el CSV
$csvData = $csvHeader;
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $csvData .= "{$row['Categoria']},{$row['Cantidad']}\n";
    }
} else {
    $csvData .= "Sin datos,0\n";
}

$stmt->close();
$con->close();

// Devolvemos el JSON
echo json_encode([
    'csv' => trim($csvData),
    'titulo' => $tituloGrafica
]);
?>