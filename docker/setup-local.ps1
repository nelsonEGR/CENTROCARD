[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'

$identity = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($identity)
if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw 'Ejecuta PowerShell como Administrador para registrar centrocar.dev en hosts.'
}

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    throw 'Docker Desktop no esta instalado o no esta disponible en PATH.'
}

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location -LiteralPath $projectRoot

& docker compose version
if ($LASTEXITCODE -ne 0) {
    throw 'Docker Compose v2 no esta disponible.'
}

$domain = 'centrocar.dev'
$hostsFile = Join-Path $env:SystemRoot 'System32\drivers\etc\hosts'
$domainPattern = '(^|\s)' + [regex]::Escape($domain) + '(\s|$)'
$entries = @(Get-Content -LiteralPath $hostsFile | Where-Object {
    $_ -notmatch '^\s*#' -and $_ -match $domainPattern
})

if ($entries.Count -eq 0) {
    Add-Content -LiteralPath $hostsFile -Value "`r`n127.0.0.1 $domain"
} elseif (@($entries | Where-Object { $_ -notmatch '^\s*127\.0\.0\.1(\s|$)' }).Count -gt 0) {
    throw "$domain ya esta asociado a otra direccion en $hostsFile. Resuelve ese conflicto antes de continuar."
}

& docker compose up --build --detach
if ($LASTEXITCODE -ne 0) {
    throw 'No fue posible iniciar los contenedores. Revisa que los puertos 80 y 443 esten libres.'
}

$certificatePath = Join-Path ([System.IO.Path]::GetTempPath()) 'centrocar.dev-root-ca.crt'
Remove-Item -LiteralPath $certificatePath -Force -ErrorAction SilentlyContinue

try {
    $certificateCopied = $false
    for ($attempt = 1; $attempt -le 30; $attempt++) {
        & docker compose cp 'proxy:/data/caddy/pki/authorities/local/root.crt' $certificatePath
        if ($LASTEXITCODE -eq 0 -and (Test-Path -LiteralPath $certificatePath)) {
            $certificateCopied = $true
            break
        }

        Start-Sleep -Seconds 2
    }

    if (-not $certificateCopied) {
        throw 'No fue posible obtener el certificado local de Caddy.'
    }

    & certutil.exe -user -addstore -f Root $certificatePath | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw 'No fue posible confiar el certificado local en Windows.'
    }

    $ready = $false
    for ($attempt = 1; $attempt -le 30; $attempt++) {
        try {
            $response = Invoke-WebRequest -Uri "https://$domain/" -UseBasicParsing -TimeoutSec 5
            if ($response.StatusCode -eq 200) {
                $ready = $true
                break
            }
        } catch {
            Start-Sleep -Seconds 2
        }
    }

    if (-not $ready) {
        throw 'Los contenedores iniciaron, pero la aplicacion no respondio correctamente. Ejecuta docker compose logs.'
    }
} finally {
    Remove-Item -LiteralPath $certificatePath -Force -ErrorAction SilentlyContinue
}

Write-Host ''
Write-Host "Centrocard listo en https://$domain"
Write-Host 'Acceso local inicial: admin / password'
Write-Host 'Cambia la contrasena despues de iniciar sesion.'
