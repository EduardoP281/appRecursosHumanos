<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
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
        #imagen-previa-editar { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; display: none; }
        .page-link { cursor: pointer; }
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
            No se encontraron registros.
        </div>
    </div>
    
    <div id="paginacion-container" class="mt-4">
    </div>
</div>

<div class="modal fade" id="agregarPersonalModal" tabindex="-1" aria-labelledby="modalLabelAgregar" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelAgregar">Registrar Nuevo Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarPersonal" action="guardar-personal.php" method="POST" enctype="multipart/form-data">
                    <div id="modal-error-agregar" class="alert alert-danger" style="display: none;"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_agregar" class="form-label">Nombre completo:</label>
                            <input type="text" id="nombre_agregar" name="nombre" class="form-control" 
                                   pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,}" title="Solo letras y espacios, mínimo 3 caracteres." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono_agregar" class="form-label">Teléfono:</label>
                            <input type="text" id="telefono_agregar" name="telefono" class="form-control" 
                                   pattern="[\d ()+-]{8,20}" title="Teléfono válido (8-20 caracteres). Puede incluir +, (), - y espacios." required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dui_agregar" class="form-label">DUI:</label>
                            <input type="text" id="dui_agregar" name="dui" class="form-control" 
                                   pattern="[0-9]{8}-?[0-9]{1}" title="Formato: 12345678-9. El guion es opcional." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_nacimiento_agregar" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" id="fecha_nacimiento_agregar" name="fecha_nacimiento" class="form-control" required>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departamento_agregar" class="form-label">Departamento:</label>
                            <input type="text" id="departamento_agregar" name="departamento" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="distrito_agregar" class="form-label">Distrito:</label>
                            <input type="text" id="distrito_agregar" name="distrito" class="form-control" required>
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
                             <label for="estado_civil_agregar" class="form-label">Estado Civil:</label>
                             <select id="estado_civil_agregar" name="estado_civil" class="form-select" required>
                                 <option value="" disabled selected>Seleccione...</option>
                                 <option value="soltero">Soltero(a)</option>
                                 <option value="casado">Casado(a)</option>
                                 <option value="divorciado">Divorciado(a)</option>
                                 <option value="viudo">Viudo(a)</option>
                             </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="fotografia_agregar" class="form-label">Fotografía:</label>
                            <input type="file" id="fotografia_agregar" name="fotografia" class="form-control" accept="image/*">
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

<div class="modal fade" id="editarPersonalModal" tabindex="-1" aria-labelledby="modalLabelEditar" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelEditar">Editar Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPersonal" action="guardar-personal.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="id_editar" name="id">
                    <div id="modal-error-editar" class="alert alert-danger" style="display: none;"></div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_editar" class="form-label">Nombre completo:</label>
                            <input type="text" id="nombre_editar" name="nombre" class="form-control" 
                                   pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,}" title="Solo letras y espacios, mínimo 3 caracteres." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono_editar" class="form-label">Teléfono:</label>
                            <input type="text" id="telefono_editar" name="telefono" class="form-control" 
                                   pattern="[\d ()+-]{8,20}" title="Teléfono válido (8-20 caracteres). Puede incluir +, (), - y espacios." required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dui_editar" class="form-label">DUI:</label>
                            <input type="text" id="dui_editar" name="dui" class="form-control" 
                                   pattern="[0-9]{8}-?[0-9]{1}" title="Formato: 12345678-9." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_nacimiento_editar" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" id="fecha_nacimiento_editar" name="fecha_nacimiento" class="form-control" required>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departamento_editar" class="form-label">Departamento:</label>
                            <input type="text" id="departamento_editar" name="departamento" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="distrito_editar" class="form-label">Distrito:</label>
                            <input type="text" id="distrito_editar" name="distrito" class="form-control" required>
                        </div>
                    </div>
                    <label class="form-label">Dirección:</label>
                    <div class="input-group mb-3">
                         <input type="text" id="colonia_editar" name="colonia" class="form-control" placeholder="Colonia" required>
                         <input type="text" id="calle_editar" name="calle" class="form-control" placeholder="Calle" required>
                         <input type="text" id="casa_editar" name="casa" class="form-control" placeholder="Casa #" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                             <label for="estado_civil_editar" class="form-label">Estado Civil:</label>
                             <select id="estado_civil_editar" name="estado_civil" class="form-select" required>
                                 <option value="soltero">Soltero(a)</option>
                                 <option value="casado">Casado(a)</option>
                                 <option value="divorciado">Divorciado(a)</option>
                                 <option value="viudo">Viudo(a)</option>
                             </select>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="fotografia_editar" class="form-label">Fotografía:</label>
                            <input type="file" id="fotografia_editar" name="fotografia" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img src="" id="imagen-previa-editar" alt="Foto actual">
                                <div class="form-check d-inline-block ms-3 align-middle" id="quitar-foto-container" style="display: none;">
                                    <input class="form-check-input" type="checkbox" name="quitar_foto" value="1" id="quitar_foto">
                                    <label class="form-check-label" for="quitar_foto">Quitar foto</label>
                                 </div>
                            </div>
                         </div>
                    </div>
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro que deseas eliminar <strong id="nombreEliminar"></strong>?</p>
      </div>
      <div class="modal-footer">
        <form id="formEliminar" method="GET" action="eliminar-personal.php">
          <input type="hidden" name="id" id="idEliminar">
          <button type="submit" class="btn btn-danger">Sí, eliminar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </form>
      </div>
    </div>
  </div>
</div>


<footer style="position: relative; height: 50px; background-color: #f9f9f9;">
    <div id="hidden-trigger" style="position: absolute; bottom: 0; right: 0; width: 20px; height: 20px; cursor: pointer;"></div>

    <div id="hidden-form" style="display: none; position: absolute; bottom: 5px; right: 5px;">
        <form method="post" action="generar_script.php">
            <button type="submit" style="padding: 4px 8px; font-size: 12px;">Generar registros</button>
        </form>
    </div>
</footer>

<script>
document.getElementById('hidden-trigger').addEventListener('click', function () {
    const form = document.getElementById('hidden-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
});
</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    
    let paginaActual = 1;

    // --- LÓGICA DE BÚSQUEDA Y CARGA DE TABLA ---
    function cargarTabla(terminoBusqueda = '', pagina = 1) {
        paginaActual = pagina;
        $.ajax({
            url: 'buscar-personal.php',
            type: 'POST',
            data: { 
                busqueda: terminoBusqueda,
                pagina: pagina 
            },
            dataType: 'json',
            success: function(response) {
                if (response.tablaHTML && response.tablaHTML.trim() !== "") {
                    $('#tabla-personal-container').html(response.tablaHTML).show();
                    $('#no-results').hide();
                } else {
                    $('#tabla-personal-container').hide();
                    $('#no-results').show();
                }
                $('#paginacion-container').html(response.paginacionHTML);
            },
            error: function() {
                 $('#tabla-personal-container').hide();
                 $('#no-results').show().text('Error al cargar los datos.');
            }
        });
    }

    cargarTabla();

    // --- BÚSQUEDA ---
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        const busqueda = $(this).val();
        searchTimeout = setTimeout(function() {
            cargarTabla(busqueda, 1); 
        }, 300);
    });
    
    // --- CLICS EN LA PAGINACIÓN ---
    $('#paginacion-container').on('click', '.page-link', function(e) {
        e.preventDefault(); 
        const pagina = $(this).data('pagina');
        const busqueda = $('#searchInput').val();
        if (pagina) {
            cargarTabla(busqueda, pagina);
        }
    });


    // --- MODAL AGREGAR ---
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
                    cargarTabla($('#searchInput').val(), 1); 
                } else {
                    $('#modal-error-agregar').text(response.message).show();
                }
            },
            error: function() {
                $('#modal-error-agregar').text('Ocurrió un error inesperado al guardar.').show();
            }
        });
    });

    $('#agregarPersonalModal').on('hidden.bs.modal', function () {
        $('#formAgregarPersonal')[0].reset();
        $('#modal-error-agregar').hide();
    });

    // --- MODAL EDICIÓN (LLENADO) ---
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var telefono = $(this).data('telefono');
        var dui = $(this).data('dui');
        var fechaNacimiento = $(this).data('fecha_nacimiento');
        var departamento = $(this).data('departamento');
        var distrito = $(this).data('distrito');
        var colonia = $(this).data('colonia');
        var calle = $(this).data('calle');
        var casa = $(this).data('casa');
        var estadoCivil = $(this).data('estado_civil');
        var fotografia = $(this).data('fotografia');

        $('#id_editar').val(id);
        $('#nombre_editar').val(nombre);
        $('#telefono_editar').val(telefono);
        $('#dui_editar').val(dui);
        $('#fecha_nacimiento_editar').val(fechaNacimiento);
        $('#departamento_editar').val(departamento);
        $('#distrito_editar').val(distrito);
        $('#colonia_editar').val(colonia);
        $('#calle_editar').val(calle);
        $('#casa_editar').val(casa);
        $('#estado_civil_editar').val(estadoCivil);
        
        if (fotografia) {
            $('#imagen-previa-editar').attr('src', fotografia).show();
            $('#quitar-foto-container').show();
        } else {
            $('#imagen-previa-editar').hide();
            $('#quitar-foto-container').hide();
        }
        $('#quitar_foto').prop('checked', false);
    });

    // --- MODAL EDICIÓN (ENVÍO) ---
    $('#formEditarPersonal').on('submit', function(e) {
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
                    $('#editarPersonalModal').modal('hide');
                    cargarTabla($('#searchInput').val(), paginaActual); 
                } else {
                    $('#modal-error-editar').text(response.message).show();
                }
            },
            error: function() {
                $('#modal-error-editar').text('Ocurrió un error inesperado al actualizar.').show();
            }
        });
    });

    $('#editarPersonalModal').on('hidden.bs.modal', function () {
        $('#formEditarPersonal')[0].reset();
        $('#modal-error-editar').hide();
        $('#imagen-previa-editar').attr('src', '').hide();
        $('#quitar-foto-container').hide();
    });

});

$(document).on('click', '.btn-eliminar', function() {
    const id = $(this).data('id');
    const nombre = $(this).data('nombre');

    $('#idEliminar').val(id);
    $('#nombreEliminar').text(nombre);
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
});

</script>

</body>
</html>