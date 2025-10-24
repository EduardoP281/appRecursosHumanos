<?php
include_once('../conf/conf.php');

$estadoCivil = $_POST['estadoCivil'] ?? '';
$departamento = $_POST['departamento'] ?? '';

$where = [];

if ($estadoCivil !== '') {
    $where[] = "estado_civil = '" . mysqli_real_escape_string($conn, $estadoCivil) . "'";
}
if ($departamento !== '') {
    $where[] = "departamento = '" . mysqli_real_escape_string($conn, $departamento) . "'";
}

$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT departamento, COUNT(*) AS cantidad FROM personal $whereSQL GROUP BY departamento";
$result = mysqli_query($conn, $sql);

$csv = "Departamento,Cantidad\n";
while ($row = mysqli_fetch_assoc($result)) {
    $csv .= "{$row['departamento']},{$row['cantidad']}\n";
}

echo $csv;

