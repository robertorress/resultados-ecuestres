# Resultados ecuestres

Aplicación PHP para publicar concursos, órdenes de salida y resultados ecuestres almacenados en XML y JSON. La interfaz utiliza MaterializeCSS.

## Requisitos

- PHP 8.2 o superior.
- Apache con `mod_rewrite` y `mod_headers` recomendados.
- Extensiones PHP: `SimpleXML`, `JSON`, `mbstring` y `BCMath`.

## Configuración

1. Servir la carpeta del proyecto desde Apache.
2. Revisar `configuracion.php`.
3. Mantener `RESULTADOS_DISPLAY_ERRORS=0` en producción.
4. Usar `RESULTADOS_DISPLAY_ERRORS=1` únicamente durante desarrollo.
5. Mantener `config.xml` como catálogo de concursos.

Las rutas se pueden separar mediante variables de entorno:

- `RESULTADOS_APP_ROOT`: carpeta del código PHP y vistas.
- `RESULTADOS_DATA_ROOT`: carpeta que contiene `recursos`.
- `RESULTADOS_CATALOG_PATH`: ruta absoluta o relativa a `config.xml`.
- `RESULTADOS_LOG_ROOT`: carpeta donde se escriben los logs propios.

Si no se definen, se mantiene la estructura actual y el proyecto usa su propia carpeta como raíz de código, datos y catálogo.

Las plantillas `config\development.env.example` y `config\production.env.example` muestran la configuración recomendada para cada entorno. En producción las variables deben configurarse en Apache, PHP o el sistema operativo, nunca en archivos públicos del proyecto.

Los errores de aplicación se escriben en `logs\application-AAAA-MM-DD.log` y los errores de nivel `error` también en `logs\errors-AAAA-MM-DD.log`. La carpeta está protegida mediante `.htaccess` y no se sirve directamente al navegador.

## Validación antes de publicar

Desde la raíz del proyecto:

```text
C:\xampp\php\php.exe tools\validate-data.php
C:\xampp\php\php.exe tools\smoke-test.php
```

El primer comando valida XML, enlaces y JSON. El segundo comprueba extensiones y el flujo principal de lectura.

En Windows también se puede ejecutar la rutina completa con:

```text
tools\run-checks.bat
```

El script comprueba la sintaxis de las vistas, ejecuta la prueba de humo y valida todos los datos antes de publicar.

Para una comprobación operativa completa:

```text
tools\maintenance-check.bat
```

Esta rutina comprueba archivos esenciales y ejecuta todas las validaciones existentes.

El estado técnico básico se puede consultar con:

```text
C:\xampp\php\php.exe tools\health-check.php
```

El comando devuelve JSON y un código de salida distinto de cero si falla el catálogo, la carpeta de datos o alguna extensión PHP.

## Publicación y recuperación

Crear una copia comprimida antes de publicar:

```powershell
powershell -ExecutionPolicy Bypass -File tools\backup-project.ps1
```

La copia no incluye `config.xml`, que debe conservarse manualmente. Para restaurar una copia:

```powershell
powershell -ExecutionPolicy Bypass -File tools\restore-project.ps1 -Archive C:\PROYECTOS\backups\resultados-ecuestres-AAAAMMDD-HHMMSS.zip -Force
```

## Estructura de datos

- `config.xml`: catálogo general de concursos.
- `recursos\AAAA\N\concurso.xml`: metadatos de un concurso.
- `recursos\AAAA\N\resultados\*.json`: resultados de cada prueba.
- `recursos\AAAA\N\orden_salida\`: imágenes de órdenes de salida.

## Errores y logs

Los errores se registran mediante la configuración de logs de PHP/Apache. En producción no se muestran detalles técnicos al usuario.
