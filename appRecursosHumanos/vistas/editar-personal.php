<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

include_once __DIR__ . '/../conf/conf.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: personal.php');
    exit;
}

// Obtener los datos actuales del personal para rellenar el formulario
$sql = "SELECT * FROM personal WHERE id=?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 0) {
    // Si no se encuentra un usuario con ese ID, redirigir
    header('Location: personal.php');
    exit;
}
$datos = $resultado->fetch_assoc();
$stmt->close();
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
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="container mt-4">
    <h2>Editar Personal</h2>
    
    <div id="error-container" class="alert alert-danger" style="display: none;"></div>
    
    <form id="formEditarPersonal" action="guardar-personal.php" method="POST" enctype="multipart/form-data" class="border p-4 rounded">
        <input type="hidden" name="id" value="<?= htmlspecialchars($datos['id']) ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required value="<?= htmlspecialchars($datos['nombre']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control" required value="<?= htmlspecialchars($datos['telefono']) ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="dui" class="form-label">DUI:</label>
                <input type="text" id="dui" name="dui" class="form-control" required value="<?= htmlspecialchars($datos['dui']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required value="<?= htmlspecialchars($datos['fecha_nacimiento']) ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="departamento" class="form-label">Departamento:</label>
                <input type="text" id="departamento" name="departamento" class="form-control" required value="<?= htmlspecialchars($datos['departamento']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="distrito" class="form-label">Distrito:</label>
                <input type="text" id="distrito" name="distrito" class="form-control" required value="<?= htmlspecialchars($datos['distrito']) ?>">
            </div>
        </div>
        
        <label class="form-label">Dirección:</label>
        <div class="input-group mb-3">
            <input type="text" name="colonia" class="form-control" placeholder="Colonia" required value="<?= htmlspecialchars($datos['colonia']) ?>">
            <input type="text" name="calle" class="form-control" placeholder="Calle" required value="<?= htmlspecialchars($datos['calle']) ?>">
            <input type="text" name="casa" class="form-control" placeholder="Casa #" required value="<?= htmlspecialchars($datos['casa']) ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="estado_civil" class="form-label">Estado Civil:</label>
                <select id="estado_civil" name="estado_civil" class="form-select" required>
                    <option value="soltero" <?= $datos['estado_civil'] == 'soltero' ? 'selected' : '' ?>>Soltero</option>
                    <option value="casado" <?= $datos['estado_civil'] == 'casado' ? 'selected' : '' ?>>Casado</option>
                    <option value="divorciado" <?= $datos['estado_civil'] == 'divorciado' ? 'selected' : '' ?>>Divorciado</option>
                    <option value="viudo" <?= $datos['estado_civil'] == 'viudo' ? 'selected' : '' ?>>Viudo</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="fotografia" class="form-label">Cambiar Fotografía:</label>
                <input type="file" id="fotografia" name="fotografia" class="form-control" accept="image/*">
                <?php if (!empty($datos['fotografia'])): 
                    $foto_path = '../public/uploads/' . basename($datos['fotografia']);
                    if (file_exists($foto_path)):
                ?>
                    <div class="mt-2">
                        <img src="<?= $foto_path ?>" alt="Foto actual" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                        <div class="form-check d-inline-block ms-3 align-middle">
                            <input class="form-check-input" type="checkbox" name="quitar_foto" value="1" id="quitar_foto">
                            <label class="form-check-label" for="quitar_foto">Quitar foto de perfil</label>
                        </div>
                    </div>
                <?php endif; endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="personal.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#formEditarPersonal').on('submit', function(e) {
        e.preventDefault(); // Evitamos el envío normal

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // ÉXITO: Redirigimos a la lista de personal
                    window.location.href = 'personal.php';
                } else {
                    // ERROR: Mostramos el mensaje en el contenedor
                    $('#error-container').text(response.message).show();
                    // Hacemos scroll hasta arriba para que el usuario vea el error
                    window.scrollTo(0, 0);
                }
            },
            error: function() {
                // Para errores de conexión o del servidor
                $('#error-container').text('Ocurrió un error inesperado. Inténtelo de nuevo.').show();
                window.scrollTo(0, 0);
            }
        });
    });
});
</script>

</body>
</html>