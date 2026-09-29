param(
    [Parameter(Mandatory = $true)]
    [string]$Archive,
    [string]$Target = (Split-Path -Parent $PSScriptRoot),
    [switch]$Force
)

$ErrorActionPreference = 'Stop'
if (-not $Force) {
    throw 'La restauracion requiere especificar -Force para evitar sobreescrituras accidentales.'
}

$archivePath = $Archive
$targetPath = $Target
$temporaryPath = Join-Path $env:TEMP ("resultados-ecuestres-restore-" + $PID + "-" + (Get-Date -Format 'yyyyMMddHHmmss'))

if (-not (Test-Path -LiteralPath $archivePath -PathType Leaf)) {
    throw "No se encuentra la copia: $archivePath"
}

New-Item -ItemType Directory -Path $temporaryPath | Out-Null
try {
    Expand-Archive -LiteralPath $archivePath -DestinationPath $temporaryPath -Force
    Get-ChildItem -LiteralPath $temporaryPath -Force |
        Where-Object { $_.Name -ne 'config.xml' } |
        Copy-Item -Destination $targetPath -Recurse -Force
    Write-Output "Restauracion completada en: $targetPath"
}
finally {
    Remove-Item -LiteralPath $temporaryPath -Recurse -Force -ErrorAction SilentlyContinue
}
