# Script de verificación y despliegue para el proyecto "pagina proyecto"
# Ejecutar en PowerShell desde la raíz del proyecto (C:\xampp\htdocs\pagina proyecto)

param(
    [string]$CommitMessage = "Actualizar conexion y agregar script de verificación",
    [string]$RemoteName = "paginaproyecto"
)

function Abort([string]$msg){
    Write-Host "ERROR: $msg" -ForegroundColor Red
    exit 1
}

# Comprobar que php esté disponible
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Abort "PHP no está instalado o no está en PATH. Instala PHP o ejecuta este script desde XAMPP shell." 
}

$files = @(
    'conexion.php', 'login.php', 'obtener_productos.php', 'guardar_pedido.php', 'guardar_contacto.php', 'pedidos.php', 'registro.php', 'admin.php'
)

Write-Host "Verificando sintaxis PHP en archivos clave..."
foreach ($f in $files) {
    if (-not (Test-Path $f)) {
        Write-Host "Aviso: archivo no encontrado $f" -ForegroundColor Yellow
        continue
    }
    $out = php -l $f 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Fallo de sintaxis en $f:" -ForegroundColor Red
        Write-Host $out
        Abort "Corrige los errores de sintaxis antes de continuar."
    } else {
        Write-Host "$f OK" -ForegroundColor Green
    }
}

# Probar endpoint de ejemplo
$testUrl = 'http://localhost/pagina%20proyecto/obtener_productos.php'
Write-Host "Probando endpoint: $testUrl"
try {
    $resp = Invoke-WebRequest -Uri $testUrl -UseBasicParsing -TimeoutSec 10
    if ($resp.StatusCode -ne 200) {
        Write-Host "Respuesta inesperada: $($resp.StatusCode)" -ForegroundColor Yellow
    } else {
        Write-Host "Endpoint respondió 200 OK" -ForegroundColor Green
        # Mostrar primeros 300 caracteres del body
        $bodyPreview = $resp.Content
        if ($bodyPreview.Length -gt 300) { $bodyPreview = $bodyPreview.Substring(0,300) + '...'}
        Write-Host $bodyPreview
    }
} catch {
    Write-Host "No se pudo conectar al endpoint local. Asegúrate de que Apache y MySQL estén corriendo en XAMPP." -ForegroundColor Red
    Write-Host $_.Exception.Message
    Abort "Fallo en la prueba de endpoint." 
}

# Preparar commit
Write-Host "Preparando commit..."
git add conexion.php check_and_deploy.ps1 README_DEPLOY.md 2>$null
$commit = git commit -m "$CommitMessage" 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "git commit: $commit" -ForegroundColor Yellow
    Write-Host "Puede que no haya cambios para commitear o hay un conflicto. Continuando..."
} else {
    Write-Host "Commit creado." -ForegroundColor Green
}

# Push al remote
Write-Host "Empujando a remote '$RemoteName' (rama HEAD)..."
$push = git push $RemoteName HEAD 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error al hacer push: $push" -ForegroundColor Red
    Abort "No se pudo hacer push al remote. Verifica configuración del remote y tus credenciales." 
}
Write-Host "Push completado." -ForegroundColor Green
Write-Host "Script finalizado con éxito." -ForegroundColor Cyan
