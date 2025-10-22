<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

include_once __DIR__ . '/../conf/conf.php';

// --- Configuración de Paginación ---
$registros_por_pagina = 10;
$pagina_actual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$busqueda = isset($_POST['busqueda']) ? $con->real_escape_string($_POST['busqueda']) : '';
$param_busqueda = "%" . $busqueda . "%";

// --- 1. Consulta para CONTAR el total de registros ---
$sql_conteo = "SELECT COUNT(id) AS total FROM personal";
if (!empty($busqueda)) {
    $sql_conteo .= " WHERE nombre LIKE ? OR dui LIKE ?";
    $stmt_conteo = $con->prepare($sql_conteo);
    $stmt_conteo->bind_param('ss', $param_busqueda, $param_busqueda);
} else {
    $stmt_conteo = $con->prepare($sql_conteo);
}
$stmt_conteo->execute();
$total_registros = $stmt_conteo->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$stmt_conteo->close();

// --- 2. Consulta para OBTENER los registros de la página actual ---
$sql = "SELECT * FROM personal";
if (!empty($busqueda)) {
    $sql .= " WHERE nombre LIKE ? OR dui LIKE ?";
}
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?"; 

if (!empty($busqueda)) {
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ssii', $param_busqueda, $param_busqueda, $registros_por_pagina, $offset);
} else {
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ii', $registros_por_pagina, $offset);
}

$stmt->execute();
$resultado = $stmt->get_result();

// --- 3. Generar HTML de la tabla ---
$tabla_html = '';
$modales_html = '';

if ($resultado->num_rows > 0) {
    $tabla_html = '
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Avatar</th>
                <th>Nombre</th>
                <th>DUI</th>
                <th>Departamento</th>
                <th>Distrito</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';

    $avatar_default = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
    while ($row = $resultado->fetch_assoc()) {
        $avatar = $avatar_default;
        $ruta_web_avatar = '';
        if (!empty($row['fotografia'])) {
            $ruta_web = '../public/uploads/' . basename($row['fotografia']);
            $ruta_servidor = __DIR__ . '/../public/uploads/' . basename($row['fotografia']);
            if (file_exists($ruta_servidor)) {
                $avatar = $ruta_web;
                $ruta_web_avatar = $ruta_web;
            }
        }
        
        $tabla_html .= '
        <tr>
            <td>'.$row['id'].'</td>
            <td class="text-center"><img src="'.$avatar.'" alt="avatar"></td>
            <td>'.htmlspecialchars($row['nombre']).'</td>
            <td>'.htmlspecialchars($row['dui']).'</td>
            <td>'.htmlspecialchars($row['departamento']).'</td>
            <td>'.htmlspecialchars($row['distrito']).'</td>
            <td>
                <button type="button" class="btn btn-primary btn-sm btn-editar" title="Modificar"
                    data-bs-toggle="modal" data-bs-target="#editarPersonalModal"
                    data-id="'.$row['id'].'"
                    data-nombre="'.htmlspecialchars($row['nombre']).'"
                    data-telefono="'.htmlspecialchars($row['telefono']).'"
                    data-dui="'.htmlspecialchars($row['dui']).'"
                    data-fecha_nacimiento="'.htmlspecialchars($row['fecha_nacimiento']).'"
                    data-departamento="'.htmlspecialchars($row['departamento']).'"
                    data-distrito="'.htmlspecialchars($row['distrito']).'"
                    data-colonia="'.htmlspecialchars($row['colonia']).'"
                    data-calle="'.htmlspecialchars($row['calle']).'"
                    data-casa="'.htmlspecialchars($row['casa']).'"
                    data-estado_civil="'.htmlspecialchars($row['estado_civil']).'"
                    data-fotografia="'.htmlspecialchars($ruta_web_avatar).'">
                    ✏️
                </button>
                
                <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="'.$row['id'].'" data-nombre="'.htmlspecialchars($row['nombre']).'" title="Eliminar">🗑️</button>

                <button type="button" class="btn btn-info btn-sm" title="Ver Detalles" data-bs-toggle="modal" data-bs-target="#verModal'.$row['id'].'">👁️</button>
            </td>
        </tr>';
        
        // ✅ --- INICIO DE LA CORRECCIÓN ---
        // Se ha restaurado el contenido completo del modal "Ver Detalles".
        $modales_html .= '
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
                    <p><strong>Estado Civil:</strong> '.htmlspecialchars(ucfirst($row['estado_civil'])).'</p>
                    <p><strong>Fecha de registro:</strong> '.htmlspecialchars($row['fecha_registro']).'</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>';
        // ✅ --- FIN DE LA CORRECCIÓN ---
        
    } // Fin del while

    $tabla_html .= '</tbody></table>' . $modales_html;
}
$stmt->close();

// --- 4. Generar HTML de la Paginación (sin cambios) ---
$paginacion_html = '';
if ($total_paginas > 1) {
    $paginacion_html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    $clase_anterior = ($pagina_actual == 1) ? 'disabled' : '';
    $paginacion_html .= '<li class="page-item '.$clase_anterior.'">
        <a class="page-link" href="#" data-pagina="'.($pagina_actual - 1).'">Anterior</a>
    </li>';
    
    // Aquí puedes añadir un bucle para más números de página si lo deseas
    $paginacion_html .= '<li class="page-item active" aria-current="page">
        <a class="page-link" href="#" data-pagina="'.$pagina_actual.'">'.$pagina_actual.'</a>
    </li>';
    
    $clase_siguiente = ($pagina_actual >= $total_paginas) ? 'disabled' : '';
    $paginacion_html .= '<li class="page-item '.$clase_siguiente.'">
        <a class="page-link" href="#" data-pagina="'.($pagina_actual + 1).'">Siguiente</a>
    </li>';
    
    $paginacion_html .= '</ul></nav>';
    $paginacion_html .= '<p class="text-center text-muted small">Página '.$pagina_actual.' de '.$total_paginas.'</p>';
}

// --- 5. Devolver JSON (sin cambios) ---
header('Content-Type: application/json');
echo json_encode([
    'tablaHTML' => $tabla_html,
    'paginacionHTML' => $paginacion_html
]);
?>