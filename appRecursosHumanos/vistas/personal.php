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
        .table img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
        #no-results { display: none; }
    </style>
</head>
<body>
<?php include_once __DIR__ . '/nav.php'; ?>

<div class="contenido">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2>Personal</h2>
        <input type="text" id="searchInput" class="form-control" style="max-width: 300px;" placeholder="Buscar por nombre o DUI...">
    </div>
    
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#agregarPersonalModal">
        Agregar Personal
    </button>
    
    <div class="table-responsive">
        <div id="tabla-personal-container">
            </div>
        <div id="no-results" class="alert alert-warning text-center">
            No se encontraron registros que coincidan con la búsqueda.
        </div>
    </div>
</div>

<div class="modal fade" id="agregarPersonalModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Registrar Nuevo Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarPersonal" action="guardar-personal.php" method="POST" enctype="multipart/form-data">
                    <div id="modal-error" class="alert alert-danger" style="display: none;"></div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre completo:</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="text" id="telefono" name="telefono" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dui" class="form-label">DUI:</label>
                            <input type="text" id="dui" name="dui" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departamento" class="form-label">Departamento:</label>
                            <input type="text" id="departamento" name="departamento" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="distrito" class="form-label">Distrito:</label>
                            <input type="text" id="distrito" name="distrito" class="form-control" required>
                        </div>
                    </div>

                    <label class="form-label">Dirección:</label>
                    <div class="input-group mb-3">
                         <input type="text" name="colonia" class="form-control" placeholder="Colonia" required>
                         <input type="text" name="calle" class="form-control" placeholder="Calle" required>
                         <input type="text" name="casa" class="form-control" placeholder="Casa #" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                             <label for="estado_civil" class="form-label">Estado Civil:</label>
                             <select id="estado_civil" name="estado_civil" class="form-select" required>
                                 <option value="" disabled selected>Seleccione una opción...</option>
                                 <option value="soltero">Soltero(a)</option>
                                 <option value="casado">Casado(a)</option>
                                 <option value="divorciado">Divorciado(a)</option>
                                 <option value="viudo">Viudo(a)</option>
                             </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="fotografia" class="form-label">Fotografía:</label>
                            <input type="file" id="fotografia" name="fotografia" class="form-control" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Personal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    // --- LÓGICA DE BÚSQUEDA Y CARGA DE TABLA ---
    function cargarTabla(terminoBusqueda = '') {
        $.ajax({
            url: 'buscar-personal.php',
            type: 'POST',
            data: { busqueda: terminoBusqueda },
            success: function(response) {
                if (response.trim() !== "") {
                    $('#tabla-personal-container').html(response).show();
                    $('#no-results').hide();
                } else {
                    $('#tabla-personal-container').hide();
                    $('#no-results').show();
                }
            },
            error: function() {
                 $('#tabla-personal-container').hide();
                 $('#no-results').show().text('Error al cargar los datos.');
            }
        });
    }

    cargarTabla();

    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        const busqueda = $(this).val();
        searchTimeout = setTimeout(function() {
            cargarTabla(busqueda);
        }, 300); // Espera 300ms antes de buscar para no saturar
    });

    // --- LÓGICA PARA EL MODAL DE AGREGAR ---
    $('#formAgregarPersonal').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#agregarPersonalModal').modal('hide');
                    cargarTabla(); 
                } else {
                    $('#modal-error').text(response.message).show();
                }
            },
            error: function() {
                $('#modal-error').text('Ocurrió un error inesperado al guardar.').show();
            }
        });
    });

    $('#agregarPersonalModal').on('hidden.bs.modal', function () {
        $('#formAgregarPersonal')[0].reset();
        $('#modal-error').hide();
    });
});
</script>

</body>
</html>