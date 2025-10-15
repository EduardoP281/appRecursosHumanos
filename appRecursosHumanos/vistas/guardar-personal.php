<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. Sesión no iniciada.']);
    exit;
}

include_once __DIR__ . '/../conf/conf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
    exit;
}

// --- Recopilación y limpieza de datos ---
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$dui = trim($_POST['dui'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$departamento = trim($_POST['departamento'] ?? '');
$distrito = trim($_POST['distrito'] ?? '');
$colonia = trim($_POST['colonia'] ?? '');
$calle = trim($_POST['calle'] ?? '');
$casa = trim($_POST['casa'] ?? '');
$estado_civil = trim($_POST['estado_civil'] ?? '');
$fotografia = $_FILES['fotografia'] ?? null;
$quitar_foto = isset($_POST['quitar_foto']) && $_POST['quitar_foto'] == '1';

// --- Validación básica del lado del servidor ---
if (empty($nombre) || empty($telefono) || empty($dui)) {
    echo json_encode(['status' => 'error', 'message' => 'Nombre, teléfono y DUI son campos obligatorios.']);
    exit;
}

// --- Validación de DUI único ---
$sql_dui = $id > 0 ? "SELECT id, fotografia FROM personal WHERE dui=? AND id<>?" : "SELECT id FROM personal WHERE dui=?";
$stmt_dui = $con->prepare($sql_dui);
if ($id > 0) {
    $stmt_dui->bind_param('si', $dui, $id);
} else {
    $stmt_dui->bind_param('s', $dui);
}
$stmt_dui->execute();
if ($stmt_dui->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'El DUI ya está registrado.']);
    $stmt_dui->close();
    exit;
}
$stmt_dui->close();

// --- Obtener foto antigua (necesario para borrarla si se actualiza) ---
$foto_antigua = '';
if ($id > 0) {
    $stmt_foto = $con->prepare("SELECT fotografia FROM personal WHERE id=?");
    $stmt_foto->bind_param('i', $id);
    if ($stmt_foto->execute()) {
        $res = $stmt_foto->get_result();
        if ($res->num_rows > 0) {
            $foto_antigua = $res->fetch_assoc()['fotografia'];
        }
    }
    $stmt_foto->close();
}

// --- Manejo de la subida de imagen ---
$ruta_imagen_db = null;
if ($fotografia && $fotografia['error'] === UPLOAD_ERR_OK) {
    $carpeta_destino = __DIR__ . '/../public/uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jfif'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    if (!in_array($fotografia['type'], $allowed_types) || $fotografia['size'] > $max_size) {
        echo json_encode(['status' => 'error', 'message' => 'Archivo no válido o demasiado grande (Máx 5MB).']);
        exit;
    }

    $nombre_archivo = uniqid() . '-' . basename($fotografia['name']);
    if (move_uploaded_file($fotografia['tmp_name'], $carpeta_destino . $nombre_archivo)) {
        $ruta_imagen_db = $nombre_archivo;
        if ($foto_antigua && file_exists($carpeta_destino . $foto_antigua)) {
            unlink($carpeta_destino . $foto_antigua);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar el archivo de imagen.']);
        exit;
    }
} else if ($quitar_foto) {
    $ruta_imagen_db = '';
    $carpeta_destino = __DIR__ . '/../public/uploads/';
    if ($foto_antigua && file_exists($carpeta_destino . $foto_antigua)) {
        unlink($carpeta_destino . $foto_antigua);
    }
}

// --- Operación en Base de Datos ---
if ($id > 0) {
    // Lógica de ACTUALIZACIÓN
    $sql_parts = [
        "nombre=?", "telefono=?", "dui=?", "fecha_nacimiento=?",
        "departamento=?", "distrito=?", "colonia=?", "calle=?",
        "casa=?", "estado_civil=?"
    ];
    $params = [
        $nombre, $telefono, $dui, $fecha_nacimiento, $departamento,
        $distrito, $colonia, $calle, $casa, $estado_civil
    ];
    $types = 'ssssssssss';

    if ($ruta_imagen_db !== null) {
        $sql_parts[] = "fotografia=?";
        $params[] = $ruta_imagen_db;
        $types .= 's';
    }

    $params[] = $id;
    $types .= 'i';
    
    $sql = "UPDATE personal SET " . implode(', ', $sql_parts) . " WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Personal actualizado con éxito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar en la base de datos.']);
    }
    $stmt->close();

} else { // ✅ LA LLAVE FALTANTE ESTABA ANTES DE ESTE "ELSE"
    // Lógica de INSERCIÓN
    $sql = "INSERT INTO personal (nombre, telefono, dui, fecha_nacimiento, departamento, distrito, colonia, calle, casa, estado_civil, fotografia, fecha_registro) VALUES (?,?,?,?,?,?,?,?,?,?,?, NOW())";
    $stmt = $con->prepare($sql);
    $ruta_final = $ruta_imagen_db ?? '';
    $stmt->bind_param('sssssssssss', $nombre, $telefono, $dui, $fecha_nacimiento, $departamento, $distrito, $colonia, $calle, $casa, $estado_civil, $ruta_final);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Personal registrado con éxito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
    }
    $stmt->close();
}

$con->close();
?>