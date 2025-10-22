<?php
include_once __DIR__ . '/../conf/conf.php';

$zonas_oriente = [
    'San Miguel' => ['San Miguel Centro', 'Ciudad Barrios', 'El Tránsito'],
    'La Unión' => ['Santa Rosa de Lima', 'La Unión Centro', 'Anamorós'],
    'Morazán' => ['San Francisco Gotera', 'Jocoro', 'Chilanga'],
    'Usulután' => ['Usulután Centro', 'Jiquilisco', 'Berlín']
];

function generarNombre() {
    $nombres = ['Carlos', 'Ana', 'Luis', 'María', 'José', 'Lucía', 'Pedro', 'Sofía', 'Juan', 'Valeria'];
    $apellidos = ['Gómez', 'Martínez', 'López', 'Hernández', 'Ramírez', 'Torres', 'Flores', 'Cruz', 'Reyes', 'Morales'];
    return $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)];
}

function generarTelefono() {
    return '7' . rand(1000000, 9999999);
}

function generarDUI() {
    return rand(10000000, 99999999) . '-' . rand(0, 9);
}

function generarFechaNacimiento() {
    $timestamp = rand(strtotime('1970-01-01'), strtotime('2005-12-31'));
    return date('Y-m-d', $timestamp);
}

function generarDireccion($base) {
    return $base . ' ' . rand(1, 50);
}

function generarEstadoCivil() {
    $estados = ['Soltero', 'Casado', 'Divorciado', 'Viudo'];
    return $estados[array_rand($estados)];
}

function generarFotografia($id) {
    return "foto_$id.jpg";
}

function generarFechaRegistro() {
    $timestamp = rand(strtotime('2022-01-01'), strtotime('2025-10-01'));
    return date('Y-m-d', $timestamp);
}

for ($i = 1; $i <= 100; $i++) {
    // Seleccionar departamento y distrito correspondiente
    $departamento = array_rand($zonas_oriente);
    $distrito = $zonas_oriente[$departamento][array_rand($zonas_oriente[$departamento])];

    $nombre = generarNombre();
    $telefono = generarTelefono();
    $dui = generarDUI();
    $fecha_nacimiento = generarFechaNacimiento();
    $colonia = generarDireccion('Colonia');
    $calle = generarDireccion('Calle');
    $casa = 'Casa ' . rand(1, 100);
    $estado_civil = generarEstadoCivil();
    $fotografia = generarFotografia($i);
    $fecha_registro = generarFechaRegistro();

    $sql = "INSERT INTO personal (nombre, telefono, dui, fecha_nacimiento, departamento, distrito, colonia, calle, casa, estado_civil, fotografia, fecha_registro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('ssssssssssss', $nombre, $telefono, $dui, $fecha_nacimiento, $departamento, $distrito, $colonia, $calle, $casa, $estado_civil, $fotografia, $fecha_registro);
    $stmt->execute();
    $stmt->close();
}

header("Location: personal.php");
exit;

?>