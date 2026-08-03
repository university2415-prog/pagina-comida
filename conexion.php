
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
?>