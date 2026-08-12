Despliegue del proyecto "pagina proyecto"

Resumen rápido

- Frontend: archivos estáticos (HTML/CSS/JS) pueden alojarse en Netlify.
- Backend: este proyecto usa PHP + MySQL. Netlify no ejecuta PHP ni puede alojar MySQL local; necesitas desplegar el backend en un host que soporte PHP (o migrarlo a una API serverless) y configurar las variables de entorno DB_*.

Opciones recomendadas

1) Desplegar backend PHP + MySQL en un host (recomendado si quieres mínimo cambio)
   - Proveedores: Render.com (servicio web con PHP), DigitalOcean App Platform, Hostinger, o un VPS.
   - Pasos generales:
     1. Crear un servicio/servidor que soporte PHP y MySQL.
     2. Importar la base de datos MySQL (dump) o crearla manualmente.
     3. Configurar variables de entorno: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.
     4. Subir los archivos PHP al servidor (git clone o SFTP).
     5. Ajustar permisos en la carpeta `img/uploads` para permitir uploads.

2) Mantener frontend en Netlify y usar una API remota
   - Mantén los archivos estáticos en Netlify.
   - Implementa el backend como una API en Render / Heroku / Vercel (serverless) o como funciones gestionadas que expongan endpoints.
   - Configura en Netlify las llamadas del frontend para apuntar a la URL del backend público.

Comandos útiles locales

- Ejecutar comprobaciones de sintaxis PHP:
  php -l conexion.php

- Probar endpoint local (con XAMPP corriendo):
  curl -i "http://localhost/pagina%20proyecto/obtener_productos.php"

- Hacer commit y push manualmente:
  git add conexion.php check_and_deploy.ps1 README_DEPLOY.md
  git commit -m "Usar variables de entorno en conexion.php y añadir scripts de despliegue"
  git push paginaproyecto HEAD

Notas finales

- Antes de desplegar en producción, no dejes credenciales en el código; usa siempre variables de entorno.
- Si quieres, puedo preparar un `dump.sql` para importar la estructura y datos iniciales de `productos`/`usuarios`/`pedidos`.
