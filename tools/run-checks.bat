@echo off
setlocal

set "PROJECT=%~dp0.."
set "PHP=C:\xampp\php\php.exe"

if not exist "%PHP%" (
    echo ERROR: No se encuentra PHP en "%PHP%".
    exit /b 1
)

echo === Comprobacion de sintaxis PHP ===
for /r "%PROJECT%" %%F in (*.php) do (
    "%PHP%" -l "%%F" >nul
    if errorlevel 1 (
        echo ERROR: Sintaxis incorrecta en %%F
        exit /b 1
    )
)

echo === Prueba de humo ===
"%PHP%" "%PROJECT%\tools\smoke-test.php"
if errorlevel 1 exit /b 1

echo === Pruebas automatizadas ===
"%PHP%" "%PROJECT%\tools\automated-tests.php"
if errorlevel 1 exit /b 1

echo === Validacion de datos ===
"%PHP%" "%PROJECT%\tools\validate-data.php"
if errorlevel 1 exit /b 1

echo === Todas las comprobaciones han superado la validacion ===
exit /b 0
