<?php
header('Content-Type: application/json; charset=UTF-8');

// Incluye conexion.php (usa DB_* o valores por defecto)
require_once 'conexion.php';

$output = [
    'success' => false,
    'message' => '',
    'host' => $host ?? null,
    'db' => $db ?? null,
    'tables' => []
];

if (!isset($conn) || !$conn) {
    $output['message'] = 'No hay conexión disponible (variable $conn no definida).';
    echo json_encode($output);
    exit;
}

if ($conn->connect_error) {
    $output['message'] = 'Error en la conexión: ' . $conn->connect_error;
    echo json_encode($output);
    exit;
}

$res = $conn->query('SHOW TABLES');
if ($res) {
    while ($row = $res->fetch_array()) {
        $output['tables'][] = $row[0];
    }
    $output['success'] = true;
    $output['message'] = 'Conexión exitosa y tablas listadas.';
} else {
    $output['message'] = 'Error al listar tablas: ' . $conn->error;
}

echo json_encode($output);
