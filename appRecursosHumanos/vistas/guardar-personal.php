<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

include_once __DIR__ . '/../conf/conf.php'; // ✅ Ruta corregida

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $dui = $_POST['dui'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $departamento = $_POST['departamento'];
    $distrito = $_POST['distrito'];
    $colonia = $_POST['colonia'];
    $calle = $_POST['calle'];
    $casa = $_POST['casa'];
    $estado_civil = $_POST['estado_civil'];
    $fotografia = $_FILES['fotografia'];

    // Validar DUI único
    if ($id > 0) {
        $sql = "SELECT id FROM personal WHERE dui=? AND id<>?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('si', $dui, $id);
    } else {
        $sql = "SELECT id FROM personal WHERE dui=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('s', $dui);
    }
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $_SESSION['personal_error'] = 'El DUI ya está registrado.';
        $_SESSION['personal_data'] = $_POST;
        header('Location: ' . ($id > 0 ? 'editar-personal.php?id=' . $id : 'agregar-personal.php'));
        exit;
    }
    $stmt->close();

    // Carpeta destino para imágenes
    $carpeta_destino = '../public/uploads/'; // ✅ Ruta corregida
    if (!file_exists($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }
    $ruta_imagen = '';
    if ($fotografia['name']) {
        $ruta_imagen = $carpeta_destino . basename($fotografia['name']);
        move_uploaded_file($fotografia['tmp_name'], $ruta_imagen);
    }

    if ($id > 0) {
        // Actualizar registro existente
        $quitar_foto = isset($_POST['quitar_foto']) ? intval($_POST['quitar_foto']) : 0;
        if ($quitar_foto === 1) {
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
            $sql = "UPDATE personal SET nombre=?, telefono=?, dui=?, fecha_nacimiento=?, departamento=?, distrito=?, colonia=?, calle=?, casa=?, estado_civil=?, fotografia='' WHERE id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('ssssssssssi', $nombre, $telefono, $dui, $fecha_nacimiento, $departamento, $distrito, $colonia, $calle, $casa, $estado_civil, $id);
        } elseif ($ruta_imagen) {
            $sql = "UPDATE personal SET nombre=?, telefono=?, dui=?, fecha_nacimiento=?, departamento=?, distrito=?, colonia=?, calle=?, casa=?, estado_civil=?, fotografia=? WHERE id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('sssssssssssi', $nombre, $telefono, $dui, $fecha_nacimiento, $departamento, $distrito, $colonia, $calle, $casa, $estado_civil, $ruta_imagen, $id);
        } else {
            $sql = "UPDATE personal SET nombre=?, telefono=?, dui=?, fecha_nacimiento=?, departamento=?, distrito=?, colonia=?, calle=?, casa=?, estado_civil=? WHERE id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('ssssssssssi', $nombre, $telefono, $dui, $fecha_nacimiento, $departamento, $distrito, $colonia, $calle, $casa, $estado_civil, $id);
        }
        $stmt->execute();
        $stmt->close();
        header('Location: personal.php');
        exit;
    } else {
        // Insertar nuevo registro
        $sql = "INSERT INTO personal (nombre, telefono, dui, fecha_nacimiento, departamento, distrito, colonia, calle, casa, estado_civil, fotografia) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('sssssssssss', $nombre, $telefono, $dui, $fecha_nacimiento, $departamento, $distrito, $colonia, $calle, $casa, $estado_civil, $ruta_imagen);
        $stmt->execute();
        $stmt->close();
        header('Location: personal.php');
        exit;
    }
}
?>