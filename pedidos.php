<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Invitado';

// Consultar pedidos del usuario
$sql = "SELECT p.id, p.fecha, p.subtotal, p.impuestos, p.total, u.nombre as cliente 
        FROM pedidos p 
        LEFT JOIN usuarios u ON p.usuario_id = u.id ";
if ($usuario_id) {
    $sql .= " WHERE p.usuario_id = " . intval($usuario_id);
}
$sql .= " ORDER BY p.fecha DESC";

$res = $conn->query($sql);
$pedidos = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $pedido_id = $row['id'];
        // Obtener detalles del pedido
        $sql_det = "SELECT pd.*, pr.nombre as producto_nombre, pr.origen 
                    FROM pedido_detalle pd 
                    LEFT JOIN productos pr ON pd.producto_id = pr.id 
                    WHERE pd.pedido_id = " . intval($pedido_id);
        $res_det = $conn->query($sql_det);
        $detalles = [];
        if ($res_det) {
            while ($det = $res_det->fetch_assoc()) {
                $detalles[] = $det;
            }
        }
        $row['detalles'] = $detalles;
        $pedidos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mis Pedidos - Comidas Típicas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
  <style>
    .orders-container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .order-card {
      background: #ffffff;
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--border);
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .order-title {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--accent-dark);
    }
    .order-date {
      color: var(--muted);
      font-size: 0.9rem;
    }
    .order-details-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }
    .order-details-table th, .order-details-table td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px dashed var(--border);
    }
    .order-details-table th {
      color: var(--muted);
      font-weight: 600;
      font-size: 0.85rem;
      text-transform: uppercase;
    }
    .order-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 700;
      font-size: 1.1rem;
      color: var(--text);
    }
    .order-badge {
      background: #22c55e;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
    }
  </style>
</head>
<body class="catalog-page">
  <header class="navbar">
    <div class="navbar-content">
      <div class="navbar-brand">
        <div class="brand-logo">
          <svg width="32" height="32" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="30" cy="22" r="8" fill="white"/>
            <path d="M 18 28 L 22 35 L 38 35 L 42 28 Z" fill="white"/>
            <rect x="16" y="35" width="28" height="3" fill="white"/>
          </svg>
        </div>
        <div>
          <h2>Comidas Típicas</h2>
          <p>SISTEMA DE PEDIDOS</p>
        </div>
      </div>

      <nav class="top-nav" style="margin: 0;">
        <a href="menu.html">Menú</a>
        <a href="pedidos.php" class="active">Mis Pedidos</a>
        <a href="contacto.php">Contacto</a>
        <a href="admin.php">Administración</a>
      </nav>

      <div class="navbar-actions">
        <a href="carrito.html" class="cart-btn">🛒</a>
        <a href="logout.php" class="logout-btn" style="text-decoration:none; display:inline-block; text-align:center;">Salir</a>
      </div>
    </div>
  </header>

  <main class="orders-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <h1>Historial de Pedidos (<?php echo htmlspecialchars($usuario_nombre); ?>)</h1>
      <a href="menu.html" class="btn-explore" style="margin-top:0;">Volver al Menú</a>
    </div>

    <?php if (empty($pedidos)): ?>
      <div class="empty-cart">
        <div class="empty-cart-content">
          <div class="empty-cart-icon">📦</div>
          <h2>No tienes pedidos registrados</h2>
          <p>Aún no has realizado ninguna orden en nuestra plataforma.</p>
          <a href="menu.html" class="btn-explore">Explorar el Menú</a>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($pedidos as $p): ?>
        <div class="order-card">
          <div class="order-header">
            <div>
              <span class="order-title">Pedido #<?php echo $p['id']; ?></span>
              <span class="order-badge">Completado</span>
            </div>
            <span class="order-date">📅 <?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></span>
          </div>

          <table class="order-details-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Origen</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($p['detalles'] as $d): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($d['producto_nombre'] ?? 'Producto'); ?></strong></td>
                  <td><?php echo htmlspecialchars($d['origen'] ?? '-'); ?></td>
                  <td><?php echo $d['cantidad']; ?></td>
                  <td>$<?php echo number_format($d['precio_unitario'], 2); ?></td>
                  <td>$<?php echo number_format($d['cantidad'] * $d['precio_unitario'], 2); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="order-total-row">
            <span>Cliente: <?php echo htmlspecialchars($p['cliente'] ?? 'Registrado'); ?></span>
            <div>
              <span style="color: var(--muted); font-size: 0.95rem; margin-right: 12px;">Impuestos: $<?php echo number_format($p['impuestos'], 2); ?></span>
              <span style="color: #22c55e;">Total: $<?php echo number_format($p['total'], 2); ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>
</body>
</html>
