<?php
include_once __DIR__ . '/../conf/conf.php';

$id      = isset($_POST['id'])      ? mysqli_real_escape_string($con, $_POST['id']) : "";
$nombre  = isset($_POST['usuario']) ? mysqli_real_escape_string($con, $_POST['usuario']) : "";
$usuario = isset($_POST['correo'])  ? mysqli_real_escape_string($con, $_POST['correo']) : "";
$pwd     = isset($_POST['pwd'])     ? mysqli_real_escape_string($con, $_POST['pwd']) : "";
$passformat = md5($pwd);
$bandera = isset($_POST['bandera']) ? $_POST['bandera'] : "";
$rol_id  = isset($_POST['rol_id'])  ? mysqli_real_escape_string($con, $_POST['rol_id']) : "";

if ($bandera == 1) {
    // Validar que el rol sea obligatorio y válido
    if (!in_array($rol_id, ['1', '2'])) {
        header('Location: ../vistas/agregar-usuario.php?error=2&usuario=' . $nombre . '&correo=' . $usuario);
        exit;
    }

    // Verificar si el nombre de usuario ya existe
    $vericar_nombre = "SELECT * FROM usuario WHERE usuario = '$nombre'";
    $verificar_sql = mysqli_query($con, $vericar_nombre);

    if (mysqli_num_rows($verificar_sql) >= 1) {
        header('Location: ../vistas/agregar-usuario.php?error=1&usuario=' . $nombre . '&correo=' . $usuario);
        exit;
    } else {
        // Insertar nuevo usuario con rol
        $insertar = "INSERT INTO usuario (id, usuario, email, pwd, rol_id) VALUES (NULL, '$nombre', '$usuario', '$passformat', $rol_id)";
        $ejecucion = mysqli_query($con, $insertar);

        if ($ejecucion) {
            header('Location: ../vistas/usuarios.php');
            exit;
        } else {
            header('Location: ../vistas/agregar-usuario.php');
            exit;
        }
    }

} elseif ($bandera == 2) {
    // Actualización de usuario sin rol
    $estado_nombre = "SELECT usuario FROM usuario WHERE id = $id";
    $verificar_sql = mysqli_query($con, $estado_nombre);
    $verificador = mysqli_fetch_assoc($verificar_sql);
    $var_usuario = $verificador['usuario'];

    if ($var_usuario == $nombre) {
        $update_usuario = "UPDATE usuario SET usuario = '$nombre', email = '$usuario' WHERE id = $id";
        $update_ejecucion = mysqli_query($con, $update_usuario);

        if ($update_ejecucion) {
            header('Location: ../vistas/usuarios.php');
            exit;
        }
    } else {
        $verificar_nuevo = "SELECT usuario FROM usuario WHERE usuario = '$nombre'";
        $exec_verificacion = mysqli_query($con, $verificar_nuevo);

        if (mysqli_num_rows($exec_verificacion) >= 1) {
            header('Location: ../vistas/editar-usuario.php?error=1&id=' . $id);
            exit;
        } else {
            $update_usuario = "UPDATE usuario SET usuario = '$nombre', email = '$usuario' WHERE id = $id";
            $update_ejecucion = mysqli_query($con, $update_usuario);

            if ($update_ejecucion) {
                header('Location: ../vistas/usuarios.php');
                exit;
            }
        }
    }

} elseif ($bandera == 3) {
    $eliminar = "DELETE FROM usuario WHERE id = $id";
    $ejecutar_eliminar = mysqli_query($con, $eliminar);

    if ($ejecutar_eliminar) {
        header('Location: ../vistas/usuarios.php');
        exit;
    }

} elseif ($bandera == 6) {
    // Actualizar rol si se seleccionó
    if (!empty($rol_id)) {
        $update_rol = "UPDATE usuario SET rol_id = $rol_id WHERE id = $id";
        mysqli_query($con, $update_rol);
    }

    // Actualizar contraseña si se ingresó
    if (!empty($pwd)) {
        $update_pwd = "UPDATE usuario SET pwd = '$passformat' WHERE id = $id";
        mysqli_query($con, $update_pwd);
    }

    header('Location: ../vistas/usuarios.php');
    exit;
}
?>
