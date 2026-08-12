<#
Script: push_all.ps1
Descripción: Añade, commitea y empuja todos los cambios al remote 'paginaproyecto'.
Uso: Ejecuta desde la raíz del proyecto en PowerShell.
#>
param(
    [string]$CommitMessage = "Actualizar proyecto: cambios locales",
    [string]$RemoteName = "paginaproyecto"
)

function Abort([string]$msg){
    Write-Host "ERROR: $msg" -ForegroundColor Red
    exit 1
}

if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Abort "git no está instalado o no está en PATH."
}

Push-Location -Path (Split-Path -Path $MyInvocation.MyCommand.Definition -Parent)

# Detectar remote
$remotes = git remote
if (-not ($remotes -match $RemoteName)) {
    Write-Host "Remote '$RemoteName' no existe." -ForegroundColor Yellow
    $url = Read-Host "Introduce la URL del remote (o deja vacío para cancelar)"
    if ([string]::IsNullOrEmpty($url)) { Abort "Operación cancelada por el usuario." }
    git remote add $RemoteName $url
}

# Asegurar que estamos en la rama correcta
$branch = git branch --show-current
Write-Host "Rama actual: $branch"

# Añadir todos los cambios
Write-Host "Añadiendo todos los cambios..."
git add -A

# Commitear
Write-Host "Creando commit..."
$commitOut = git commit -m "$CommitMessage" 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host $commitOut -ForegroundColor Yellow
    Write-Host "Quizá no haya cambios para commitear. Continuando..." -ForegroundColor Yellow
} else {
    Write-Host "Commit creado." -ForegroundColor Green
}

# Push
Write-Host "Haciendo push a $RemoteName HEAD..."
$pushOut = git push $RemoteName HEAD 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error al hacer push:" -ForegroundColor Red
    Write-Host $pushOut
    Abort "No se pudo hacer push. Revisa credenciales y la URL del remote."
}

Write-Host "Push completado correctamente." -ForegroundColor Green
Pop-Location
