<?php
session_start();
if(!isset($_SESSION['usuario'])){
    Header('Location: index.php');
}
include_once('./conf/conf.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    // Eliminar registro y su imagen
    $sql = "SELECT fotografia FROM personal WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['fotografia']) && file_exists($row['fotografia'])) {
            unlink($row['fotografia']);
        }
    }
    $stmt->close();
    $sql = "DELETE FROM personal WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header('Location: personal.php');
exit();
