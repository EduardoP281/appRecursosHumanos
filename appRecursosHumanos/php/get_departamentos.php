<?php
include_once('../conf/conf.php'); // Asegúrate que $conn esté definido

$sql = "SELECT DISTINCT departamento FROM personal ORDER BY departamento ASC";
$result = mysqli_query($con, $sql);

$departamentos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $departamentos[] = $row['departamento'];
}

header('Content-Type: application/json');
echo json_encode($departamentos);
?>