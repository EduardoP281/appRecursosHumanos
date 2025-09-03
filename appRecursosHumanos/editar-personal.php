<?php
$sesion_estado = session_status();
if ($sesion_estado === PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['usuario'])){
    Header('Location: index.php');
}
include_once('./conf/conf.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = isset($_SESSION['personal_error']) ? $_SESSION['personal_error'] : '';
$data = isset($_SESSION['personal_data']) ? $_SESSION['personal_data'] : [];
$datos = [
    'nombre' => '', 'telefono' => '', 'dui' => '', 'fecha_nacimiento' => '',
    'departamento' => '', 'distrito' => '', 'colonia' => '', 'calle' => '', 'casa' => '',
    'estado_civil' => '', 'fotografia' => ''
];
if ($id > 0) {
    $sql = "SELECT * FROM personal WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $datos = $res->fetch_assoc();
    }
    $stmt->close();
}
if($data){
    foreach($data as $k=>$v){
        if(isset($datos[$k])) $datos[$k] = $v;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once('nav.php'); ?>
<div class="container mt-4">
    <h2>Editar Personal</h2>
    <?php if($error){ echo '<div class="alert alert-danger">'.$error.'</div>'; unset($_SESSION['personal_error']); } ?>
    <form action="guardar-personal.php" method="POST" enctype="multipart/form-data" class="form-control">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($datos['nombre']); ?>">
        <br>
        <label>Teléfono:</label>
        <input type="text" name="telefono" class="form-control" required value="<?php echo htmlspecialchars($datos['telefono']); ?>">
        <br>
        <label>DUI:</label>
        <input type="text" name="dui" class="form-control" required value="<?php echo htmlspecialchars($datos['dui']); ?>">
        <br>
        <label>Fecha de Nacimiento:</label>
        <input type="date" name="fecha_nacimiento" class="form-control" required value="<?php echo htmlspecialchars($datos['fecha_nacimiento']); ?>">
        <br>
        <label>Departamento:</label>
        <input type="text" name="departamento" class="form-control" required value="<?php echo htmlspecialchars($datos['departamento']); ?>">
        <br>
        <label>Distrito:</label>
        <input type="text" name="distrito" class="form-control" required value="<?php echo htmlspecialchars($datos['distrito']); ?>">
        <br>
        <label>Dirección:</label><br>
        <input type="text" name="colonia" class="form-control mb-1" placeholder="Colonia" required value="<?php echo htmlspecialchars($datos['colonia']); ?>">
        <input type="text" name="calle" class="form-control mb-1" placeholder="Calle" required value="<?php echo htmlspecialchars($datos['calle']); ?>">
        <input type="text" name="casa" class="form-control mb-1" placeholder="Casa" required value="<?php echo htmlspecialchars($datos['casa']); ?>">
        <br>
        <label>Estado Civil:</label>
        <select name="estado_civil" class="form-control" required>
            <option value="soltero" <?php if($datos['estado_civil']=='soltero') echo 'selected'; ?>>Soltero</option>
            <option value="casado" <?php if($datos['estado_civil']=='casado') echo 'selected'; ?>>Casado</option>
            <option value="divorciado" <?php if($datos['estado_civil']=='divorciado') echo 'selected'; ?>>Divorciado</option>
            <option value="viudo" <?php if($datos['estado_civil']=='viudo') echo 'selected'; ?>>Viudo</option>
        </select>
        <br>
        <label>Fotografía:</label>
        <input type="file" name="fotografia" class="form-control" accept="image/*">
        <?php if($datos['fotografia']){
            echo '<br><img src="'.$datos['fotografia'].'" style="width:80px;">';
            echo '<br><input type="checkbox" name="quitar_foto" value="1"> Quitar imagen de perfil';
        } ?>
        <br>
        <input type="submit" value="Guardar Cambios" class="btn btn-primary">
        <a href="personal.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
