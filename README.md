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

- `config.xml`: catálogo general de concursos, ubicado en la raíz del proyecto (`C:\...\resultados-ecuestres\config.xml`).
- `recursos\concurso.xml.template`: plantilla comentada para crear el `concurso.xml` de cada concurso.
- `recursos\AAAA\N\concurso.xml`: metadatos de un concurso.
- `recursos\AAAA\N\equipos.xml`: definición de equipos (cuando `concurso.xml` habilita resultados por equipos).
- `recursos\AAAA\N\resultados\*.json`: resultados de cada prueba.
- `recursos\AAAA\N\orden_salida\`: imágenes de órdenes de salida.
- `recursos\AAAA\N\plano\` (opcional): imágenes del plano de instalaciones referenciadas desde `concurso.xml`.

Para crear un nuevo concurso, copia `recursos\concurso.xml.template` como `recursos\AAAA\N\concurso.xml`, sustituye los valores de ejemplo y añade una entrada del concurso en `config.xml` dentro del año correspondiente. Las rutas de recursos deben ser relativas a la raíz del proyecto y usar `/` dentro de los archivos XML.

La ubicación del concurso admite el campo opcional `<visible>`:

```xml
<ubicacion>
  <nombre>Nombre del centro o lugar</nombre>
  <visible>si</visible>
</ubicacion>
```

La ubicación se muestra cuando el valor es `si`, `sí` o `yes`, sin distinguir mayúsculas (`SI`, `Sí`, `YES`, etc.). En el detalle del concurso aparece como un bloque desplegable con el icono de ubicación; al pulsarlo se muestra u oculta únicamente el mapa. Para ocultar toda la sección se puede utilizar `no` en cualquier combinación de mayúsculas. Si se omite `<visible>`, la ubicación se considera visible por compatibilidad con concursos existentes.

Para mostrar el menú **PLANO**, `concurso.xml` admite un bloque opcional `planos` con una o varias entradas `plano`:

```xml
<planos>
  <plano>recursos/AAAA/N/plano/plano_instalaciones_01.png</plano>
  <plano>recursos/AAAA/N/plano/plano_instalaciones_02.png</plano>
</planos>
```

Si no hay planos válidos o no se define el bloque, la sección **PLANO** muestra una tarjeta vacía con el texto **NO HAY PLANOS DISPONIBLES** y un botón de regreso al detalle del concurso.

El detalle del concurso también admite una sección opcional de anuncios. El bloque `<anuncios>` admite el campo `<visible>` con los mismos valores que la ubicación: `si`, `sí` o `yes` muestran la colección, y `no` la oculta, sin distinguir mayúsculas. Si se omite `<visible>`, los anuncios se muestran por compatibilidad. Cada `<anuncio>` puede contener `<titulo>`, `<texto>`, `<fecha>` y `<tipo>`, además de un `<link>` opcional con su propia etiqueta y URL:

```xml
<anuncios>
  <anuncio><texto>Información importante para los participantes</texto></anuncio>
  <anuncio>
    <titulo>Documentación disponible</titulo>
    <texto>Consulta la documentación</texto>
    <fecha>25/10/2026</fecha>
    <tipo>info</tipo>
    <link>
      <texto>ABRIR DOCUMENTACIÓN</texto>
      <url>https://ejemplo.test/documentacion</url>
    </link>
  </anuncio>
</anuncios>
```

La tarjeta de anuncios se muestra encima del mapa únicamente cuando existe el bloque `<anuncios>`. Actualmente no se muestra un título visible para la tarjeta; el texto **ANUNCIOS** queda conservado como comentario en la vista para poder recuperarlo en el futuro. Cada elemento puede mostrar un título destacado, texto descriptivo, fecha e indicador visual según el tipo (`none`, `info`, `importante`, `urgente` o `resultado`). El tipo `none` conserva el icono informativo, pero oculta la etiqueta azul `INFO`. El texto queda a la izquierda y el botón del enlace, cuando existe, a la derecha. Los campos nuevos son opcionales para mantener compatibilidad con anuncios antiguos.

## Navegación y mantenimiento

La navegación interna del concurso está centralizada en `view/partials/navigation-concurso.php`. Este partial renderiza tanto el menú de escritorio como el menú móvil (`sidenav`) y evita duplicar HTML entre vistas.

Las vistas de concurso únicamente deben declarar la sección activa antes de incluir el partial:

```php
<?php $activeSection = 'inicio'; require __DIR__ . '/partials/navigation-concurso.php'; ?>
```

Valores admitidos para `activeSection`:

- `inicio`
- `plano`
- `orden`
- `resultados`

Para añadir o modificar una opción de menú, se debe actualizar una sola vez la estructura de navegación en `view/partials/navigation-concurso.php`.

Los controladores de concurso (`prueba`, `resultado`, `resultadoindividual`, `resultadogeneral`, `resultadoequipos`, `ordensalida`) comparten inicialización común en `controller/base.controller.php`: basta declarar `modelClass` y `actionViews` para mantener la lógica de enrutado sin duplicación.

## Resultados por equipos

La opción **RESULTADOS EQUIPOS** aparece en la pantalla de resultados cuando en `concurso.xml` existe:

```xml
<equipos>si</equipos>
```

También se acepta el formato legado `<equipo>si</equipo>`, además de variantes `si/yes` sin distinguir mayúsculas.

Cuando está habilitado, la aplicación lee `equipos.xml` en la misma carpeta del `concurso.xml`. Reglas principales:

- Cada equipo debe tener **3 o 4 binomios**.
- Un binomio no puede repetirse dentro del mismo equipo.
- Para localizar cada binomio en resultados:
  - se intenta primero por licencia de jinete/caballo;
  - si la licencia no existe en resultados, se usa nombre normalizado.
- La puntuación de cada binomio es su **mejor nota** entre todas las pruebas.
- Si el equipo tiene 4 binomios, solo suman los 3 mejores; el no sumado se muestra tachado.

Ejemplo de `equipos.xml`:

```xml
<equipos>
  <equipo>
    <nombre>Equipo A</nombre>
    <binomios>
      <binomio>
        <jinete>
          <licencia>CM12345</licencia>
          <nombre>Dña. Ana Pérez</nombre>
        </jinete>
        <caballo>
          <licencia>CM67890</licencia>
          <nombre>TRUENO</nombre>
        </caballo>
      </binomio>
    </binomios>
  </equipo>
</equipos>
```

## Errores y logs

Los errores se registran mediante la configuración de logs de PHP/Apache. En producción no se muestran detalles técnicos al usuario.

## Mejoras propuestas (sin cambios funcionales)

- Añadir caché en memoria por petición para lecturas repetidas de JSON de resultados cuando se encadenan varias vistas del mismo concurso.
- Extraer renderizado HTML de tablas grandes de resultados a helpers/partials dedicados para reducir complejidad en `view/pruebaresultado.php`.
- Incorporar una batería de pruebas de rutas (smoke de `config/routes.php`) en CI para detectar rápido desajustes entre rutas y controladores.
