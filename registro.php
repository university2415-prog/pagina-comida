<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    $data = $_POST;
}

$nombre = trim($data['nombre'] ?? '');
$primer_apellido = trim($data['primer_apellido'] ?? '');
$segundo_apellido = trim($data['segundo_apellido'] ?? '');
$correo = trim($data['correo'] ?? '');
$password = trim($data['password'] ?? '');
$pais = trim($data['pais'] ?? '');

if (empty($nombre) || empty($primer_apellido) || empty($correo) || empty($password) || empty($pais)) {
    echo json_encode([
        'success' => false,
        'message' => 'Completa todos los campos requeridos para registrarte.'
    ]);
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Ingresa un correo válido.'
    ]);
    exit();
}

$stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
$stmt->bind_param('s', $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Este correo ya está registrado. Intenta iniciar sesión.'
    ]);
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, contraseña, pais, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param('ssssss', $nombre, $primer_apellido, $segundo_apellido, $correo, $hash, $pais);

if ($stmt->execute()) {
    $usuarioId = $stmt->insert_id;
    $stmt->close();

    $stmtContacto = $conn->prepare("INSERT INTO contactos (nombre, primer_apellido, segundo_apellido, correo, contraseña, pais) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmtContacto) {
        $stmtContacto->bind_param('ssssss', $nombre, $primer_apellido, $segundo_apellido, $correo, $hash, $pais);
        if (!$stmtContacto->execute()) {
            $stmtContacto->close();
            $conn->query("DELETE FROM usuarios WHERE id = $usuarioId");
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar el registro en la tabla contactos.'
            ]);
            exit();
        }
        $stmtContacto->close();
    } else {
        $conn->query("DELETE FROM usuarios WHERE id = $usuarioId");
        echo json_encode([
            'success' => false,
            'message' => 'Error preparando la tabla contactos.'
        ]);
        exit();
    }

    $_SESSION['usuario_id'] = $usuarioId;
    $_SESSION['usuario_nombre'] = $nombre;
    $_SESSION['usuario_correo'] = $correo;

    echo json_encode([
        'success' => true,
        'message' => 'Registro exitoso. Ya puedes iniciar sesión.'
    ]);
    exit();
}

echo json_encode([
    'success' => false,
    'message' => 'Error al crear la cuenta: ' . $conn->error
]);
