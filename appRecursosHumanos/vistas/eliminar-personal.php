<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

include_once __DIR__ . '/../conf/conf.php'; // ✅ Ruta corregida

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Eliminar imagen física si existe
    $sql = "SELECT fotografia FROM personal WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $foto_path = '../public/uploads/' . basename($row['fotografia']); // ✅ Ruta corregida
        if (!empty($row['fotografia']) && file_exists($foto_path)) {
            unlink($foto_path);
        }
    }
    $stmt->close();

    // Eliminar registro
    $sql = "DELETE FROM personal WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: personal.php');
exit;