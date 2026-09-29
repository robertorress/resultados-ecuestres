param(
    [string]$Destination = (Join-Path (Split-Path -Parent $PSScriptRoot) '..\backups')
)

$ErrorActionPreference = 'Stop'
$project = Split-Path -Parent $PSScriptRoot
$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$archive = Join-Path $Destination "resultados-ecuestres-$timestamp.zip"

New-Item -ItemType Directory -Path $Destination -Force | Out-Null

$items = Get-ChildItem -LiteralPath $project -Force |
    Where-Object { $_.Name -notin @('config.xml', 'backups') }

Compress-Archive -Path $items.FullName -DestinationPath $archive -CompressionLevel Optimal
Write-Output "Copia creada: $archive"
