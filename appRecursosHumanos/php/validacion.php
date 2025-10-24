<?php
include_once __DIR__ . '/../conf/conf.php';
session_start();

$correo = htmlspecialchars(trim($_POST['email']));
$pwd = htmlspecialchars(trim($_POST['pwd']));
$pwdFormat = md5($pwd);

$consulta = "SELECT usuario FROM usuario WHERE email = '$correo' AND pwd = '$pwdFormat'";
$ejecucion = mysqli_query($con, $consulta);
$validar = mysqli_num_rows($ejecucion);

if ($validar > 0) {
    $usuario = mysqli_fetch_assoc($ejecucion);
    $_SESSION['usuario'] = $usuario['usuario'];
    header('Location: ../vistas/home.php');
    exit;
} else {
    header('Location: ../index.php?error=error');
    exit;
}
?>
