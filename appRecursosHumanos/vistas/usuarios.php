<?php
session_start();

// ✅ Corrección de validación de sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

// ✅ Corrección de ruta absoluta
include_once __DIR__ . '/../conf/conf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .contenido { margin: 40px; }
        .table { background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .btn { margin: 0 5px; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Gestión de Usuarios</h4>
                </div>
                <div class="card-body">

                    <?php
                    $usuario_sesion = $_SESSION['usuario'];

                    // ✅ Sanitizar búsqueda
                    $busqueda = isset($_POST['busqueda']) ? mysqli_real_escape_string($con, $_POST['busqueda']) : '';

                    // ✅ Consulta de roles
                    $roles = [];
                    $consulta_roles = "SELECT id, rol FROM rol";
                    $resultado_roles = mysqli_query($con, $consulta_roles);
                    while ($rol = mysqli_fetch_assoc($resultado_roles)) {
                        $roles[$rol['id']] = $rol['rol'];
                    }

                    // ✅ Consulta de rol del usuario en sesión
                    $consulta_rol_sesion = "SELECT rol_id FROM usuario WHERE usuario = '$usuario_sesion'";
                    $resultado_rol_sesion = mysqli_query($con, $consulta_rol_sesion);
                    $rol_sesion = mysqli_fetch_assoc($resultado_rol_sesion)['rol_id'];

                    // ✅ Consulta de usuarios
                    $consulta_usuarios = "SELECT * FROM usuario";
                    if (!empty($busqueda)) {
                        $consulta_usuarios .= " WHERE usuario LIKE '%$busqueda%' OR email LIKE '%$busqueda%'";
                    }
                    $resultado_usuarios = mysqli_query($con, $consulta_usuarios);
                    ?>

                    <div class="contenido">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <form action="usuarios.php" method="POST" class="d-flex">
                                    <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar por usuario o correo" >
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </form>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="agregar-usuario.php" class="btn btn-success">Nuevo Usuario</a>
                            </div>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) {
                                    if ($usuario['usuario'] === $usuario_sesion) continue;

                                    $rol_nombre = $roles[$usuario['rol_id']] ?? 'Sin asignar';
                                    ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                                        <td><?= htmlspecialchars($rol_nombre) ?></td>
                                        <td>
                                            <a href="editar-usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                        </td>
                                        <td>
                                            <form action="../php/crud-usuarios.php" method="POST" onsubmit="return confirm('¿Eliminar este usuario?')">
                                                <input type="hidden" name="bandera" value="3">
                                                <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    // ✅ Actualización de rol y contraseña (bandera 6)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['bandera'] == '6') {
                        $id = mysqli_real_escape_string($con, $_POST['id']);
                        $rol_id = isset($_POST['rol_id']) ? mysqli_real_escape_string($con, $_POST['rol_id']) : null;
                        $pwd = isset($_POST['pwd']) ? mysqli_real_escape_string($con, $_POST['pwd']) : null;

                        if ($rol_id) {
                            mysqli_query($con, "UPDATE usuario SET rol_id = '$rol_id' WHERE id = '$id'");
                        }

                        if ($pwd) {
                            $hashed_pwd = md5($pwd); // ⚠️ Recomendado: usar password_hash()
                            mysqli_query($con, "UPDATE usuario SET pwd = '$hashed_pwd' WHERE id = '$id'");
                        }

                        if (!headers_sent()) {
                            header('Location: usuarios.php');
                            exit;
                        }
                    }

                    mysqli_close($con);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>
