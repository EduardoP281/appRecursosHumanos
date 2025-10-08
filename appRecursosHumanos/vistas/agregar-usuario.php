<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

include_once __DIR__ . '/../conf/conf.php';

// ✅ Sanitizar parámetros GET
$nombre  = isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : '';
$usuario = isset($_GET['correo'])  ? htmlspecialchars($_GET['correo'])  : '';
$error   = isset($_GET['error'])   ? intval($_GET['error']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .contenido { margin: 40px; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="contenido">
    <div class="container">
        <h3 class="text-center mb-4">Agregar Nuevo Usuario</h3>

        <?php if ($error === 1): ?>
            <div class="alert alert-danger text-center">El nombre de usuario ya existe. Prueba con otro.</div>
        <?php endif; ?>

        <form action="../php/crud-usuarios.php" method="POST" class="form-control">
            <input type="hidden" name="bandera" value="1">

            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" pattern="^[a-zA-Z0-9_]{4,50}$" required placeholder="Ingrese su nombre de usuario" value="<?= $nombre ?>">

            <label for="correo" class="form-label">Correo</label>
            <input type="email" name="correo" id="correo" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required placeholder="Ingrese su correo válido" value="<?= $usuario ?>">

            <label for="pwd" class="form-label">Contraseña</label>
            <input type="password" name="pwd" id="pwd" class="form-control" pattern="(?=.*[a-z])(?=.*[A-Z])[A-Za-z\d@$!%*?&]{8,}" required placeholder="*********">
            <br>

             <label for="rol_id" class="form-label mt-3">Rol</label>
            <select name="rol_id" id="rol_id" class="form-control" required>
                <option value="" disabled selected>Seleccione un rol</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Superadmin</option>
            </select>
            
            <input type="submit" class="form-control btn btn-primary" value="Registrar">
        </form>
    </div>
</div>
</body>

</html>
