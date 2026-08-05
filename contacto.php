<?php
$status = $_GET['status'] ?? '';
$message = '';
$messageClass = '';

if ($status === 'success') {
    $message = 'Mensaje enviado correctamente. Gracias por contactarnos.';
    $messageClass = 'success-message';
} elseif ($status === 'error') {
    $message = 'Ocurrió un error al enviar tu mensaje. Intenta de nuevo.';
    $messageClass = 'error-message';
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contacto - Comidas Típicas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <main class="page">
      <nav class="top-nav" style="margin-bottom: 24px;">
        <a href="index.html">Inicio</a>
        <a href="menu.html">Menú</a>
        <a href="contacto.php" class="active">Contacto</a>
      </nav>

      <section class="page-intro">
        <h2>Contáctanos</h2>
        <p>Comparte tus comentarios, reserva una experiencia o solicita información sobre nuestros platos.</p>
      </section>

      <section class="contact-card">
        <article class="card">
          <h3>Escríbenos</h3>
          <p>Estamos listos para responder tus preguntas y ayudarte a descubrir nuevas ideas.</p>
          <p><strong>Email:</strong> contacto@comidastipicas.com</p>
          <p><strong>Teléfono:</strong> +51 999 888 777</p>
        </article>

        <article class="card">
          <form method="POST" action="guardar_contacto.php">
            <?php if ($message): ?>
              <div class="form-alert <?= htmlspecialchars($messageClass, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
              </div>
            <?php endif; ?>

            <label for="name">Nombre</label>
            <input id="name" name="nombre" type="text" required placeholder="Tu nombre" />

            <label for="email">Correo</label>
            <input id="email" name="correo" type="email" required placeholder="tu@email.com" />

            <label for="message">Mensaje</label>
            <textarea id="message" name="mensaje" rows="4" required placeholder="Cuéntanos qué te gustaría probar..."></textarea>

            <button type="submit">Enviar</button>
          </form>
        </article>
      </section>
    </main>
  </body>
</html>
