
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistema_comida";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    $message = "Error de conexión a la base de datos: " . $conn->connect_error;
    if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode([
            "success" => false,
            "message" => $message
        ]);
        exit();
    }
    die($message);
}

$conn->set_charset("utf8mb4");

$createContactosTable = "
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    primer_apellido VARCHAR(255) DEFAULT NULL,
    segundo_apellido VARCHAR(255) DEFAULT NULL,
    correo VARCHAR(255) NOT NULL,
    contraseña VARCHAR(255) DEFAULT NULL,
    pais VARCHAR(100) DEFAULT NULL,
    mensaje TEXT DEFAULT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (!$conn->query($createContactosTable)) {
    error_log("Error creando tabla contactos: " . $conn->error);
}

?>
