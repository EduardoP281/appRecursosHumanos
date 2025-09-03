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
        while($row = mysqli_fetch_assoc($resultado)){
            echo '<tr>';
            echo '<td>'.$row['id'].'</td>';
            // Mostrar avatar
            $avatar = (!empty($row['fotografia']) && file_exists($row['fotografia'])) ? $row['fotografia'] : $avatar_default;
            if(empty($row['fotografia'])) $avatar = $avatar_default;
            echo '<td><img src="'.$avatar.'" alt="avatar" style="width:50px;height:50px;border-radius:50%;object-fit:cover;"></td>';
            echo '<td>'.$row['nombre'].'</td>';
            echo '<td>'.$row['telefono'].'</td>';
            echo '<td>'.$row['dui'].'</td>';
            echo '<td>'.$row['fecha_nacimiento'].'</td>';
            echo '<td>'.$row['departamento'].'</td>';
            echo '<td>'.$row['distrito'].'</td>';
            echo '<td>'.$row['colonia'].'</td>';
            echo '<td>'.$row['calle'].'</td>';
            echo '<td>'.$row['casa'].'</td>';
            echo '<td>'.$row['estado_civil'].'</td>';
            echo '<td>'.$row['fecha_registro'].'</td>';
            echo '<td>';
            echo '<a href="editar-personal.php?id='.$row['id'].'" class="btn btn-primary btn-sm me-1">Modificar</a>';
            echo '<a href="eliminar-personal.php?id='.$row['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Seguro que desea eliminar este registro?\')">Eliminar</a>';
            echo '</td>';
            echo '</tr>';
        }
        mysqli_close($con);
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
