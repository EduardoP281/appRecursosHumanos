<?php
session_start();
if(!isset($_SESSION['usuario'])){
  Header('Location: index.php');
}
include_once('./conf/conf.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Personal</title>
  <style>
    .contenido{ margin:40px; }
  </style>
</head>
<body>
<?php include_once('nav.php'); ?>
<div class="contenido">
  <h2>Personal</h2>
  <a href="agregar-personal.php" class="btn btn-success mb-3">Agregar Personal</a>
</existing code>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Avatar</th>
        <th>Nombre</th>
        <th>Teléfono</th>
        <th>DUI</th>
        <th>Fecha Nacimiento</th>
        <th>Departamento</th>
        <th>Distrito</th>
        <th>Colonia</th>
        <th>Calle</th>
        <th>Casa</th>
        <th>Estado Civil</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $consulta = "SELECT * FROM personal";
    $resultado = mysqli_query($con, $consulta);
    $avatar_default = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
    $modales = '';
    while($row = mysqli_fetch_assoc($resultado)){
      // Determinar avatar
      $avatar = $avatar_default;
      if (!empty($row['fotografia'])) {
        $foto_path = 'uploads/' . basename($row['fotografia']);
        if (file_exists($foto_path)) {
          $avatar = $foto_path;
        }
      }
    ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><img src="<?php echo $avatar; ?>" alt="avatar" style="width:50px;height:50px;border-radius:50%;object-fit:cover;"></td>
      <td><?php echo $row['nombre']; ?></td>
      <td><?php echo $row['telefono']; ?></td>
      <td><?php echo $row['dui']; ?></td>
      <td><?php echo $row['fecha_nacimiento']; ?></td>
      <td><?php echo $row['departamento']; ?></td>
      <td><?php echo $row['distrito']; ?></td>
      <td><?php echo $row['colonia']; ?></td>
      <td><?php echo $row['calle']; ?></td>
      <td><?php echo $row['casa']; ?></td>
      <td><?php echo $row['estado_civil']; ?></td>
      <td><?php echo $row['fecha_registro']; ?></td>
      <td>
        <a href="editar-personal.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm me-1">Modificar</a>
        <a href="eliminar-personal.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar este registro?')">Eliminar</a>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#verModal<?php echo $row['id']; ?>">Ver</button>
      </td>
    </tr>
    <?php
    // Guardar el modal en una variable para imprimirlo después de la tabla
    $modales .= '<div class="modal fade" id="verModal'.$row['id'].'" tabindex="-1" aria-labelledby="verModalLabel'.$row['id'].'" aria-hidden="true">';
    $modales .= '<div class="modal-dialog modal-lg">';
    $modales .= '<div class="modal-content">';
    $modales .= '<div class="modal-header bg-info text-white">';
    $modales .= '<h5 class="modal-title" id="verModalLabel'.$row['id'].'">Detalles de '.$row['nombre'].'</h5>';
    $modales .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>';
    $modales .= '</div>';
    $modales .= '<div class="modal-body">';
    $modales .= '<div class="row">';
    $modales .= '<div class="col-md-4 text-center">';
    $modales .= '<img src="'.$avatar.'" alt="avatar" class="img-thumbnail" width="150" height="150">';
    $modales .= '</div>';
    $modales .= '<div class="col-md-8">';
    $modales .= '<p><strong>Nombre:</strong> '.$row['nombre'].'</p>';
    $modales .= '<p><strong>Teléfono:</strong> '.$row['telefono'].'</p>';
    $modales .= '<p><strong>DUI:</strong> '.$row['dui'].'</p>';
    $modales .= '<p><strong>Fecha de nacimiento:</strong> '.$row['fecha_nacimiento'].'</p>';
    $modales .= '<p><strong>Departamento:</strong> '.$row['departamento'].'</p>';
    $modales .= '<p><strong>Distrito:</strong> '.$row['distrito'].'</p>';
    $modales .= '<p><strong>Colonia:</strong> '.$row['colonia'].'</p>';
    $modales .= '<p><strong>Calle:</strong> '.$row['calle'].'</p>';
    $modales .= '<p><strong>Casa:</strong> '.$row['casa'].'</p>';
    $modales .= '<p><strong>Fecha de registro:</strong> '.$row['fecha_registro'].'</p>';
    $modales .= '</div>';
    $modales .= '</div>';
    $modales .= '</div>';
    $modales .= '<div class="modal-footer">';
    $modales .= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
    $modales .= '</div>';
    $modales .= '</div>';
    $modales .= '</div>';
    $modales .= '</div>';
    ?>
    <?php } ?>
    </tbody>
  </table>
  <?php echo $modales; ?>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</div>
</body>
</html>
