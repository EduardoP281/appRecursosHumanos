<?php
session_start();

include_once __DIR__ . '/../conf/conf.php';


if (!isset($con) || $con->connect_error) {
    header('Location: ../index.php?error=db_conn_fail');
    exit;
}

$correo = trim($_POST['email'] ?? '');
$pwd = trim($_POST['pwd'] ?? '');

$sql = "SELECT usuario, pwd FROM usuario WHERE email = ?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    header('Location: ../index.php?error=sql_prepare_fail');
    exit;
}

$stmt->bind_param('s', $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    // El email existe, ahora verificamos la contraseña
    $usuario = $resultado->fetch_assoc();
    
    // 6. Verificamos la contraseña (manteniendo tu lógica MD5 por ahora)
    $pwdFormat = md5($pwd);
    
    // Usamos 'hash_equals' para una comparación segura contra ataques de tiempo
    if (hash_equals($usuario['pwd'], $pwdFormat)) {
        // ¡Contraseña correcta!
        $_SESSION['usuario'] = $usuario['usuario'];
        header('Location: ../vistas/home.php');
        exit;
    } else {
        // Contraseña incorrecta
        header('Location: ../index.php?error=error');
        exit;
    }

} else {
    // El email no existe
    header('Location: ../index.php?error=error');
    exit;
}

$stmt->close();
$con->close();
?>
