<?php
session_start();
if(!isset($_SESSION['usuario'])){
    Header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once('nav.php'); ?>
<div class="container mt-4">
    <h2>Registrar Personal</h2>
    <?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $error = isset($_SESSION['personal_error']) ? $_SESSION['personal_error'] : '';
    $data = isset($_SESSION['personal_data']) ? $_SESSION['personal_data'] : [];
    if($error){
        echo '<div class="alert alert-danger">'.$error.'</div>';
        unset($_SESSION['personal_error']);
    }
    ?>
    <form action="guardar-personal.php" method="POST" enctype="multipart/form-data" class="form-control">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" required value="<?php echo isset($data['nombre']) ? htmlspecialchars($data['nombre']) : ''; ?>">
        <br>
        <label>Teléfono:</label>
        <input type="text" name="telefono" class="form-control" required value="<?php echo isset($data['telefono']) ? htmlspecialchars($data['telefono']) : ''; ?>">
        <br>
        <label>DUI:</label>
        <input type="text" name="dui" class="form-control" required value="<?php echo isset($data['dui']) ? htmlspecialchars($data['dui']) : ''; ?>">
        <br>
        <label>Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" class="form-control" required value="<?php echo isset($data['fecha_nacimiento']) ? htmlspecialchars($data['fecha_nacimiento']) : ''; ?>">
        <br>
        <label>Departamento:</label>
        <input type="text" name="departamento" class="form-control" required value="<?php echo isset($data['departamento']) ? htmlspecialchars($data['departamento']) : ''; ?>">
        <br>
        <label>Distrito:</label>
        <input type="text" name="distrito" class="form-control" required value="<?php echo isset($data['distrito']) ? htmlspecialchars($data['distrito']) : ''; ?>">
        <br>
        <label>Dirección:</label><br>
        <input type="text" name="colonia" class="form-control mb-1" placeholder="Colonia" required value="<?php echo isset($data['colonia']) ? htmlspecialchars($data['colonia']) : ''; ?>">
        <input type="text" name="calle" class="form-control mb-1" placeholder="Calle" required value="<?php echo isset($data['calle']) ? htmlspecialchars($data['calle']) : ''; ?>">
        <input type="text" name="casa" class="form-control mb-1" placeholder="Casa" required value="<?php echo isset($data['casa']) ? htmlspecialchars($data['casa']) : ''; ?>">
        <br>
        <label>Estado Civil:</label>
        <select name="estado_civil" class="form-control" required>
            <option value="soltero" <?php echo (isset($data['estado_civil']) && $data['estado_civil']=='soltero') ? 'selected' : ''; ?>>Soltero</option>
            <option value="casado" <?php echo (isset($data['estado_civil']) && $data['estado_civil']=='casado') ? 'selected' : ''; ?>>Casado</option>
            <option value="divorciado" <?php echo (isset($data['estado_civil']) && $data['estado_civil']=='divorciado') ? 'selected' : ''; ?>>Divorciado</option>
            <option value="viudo" <?php echo (isset($data['estado_civil']) && $data['estado_civil']=='viudo') ? 'selected' : ''; ?>>Viudo</option>
        </select>
        <br>
        <label>Fotografía:</label>
    <input type="file" name="fotografia" class="form-control" accept="image/*">
        <br>
        <input type="submit" value="Enviar" class="btn btn-primary">
        <a href="personal.php" class="btn btn-secondary">Cancelar</a>
    </form>
    <?php unset($_SESSION['personal_data']); ?>
</div>
</body>
</html>
