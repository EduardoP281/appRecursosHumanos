<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

include_once __DIR__ . '/../conf/conf.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = isset($_GET['error']) ? intval($_GET['error']) : 0;

if ($id <= 0) {
    header('Location: usuarios.php');
    exit;
}

$seleccion = "SELECT usuario, email FROM usuario WHERE id = $id";
$ejecutar = mysqli_query($con, $seleccion);
$datos = mysqli_fetch_assoc($ejecutar);

if (!$datos) {
    echo "<div class='alert alert-danger text-center mt-5'>Usuario no encontrado.</div>";
    exit;
}

// Roles
$consulta_roles = "SELECT * FROM rol";
$ejecutar_roles = mysqli_query($con, $consulta_roles);
$roles = [];
while ($rol = mysqli_fetch_assoc($ejecutar_roles)) {
    $roles[] = $rol;
}

// Rol del usuario en sesión
$usuario_sesion = $_SESSION['usuario'];
$consulta_rol_sesion = "SELECT rol_id FROM usuario WHERE usuario = '$usuario_sesion'";
$ejecutar_rol_sesion = mysqli_query($con, $consulta_rol_sesion);
$rol_sesion = mysqli_fetch_assoc($ejecutar_rol_sesion)['rol_id'] ?? null;

// Rol del usuario editado
$consulta_rol_usuario = "SELECT rol_id FROM usuario WHERE id = $id";
$ejecutar_rol_usuario = mysqli_query($con, $consulta_rol_usuario);
$rol_usuario = mysqli_fetch_assoc($ejecutar_rol_usuario)['rol_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .contenido { margin: 40px; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="contenido">
    <div class="container">
        <h3 class="text-center mb-4">Editar Usuario</h3>

        <?php if ($error === 1): ?>
            <div class="alert alert-warning text-center">El nombre de usuario ya existe. Prueba con otro.</div>
        <?php endif; ?>

        <form action="../php/crud-usuarios.php" method="POST" class="form-control">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="bandera" value="6">

            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required value="<?= htmlspecialchars($datos['usuario']) ?>">

            <label for="correo" class="form-label">Correo</label>
            <input type="email" name="correo" id="correo" class="form-control" required value="<?= htmlspecialchars($datos['email']) ?>">

            <?php if ($rol_sesion == 1): ?>
                <label for="rol" class="form-label">Rol</label>
                <select name="rol_id" id="rol" class="form-select">
                    <option value="">Seleccionar rol</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id'] ?>" <?= $rol['id'] == $rol_usuario ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['rol']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="pwd" class="form-label mt-3">Nueva Contraseña (opcional)</label>
                <input type="password" name="pwd" id="pwd" class="form-control" placeholder="Ingrese una nueva contraseña">
            <?php endif; ?>

            <br>
            <button type="submit" class="form-control btn btn-primary">Guardar Cambios</button>
            <a href="usuarios.php" class="form-control btn btn-secondary mt-2">Cancelar</a>
        </form>
    </div>
</div>
</body>
</html>