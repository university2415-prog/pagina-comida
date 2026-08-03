<?php
require_once 'conexion.php';

$res = $conn->query("SELECT * FROM productos ORDER BY id ASC");

if ($res && $res->num_rows === 0) {
    // Si la tabla productos está vacía, la poblamos con datos iniciales
    $productos_iniciales = [
        ["Chuchitos", "Deliciosos chuchitos preparados con masa de maíz y recado tradicional guatemalteco.", "Guatemala", 12.99, 4.9, 50],
        ["Pizza napolitana", "Pizza artesanal horneada en horno de leña con masa madre fermentada 24 horas y queso mozzarella.", "Italia", 15.99, 4.8, 40],
        ["Sushi Premium", "Rolls de arroz para sushi y pescado fresco de primera calidad importado de Japón.", "Japón", 16.40, 4.7, 30],
        ["Tacos al Pastor", "Tacos tradicionales con carne de cerdo marinada en achiote y especias, piña y cilantro fresco.", "México", 11.50, 4.9, 60],
        ["Goulash Tradicional", "Plato reconfortante con carne tierna, papas y una deliciosa salsa de pimentón y especias.", "Hungría", 14.50, 4.6, 25]
    ];

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, origen, precio, rating, stock) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($productos_iniciales as $p) {
        $stmt->bind_param("sssddi", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
        $stmt->execute();
    }

    $res = $conn->query("SELECT * FROM productos ORDER BY id ASC");
}

$productos = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $productos[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "productos" => $productos
]);
