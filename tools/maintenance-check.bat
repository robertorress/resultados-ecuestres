@echo off
setlocal

set "PROJECT=%~dp0.."

echo === Comprobacion de archivos esenciales ===
for %%F in (
    "%PROJECT%\config.xml"
    "%PROJECT%\configuracion.php"
    "%PROJECT%\.htaccess"
    "%PROJECT%\index.php"
) do (
    if not exist "%%~F" (
        echo ERROR: Falta %%~F
        exit /b 1
    )
)

echo === Validacion de aplicacion y datos ===
call "%PROJECT%\tools\run-checks.bat"
if errorlevel 1 exit /b 1

echo === Comprobacion de salud ===
"C:\xampp\php\php.exe" "%PROJECT%\tools\health-check.php"
if errorlevel 1 exit /b 1

echo === Mantenimiento completado ===
echo Recuerde revisar los logs de PHP y Apache y crear una copia antes de publicar.
exit /b 0
