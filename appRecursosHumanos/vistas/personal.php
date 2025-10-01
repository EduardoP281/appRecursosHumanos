<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

include_once __DIR__ . '/../conf/conf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .contenido { margin: 40px; }
    .table img { object-fit: cover; border-radius: 50%; }
  </style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="contenido">
  <h2>Personal</h2>
  <a href="agregar-personal.php" class="btn btn-success mb-3">Agregar Personal</a>

  <table class="table table-bordered">
    <thead class="table-light">
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

    while ($row = mysqli_fetch_assoc($resultado)) {
        $avatar = $avatar_default;
        if (!empty($row['fotografia'])) {
            $foto_path = '../public/uploads/' . basename($row['fotografia']);
            if (file_exists($foto_path)) {
                $avatar = $foto_path;
            }
        }
    ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><img src="<?= $avatar ?>" alt="avatar" width="50" height="50"></td>
      <td><?= htmlspecialchars($row['nombre']) ?></td>
      <td><?= htmlspecialchars($row['telefono']) ?></td>
      <td><?= htmlspecialchars($row['dui']) ?></td>
      <td><?= htmlspecialchars($row['fecha_nacimiento']) ?></td>
      <td><?= htmlspecialchars($row['departamento']) ?></td>
      <td><?= htmlspecialchars($row['distrito']) ?></td>
      <td><?= htmlspecialchars($row['colonia']) ?></td>
      <td><?= htmlspecialchars($row['calle']) ?></td>
      <td><?= htmlspecialchars($row['casa']) ?></td>
      <td><?= htmlspecialchars($row['estado_civil']) ?></td>
      <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
      <td>
        <a href="editar-personal.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm me-1">Modificar</a>
        <a href="eliminar-personal.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar este registro?')">Eliminar</a>
        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#verModal<?= $row['id'] ?>">Ver</button>
      </td>
    </tr>
    <?php
    $modales .= '
    <div class="modal fade" id="verModal'.$row['id'].'" tabindex="-1" aria-labelledby="verModalLabel'.$row['id'].'" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title" id="verModalLabel'.$row['id'].'">Detalles de '.$row['nombre'].'</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-4 text-center">
                <img src="'.$avatar.'" alt="avatar" class="img-thumbnail" width="150" height="150">
              </div>
              <div class="col-md-8">
                <p><strong>Nombre:</strong> '.$row['nombre'].'</p>
                <p><strong>Teléfono:</strong> '.$row['telefono'].'</p>
                <p><strong>DUI:</strong> '.$row['dui'].'</p>
                <p><strong>Fecha de nacimiento:</strong> '.$row['fecha_nacimiento'].'</p>
                <p><strong>Departamento:</strong> '.$row['departamento'].'</p>
                <p><strong>Distrito:</strong> '.$row['distrito'].'</p>
                <p><strong>Colonia:</strong> '.$row['colonia'].'</p>
                <p><strong>Calle:</strong> '.$row['calle'].'</p>
                <p><strong>Casa:</strong> '.$row['casa'].'</p>
                <p><strong>Fecha de registro:</strong> '.$row['fecha_registro'].'</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>';
    } ?>
    </tbody>
  </table>
  <?= $modales ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>