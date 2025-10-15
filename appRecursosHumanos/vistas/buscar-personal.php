<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(403); // Forbidden
    exit('Acceso denegado');
}

include_once __DIR__ . '/../conf/conf.php';

$busqueda = isset($_POST['busqueda']) ? $con->real_escape_string($_POST['busqueda']) : '';
$modales = '';

$sql = "SELECT * FROM personal";
if (!empty($busqueda)) {
    $param = "%" . $busqueda . "%";
    $sql .= " WHERE nombre LIKE ? OR dui LIKE ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ss', $param, $param);
} else {
    $stmt = $con->prepare($sql);
}

$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
?>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Avatar</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>DUI</th>
                <th>Departamento</th>
                <th>Estado Civil</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $avatar_default = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
            while ($row = $resultado->fetch_assoc()) {
                $avatar = $avatar_default;
                
                // ✅ --- INICIO DE LA CORRECCIÓN ---
                if (!empty($row['fotografia'])) {
                    // Ruta para el NAVEGADOR (src del <img>). Desde 'vistas' sube un nivel y entra a 'public/uploads'.
                    $ruta_web = '../public/uploads/' . basename($row['fotografia']);
                    
                    // Ruta para el SERVIDOR (para file_exists). Ruta física completa.
                    $ruta_servidor = __DIR__ . '/../public/uploads/' . basename($row['fotografia']);

                    if (file_exists($ruta_servidor)) {
                        $avatar = $ruta_web;
                    }
                }
                // ✅ --- FIN DE LA CORRECCIÓN ---
            ?>
            <tr>
                <td class="text-center"><img src="<?= $avatar ?>" alt="avatar"></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['telefono']) ?></td>
                <td><?= htmlspecialchars($row['dui']) ?></td>
                <td><?= htmlspecialchars($row['departamento']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['estado_civil'])) ?></td>
                <td>
                    <a href="editar-personal.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm" title="Modificar">✏️</a>
                    <a href="eliminar-personal.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Seguro que desea eliminar este registro?')">🗑️</a>
                    <button type="button" class="btn btn-info btn-sm" title="Ver Detalles" data-bs-toggle="modal" data-bs-target="#verModal<?= $row['id'] ?>">👁️</button>
                </td>
            </tr>
            <?php
            // La generación del modal para 'Ver' se mantiene igual
            $modales .= '
            <div class="modal fade" id="verModal'.$row['id'].'" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detalles de '.htmlspecialchars($row['nombre']).'</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-4 text-center">
                        <img src="'.$avatar.'" alt="avatar" class="img-thumbnail mb-3" width="150" height="150">
                      </div>
                      <div class="col-md-8">
                        <p><strong>Nombre:</strong> '.htmlspecialchars($row['nombre']).'</p>
                        <p><strong>Teléfono:</strong> '.htmlspecialchars($row['telefono']).'</p>
                        <p><strong>DUI:</strong> '.htmlspecialchars($row['dui']).'</p>
                        <p><strong>Fecha de nacimiento:</strong> '.htmlspecialchars($row['fecha_nacimiento']).'</p>
                        <p><strong>Dirección:</strong> '.htmlspecialchars($row['colonia'].', '.$row['calle'].', #'.$row['casa']).'</p>
                        <p><strong>Ubicación:</strong> '.htmlspecialchars($row['distrito'].', '.$row['departamento']).'</p>
                        <p><strong>Fecha de registro:</strong> '.htmlspecialchars($row['fecha_registro']).'</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            } // Fin del while
            ?>
        </tbody>
    </table>
    <?= $modales ?>
<?php
}
$stmt->close();
?>