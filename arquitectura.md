# Arquitectura y mantenimiento de Resultados Ecuestres

> **Regla de mantenimiento:** cada cambio funcional, visual, de configuración o de operación debe reflejarse también en este documento. Si cambia un recurso versionado, se actualizará igualmente su número de versión aquí.

## 1. Descripción general

`resultados-ecuestres` es una aplicación PHP sin base de datos. Publica concursos hípicos cuyos metadatos se almacenan en XML y cuyos resultados se almacenan en JSON. La presentación usa MaterializeCSS y estilos propios en `recursos/css/app.css`.

La aplicación se sirve mediante Apache. En el entorno local actual se publica como:

```text
http://localhost:8080/resultados-ecuestres/
```

Requisitos principales:

- PHP 8.2 o superior.
- Apache.
- Extensiones PHP `SimpleXML`, `JSON`, `mbstring` y `BCMath`.
- `mod_headers` recomendado para las cabeceras de seguridad y la caché.

## 2. Flujo de una petición

Todas las peticiones dinámicas pasan por `index.php`, que actúa como front controller:

1. Carga `configuracion.php` y `database/security.php`.
2. Lee `c` (controlador) y `a` (acción) de la URL.
3. Comprueba la ruta mediante una lista blanca.
4. Valida el parámetro `id` cuando la ruta lo necesita.
5. Carga el controlador autorizado.
6. El controlador solicita datos al modelo.
7. El modelo lee XML o JSON mediante las clases de `database/`.
8. La vista genera HTML.
9. Apache entrega los recursos estáticos aplicando las reglas de `.htaccess`.

Ejemplos:

```text
?c=Concursos&a=Index
?c=Prueba&a=Plano&id=15
?c=Prueba&a=ListadoResultado&id=15&prueba=1
?c=ResultadoIndividual&a=ResultadoIndividual&id=15&prueba=1&jinete=CM17341&caballo=CM5887
```

Las rutas y acciones válidas se declaran explícitamente en `index.php`. Una ruta no incluida devuelve HTTP 404 y un identificador inválido devuelve HTTP 400.

## 3. Estructura del proyecto

| Carpeta o archivo | Responsabilidad |
| --- | --- |
| `index.php` | Punto de entrada, lista blanca de rutas y manejo de errores |
| `controller/` | Recibe la petición y coordina modelos y vistas |
| `model/` | Lógica de consulta y preparación de datos |
| `view/` | Plantillas HTML y presentación |
| `view/partials/` | Fragmentos de presentación reutilizables, como la navegación de Inicio, concurso y enlaces de pruebas |
| `database/` | Lectura de datos, rutas del proyecto, seguridad y utilidades |
| `recursos/AAAA/N/` | Datos de cada concurso |
| `materialize/` | MaterializeCSS y JavaScript local |
| `recursos/css/app.css` | Estilos propios, accesibilidad y diseño móvil |
| `tools/` | Validación, pruebas, copias y restauración |
| `.htaccess` | Cabeceras, errores visibles y caché de recursos |
| `config.xml` | Catálogo de concursos |
| `configuracion.php` | Configuración de errores y logging de PHP |
| `config/paths.php` | Configuración separable de raíces de código, datos y catálogo |
| `config/environment.php` | Selección segura del entorno y de la visibilidad de errores |
| `config/routes.php` | Lista blanca de rutas y acciones, preservando las URLs existentes |
| `config/constants.php` | Año visible por defecto y versiones de recursos estáticos |
| `config/*.env.example` | Plantillas de variables para desarrollo y producción |
| `logs/` | Archivos diarios propios de aplicación y errores, protegidos contra acceso web |
| `contratos-internos.md` | Contratos XML, JSON, parámetros HTTP y responsabilidades por capa |

Los controladores de concursos, pruebas y resultados heredan de `controller/base.controller.php`. Esta clase concentra la resolución segura del concurso solicitado y el renderizado común de cabecera, vista y pie. Las acciones repetitivas se declaran como mapas `actionViews` y se resuelven mediante `__call()`, por lo que los controladores concretos solo configuran su modelo y sus vistas. Los modelos mantienen la lectura y preparación de datos.

La preparación de la pantalla inicial también está separada: `model/concursos.php` ordena los concursos por fecha, calcula los identificadores de concursos pendientes y prepara los metadatos de presentación mediante `GetConcursosForDisplay()`. `view/body_concursos.php` se limita a renderizar el resultado y gestionar la búsqueda del navegador.

La navegación de la pantalla inicial está centralizada en `view/partials/navigation-home.php`, evitando duplicar su estructura HTML en la vista principal.

La navegación interna de concurso (Inicio, Plano, Orden de salida y Resultados) está centralizada en `view/partials/navigation-concurso.php`. Las vistas de detalle solo definen `activeSection` y reutilizan ese partial para escritorio y móvil, reduciendo duplicación y simplificando cambios futuros.

La salida de datos procedentes de XML, JSON o parámetros de URL debe pasar por `escape_html()` antes de insertarse en HTML. Los identificadores de petición se validan con `request_positive_int()` y los identificadores de jinete/caballo se normalizan mediante una lista de caracteres permitidos antes de usarse en enlaces.

### Archivos PHP principales

| Archivo | Función resumida |
| --- | --- |
| `controller/concursos.controller.php` | Lista y muestra los concursos disponibles |
| `controller/prueba.controller.php` | Gestiona pruebas, plano de instalaciones, órdenes de salida y listados de resultados |
| `controller/resultado.controller.php` | Gestiona resultados de una prueba |
| `controller/resultadogeneral.controller.php` | Gestiona la clasificación o resultado general |
| `controller/resultadoequipos.controller.php` | Gestiona la clasificación de resultados por equipos |
| `controller/resultadoindividual.controller.php` | Muestra el detalle de un jinete y caballo |
| `controller/ordensalida.controller.php` | Gestiona la consulta de órdenes de salida |
| `controller/base.controller.php` | Comparte la carga del concurso y el renderizado común |
| `model/concursos.php` | Modelo histórico de concursos |
| `model/concurso.model.php` | Modelo consolidado para consultar los datos de un concurso |
| `model/data/concurso-display-data.php` | Objeto de datos tipado para la pantalla inicial |
| `model/prueba.php` | Consulta la información de las pruebas |
| `model/resultado.php` | Consulta los resultados de una prueba |
| `model/resultadogeneral.php` | Prepara los resultados generales |
| `model/resultadoequipos.php` | Calcula la clasificación por equipos con reglas de validación |
| `model/resultadoindividual.php` | Prepara el detalle individual y las notas de jueces |
| `model/ordensalida.php` | Consulta las imágenes o datos de orden de salida |
| `database/projectpath.php` | Centraliza las rutas físicas del proyecto |
| `database/concursoswrapper.php` | Compatibilidad y acceso auxiliar al catálogo |
| `database/concursowrapper.php` | Acceso auxiliar a los datos de un concurso |
| `database/resultadojson.php` | Lee, decodifica y reutiliza JSON de resultados |
| `database/security.php` | Escapado HTML, validación de parámetros, URLs y puntuaciones |

### Vistas

| Archivo | Función resumida |
| --- | --- |
| `view/header.php` | Cabecera común de las páginas interiores y carga de CSS |
| `view/header_concursos.php` | Cabecera de la página principal de concursos |
| `view/footer.php` | Pie común y carga de Materialize/JavaScript |
| `view/body.php` | Contenedor base de contenido |
| `view/body_concursos.php` | Lista principal de concursos |
| `view/pruebalistadoordensalida.php` | Listado de órdenes de salida |
| `view/pruebalistadoresultado.php` | Listado de resultados de una prueba |
| `view/pruebaresultado.php` | Resultados con acceso al detalle individual |
| `view/pruebaresultadogeneral.php` | Clasificación general |
| `view/pruebaresultadoequipos.php` | Clasificación por equipos y detalle de binomios/puntuaciones |
| `view/pruebaresultadoindividual.php` | Detalle individual, jueces, notas y observaciones |
| `view/tools.php` | Utilidades compartidas de presentación |

### Reglas funcionales de resultados por equipos

- El botón **RESULTADOS EQUIPOS** se muestra cuando `<equipos>` (o legado `<equipo>`) del `concurso.xml` vale `si/yes` (sin distinguir mayúsculas).
- La definición de equipos se carga desde `equipos.xml` en la misma carpeta del `concurso.xml`.
- Cada equipo debe tener **3 o 4 binomios**; si no, se marca como inválido y se muestra aviso al final.
- Un mismo binomio no puede repetirse dentro del mismo equipo.
- Búsqueda de resultados de binomio:
  - Para **jinete** y **caballo**, se intenta primero por licencia.
  - Si no existe esa licencia en resultados, se busca por nombre normalizado (ignorando tratamientos como `D.`, `Dña.`, `Don`, `Doña`, `Sr.`, `Sra.`).
- Si un binomio no se encuentra, se informa con un aviso explícito.
- Puntuación:
  - Se toma la **mejor nota** del binomio entre todas las pruebas del concurso.
  - En equipos de 4, suman solo los **3 mejores**; el no sumado se muestra tachado en el detalle.

### Recursos de interfaz

| Archivo o carpeta | Función resumida |
| --- | --- |
| `recursos/css/app.css` | Fondo, accesibilidad, tablas, puntuaciones y ajustes responsive propios |
| `materialize/css/materialize.css` | Framework CSS local de Materialize |
| `materialize/js/materialize.js` | Componentes JavaScript de Materialize |
| `materialize/js/init.js` | Inicializa el menú lateral móvil sin jQuery |
| `imagenes/` | Logos, iconos y carteles de concursos |
| `contador/` | Imágenes utilizadas por el contador existente |

### Herramientas operativas

| Archivo | Función resumida |
| --- | --- |
| `tools/validate-data.php` | Valida catálogo, XML, fechas, enlaces y estructura JSON |
| `tools/smoke-test.php` | Comprueba extensiones PHP y el flujo básico de lectura |
| `tools/run-checks.bat` | Ejecuta lint de vistas, prueba de humo, pruebas automatizadas y validación de datos |
| `tools/maintenance-check.bat` | Comprueba archivos esenciales y ejecuta la rutina operativa completa |
| `tools/health-check.php` | Devuelve el estado técnico básico en JSON para monitorización |
| `tools/backup-project.ps1` | Crea una copia comprimida del proyecto, excluyendo `config.xml` |
| `tools/restore-project.ps1` | Restaura una copia comprimida con confirmación explícita |

Cada concurso suele tener esta estructura:

```text
recursos\2026\15\
  concurso.xml
  resultados\*.json
  orden_salida\*.png
```

`config.xml` relaciona los concursos con sus fechas, nombres y directorios. Debe tratarse como catálogo de producción y no modificarse accidentalmente.

## 4. Cómo actualizar datos

Para publicar un concurso nuevo:

1. Crear la carpeta `recursos\AAAA\N\`.
2. Añadir su `concurso.xml`.
3. Añadir los JSON de resultados en `resultados\`.
4. Añadir las imágenes de orden de salida, si existen.
5. Registrar el concurso en `config.xml` siguiendo el formato existente.
6. Ejecutar `tools\validate-data.php`.
7. Ejecutar `tools\smoke-test.php` o `tools\run-checks.bat`.
8. Crear una copia antes de publicar.

Por instrucción operativa, los concursos 2, 9 y 10 no se deben modificar. La copia de seguridad tampoco incluye `config.xml`; este archivo debe conservarse por separado.

## 4.1. Separación de configuración, datos y código

La aplicación permite mantener el código PHP separado de los datos mediante `config/paths.php` y variables de entorno:

```text
RESULTADOS_APP_ROOT=C:\ruta\al\codigo
RESULTADOS_DATA_ROOT=D:\ruta\a\los\datos
RESULTADOS_CATALOG_PATH=D:\ruta\a\los\datos\config.xml
```

`database/projectpath.php` centraliza estas rutas. Los modelos y la validación utilizan la raíz de datos para XML, JSON e imágenes, mientras que el catálogo puede mantenerse en una ubicación independiente. Si las variables no existen, se conserva el comportamiento actual sin mover archivos.

Esta separación permite actualizar el código sin sobrescribir resultados y hacer copias de datos o catálogo de forma independiente. Las variables deben configurarse en Apache/PHP o en el entorno del proceso; no deben escribirse como secretos dentro del código.

## 4.2. Entornos de desarrollo y producción

El entorno se selecciona con `RESULTADOS_ENV`. Si no se define, se usa `production` y los errores permanecen ocultos. `RESULTADOS_DISPLAY_ERRORS=1` solo debe usarse en desarrollo.

Desarrollo puede usar una única carpeta local para facilitar las pruebas. Producción debe separar físicamente:

```text
C:\SERVICIOS\resultados-ecuestres\app   código PHP, vistas y recursos estáticos
C:\SERVICIOS\resultados-ecuestres\data  config.xml, XML, JSON e imágenes de concursos
C:\SERVICIOS\resultados-ecuestres\logs  logs propios de la aplicación
```

Las plantillas de `config/` no contienen secretos ni se activan automáticamente. Deben copiarse como variables del servicio Apache/PHP. `config/.htaccess` impide servirlas directamente.

## 5. Cómo actualizar el código

Antes de cambiar PHP, CSS o JavaScript:

1. Crear una copia con `tools\backup-project.ps1`.
2. Revisar si existe código reutilizable en `database/`, `model/` o `view/`.
3. Mantener el flujo controlador-modelo-vista.
4. Escapar cualquier dato dinámico que llegue a HTML con las funciones de `database/security.php`.
5. Ejecutar `tools\run-checks.bat`.
6. Probar las rutas afectadas en escritorio y móvil.
7. Publicar y conservar la copia de recuperación.

No se deben introducir credenciales, datos privados ni secretos en el código o en los JSON.

## 5.1. Estándares de calidad PHP

El código PHP nuevo debe seguir estas reglas:

- Usar nombres descriptivos y una responsabilidad clara por clase o función.
- Mantener el flujo controlador-modelo-vista y reutilizar helpers existentes.
- Validar entradas con las funciones de `database/security.php` y escapar la salida HTML.
- Propagar errores de lectura o validación; no ocultarlos con valores por defecto silenciosos.
- Usar comparaciones estrictas cuando se comparen tipos conocidos y evitar conversiones implícitas innecesarias.
- Ejecutar `tools\run-checks.bat`, que comprueba la sintaxis de todos los archivos PHP del proyecto, antes de publicar.

Estas reglas son compatibles con el estilo PSR-12 sin exigir todavía una dependencia externa de formateo. La adopción de un analizador adicional se puede hacer posteriormente sin cambiar los contratos de datos ni las URLs.

El código nuevo usa `camelCase` para métodos y variables, nombres de clase en `PascalCase` y constantes en `UPPER_SNAKE_CASE`. Los métodos históricos con formato `Get...` se mantienen para compatibilidad y no deben usarse en código nuevo. Por ejemplo, la preparación de concursos usa `getDisplayConcursos()`.

El código no utilizado o comentado se elimina en lugar de conservar implementaciones alternativas dentro de las vistas. Las decisiones de diseño se documentan aquí y no se mantienen como bloques ejecutables comentados.

Las validaciones usan retornos tempranos cuando una entrada no es válida, evitando anidamientos innecesarios. Las condiciones compuestas se extraen a variables o métodos con nombres descriptivos cuando representan una regla de negocio.

Las refactorizaciones se realizan por bloques pequeños: se cambia una responsabilidad aislada, se ejecutan las comprobaciones existentes y solo después se aborda el siguiente bloque. En esta fase se consolidó la lectura de campos del concurso por ID sin cambiar sus métodos públicos ni el formato de los datos.

Las acciones representadas solo por iconos deben usar la clase `icon-action`, un área mínima de 44 píxeles y `aria-label`/`title` descriptivos para que sean fáciles de pulsar y comprensibles sin identificar el icono visualmente.

Los estados de error, información y ausencia de datos usan la clase `status-message`, un icono decorativo y `role="alert"` o `role="status"` según corresponda.

Las notas finales se presentan siempre con punto decimal y tres posiciones (`xx.yyy`) mediante `format_score()`, tanto en botones enlazados como en resultados no enlazables.

## 6. Añadir una sección nueva

Una sección nueva debe conectarse a todas las capas:

1. **Ruta:** añadir en la lista `$routes` de `index.php` el nombre, archivo del controlador, clase y acciones permitidas.
2. **Controlador:** crear `controller/nueva.controller.php`, validar parámetros y llamar al modelo.
3. **Modelo:** crear o ampliar un modelo en `model/` para leer los datos necesarios.
4. **Datos:** definir el XML/JSON requerido y reutilizar `database/projectpath.php` y `database/resultadojson.php`.
5. **Vista:** crear la plantilla en `view/`, cargando la cabecera y el pie comunes.
6. **Seguridad:** validar IDs, URLs y parámetros; escapar todas las salidas.
7. **Diseño:** usar las clases existentes y comprobar la vista móvil.
8. **Pruebas:** añadir la ruta a la prueba de humo si es una funcionalidad esencial y ejecutar todas las validaciones.

No conviene crear accesos directos que eviten `index.php`, porque se perderían la lista blanca, la validación y el manejo común de errores.

## 7. Caché

`.htaccess` configura caché para recursos estáticos:

- CSS y JavaScript: 7 días (`max-age=604800`).
- Imágenes: 30 días.
- `Cache-Control: public` permite que navegador y cachés intermedias reutilicen esos archivos.

Las páginas PHP y los JSON no tienen una caché HTTP de larga duración, por lo que los resultados se consultan dinámicamente.

Para evitar que el navegador conserve un CSS o JavaScript antiguo, las vistas cargan recursos versionados:

```html
app.css?v=20260814-15
init.js?v=20260814-1
```

Después de modificar un recurso estático hay que incrementar la versión en `view/header.php` y `view/header_concursos.php` (o en la vista que lo cargue). No es necesario borrar la caché del navegador.

La versión actual de `app.css` es `20260814-61`. El número no representa una versión de la aplicación: es únicamente un identificador de cambio del recurso estático. Puede utilizarse cualquier identificador nuevo y distinto, por ejemplo una fecha o un número incremental.

Para cambiar la configuración:

- Cambiar la duración `7 days` o `30 days` en `.htaccess`.
- Cambiar el valor equivalente `max-age`.
- Mantener el versionado cuando se cambie CSS o JavaScript.
- Reiniciar Apache si la configuración del módulo no se aplica inmediatamente.

Una duración demasiado larga mejora el rendimiento, pero retrasa la llegada de cambios si se olvida actualizar la versión.

## 8. Logs y errores

`configuracion.php`:

- Activa `error_reporting(E_ALL)`.
- Mantiene `display_errors` desactivado por defecto.
- Activa `log_errors`.
- Permite usar `RESULTADOS_DISPLAY_ERRORS=1` temporalmente en desarrollo.

En producción, el usuario solo recibe un mensaje genérico HTTP 500. La excepción completa se envía a `error_log($exception)`.

Los logs concretos dependen de la instalación:

- Log de errores de PHP configurado en `php.ini`.
- Log de errores de Apache configurado en Apache/XAMPP.
- Log de acceso de Apache para peticiones HTTP.

No existe todavía un sistema propio de logs estructurados, rotación, métricas o panel de monitorización. Para investigar un error hay que consultar los logs de PHP y Apache y reproducir la URL afectada.

Nunca se debe dejar `RESULTADOS_DISPLAY_ERRORS=1` en producción, porque puede revelar rutas, consultas y detalles internos.

## 9. Seguridad incorporada

Se han añadido:

- Lista blanca de controladores y acciones.
- Validación de identificadores positivos.
- Escape HTML con `htmlspecialchars`.
- Validación de URLs externas permitidas.
- Cabeceras `X-Content-Type-Options`, `Referrer-Policy` y `Permissions-Policy`.
- Errores técnicos ocultos en producción.
- Dependencias de datos y rutas centralizadas.

La aplicación sigue sin autenticación ni panel de administración. La actualización de datos se realiza directamente sobre archivos del servidor, por lo que el acceso al servidor debe estar protegido.

## 10. Interfaz y JavaScript

MaterializeCSS se carga desde una copia local. `materialize/js/init.js` inicializa el menú móvil con `M.Sidenav` sin depender de jQuery. El parámetro de versión de `init.js` evita que Android o un navegador de escritorio conserve una versión antigua.

Las tablas principales se han mantenido en formato clásico vertical. En móvil se han añadido reglas específicas para:

- Evitar el scroll horizontal innecesario.
- Ocultar columnas secundarias cuando corresponde.
- Ajustar tablas de notas y jueces.
- Mantener las puntuaciones completas con tres decimales.

La navegación incluye un enlace permanente a `INICIO`, acceso a `PLANO`, menú lateral para móvil y una marca de página consistente. El bloque de navegación de concurso se genera desde un único partial (`navigation-concurso.php`) con sección activa configurable, de forma que todas las vistas mantienen el mismo comportamiento. La página inicial incorpora un título descriptivo, una explicación breve y búsqueda instantánea por fecha, nombre o tipo de concurso.

Los listados de resultados incorporan búsqueda por atleta o caballo y un aviso cuando no existen coincidencias.

La sección `PLANO` replica la estructura visual de `ORDEN DE SALIDA`: una tarjeta por cada imagen configurada. Cuando no hay imágenes válidas en `concurso.xml`, muestra una tarjeta vacía con el mensaje `NO HAY PLANOS DISPONIBLES` y un botón para volver al detalle del concurso.

El detalle individual incorpora títulos de sección y estilos de impresión preparados para futuras necesidades. Actualmente solo muestra la flecha centrada para regresar al listado; no incluye exportación PDF. El formato de impresión existente usa A4 vertical, escala el contenido al 32 %, reduce tipografía y espaciado, permite saltos entre filas y oculta el menú superior para forzar una sola hoja.

Las mejoras de accesibilidad incluyen foco visible de alto contraste, objetivos táctiles mínimos para el menú, enlaces del menú lateral con altura suficiente y respeto de `prefers-reduced-motion`.

Para rendimiento se usan los archivos minificados locales de Materialize, los scripts se cargan con `defer` y las imágenes secundarias de los listados usan carga diferida (`loading="lazy"`). Se mantiene la caché HTTP y el versionado de `app.css`.

La validación de calidad de datos comprueba además que los IDs de concurso no estén duplicados, que los campos obligatorios del catálogo existan y que las fechas del catálogo tengan formato `dd/mm/aaaa`.

Para producción, `config/environment.php` fuerza `display_errors` a desactivado cuando `RESULTADOS_ENV=production`, aunque exista una variable de entorno que lo solicite. El `.htaccess` desactiva el listado de directorios, bloquea el acceso web a `config.xml`, archivos de entorno, logs y scripts operativos, y añade las cabeceras `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` y `Permissions-Policy`.

La revisión visual y de accesibilidad ha comprobado la pantalla inicial y el detalle individual en escritorio y móvil, incluyendo navegación, búsqueda, títulos centrados, menú lateral, cambio de contraste, logotipos, tablas de notas, resumen de jueces, flechas de regreso, foco de teclado, etiquetas de controles y adaptación a pantallas inferiores a 993 píxeles. Se corrigieron etiquetas `INICIO` inconsistentes, nombres accesibles para los disparadores y enlaces de regreso, y una estructura HTML anidada inválida en la pantalla inicial.

La paleta visual normal usa fondo `#f4f7fb`, superficies blancas, azul marino `#1e3a5f`, turquesa `#197c78` para acciones y texto `#1f2937`. El modo contraste usa una escala de grises azulados. Ambas paletas están centralizadas en `recursos/css/app.css`: los valores normales están en `:root` y los valores de contraste en `body.contrast-mode`, usando las variables activas `--color-background`, `--color-primary`, `--color-secondary`, `--color-surface`, `--color-panel`, `--color-panel-header`, `--color-text`, `--color-text-inverse`, `--color-focus` y `--color-muted`. Para cambiar un color global basta modificar el valor correspondiente en el bloque del modo deseado.

La hoja `recursos/css/app.css` incluye comentarios de sección para identificar la paleta, navegación y footer, tarjetas y paneles, accesibilidad, buscadores, impresión, tablas responsive, multimedia y reducción de animaciones. Esta organización permite localizar cada grupo de estilos sin revisar archivo por archivo.

El usuario puede alternar entre modo normal y modo de alto contraste mediante el elemento `contrast-menu-item`, añadido automáticamente al menú lateral móvil y al menú superior de escritorio. En anchos móviles inferiores a 993 px no se crea el elemento del menú superior, evitando que aparezca fuera del menú lateral. La selección se aplica con la clase `contrast-mode`, utiliza una escala de grises azulados diferenciados, texto blanco y amarillo para acciones, y se conserva en `localStorage` con la clave `resultados-contrast`. El comportamiento se inicializa en `materialize/js/init.js`.

En modo contraste, los logotipos con clase `hipica-logo` se muestran sobre una superficie blanca con borde claro para conservar su visibilidad independientemente de los colores originales de cada imagen.

Las imágenes de la interfaz usan `loading="lazy"` cuando no son críticas y `decoding="async"` para no bloquear el renderizado. La hoja común limita su anchura al contenedor y Apache aplica caché de 30 días para los formatos de imagen.

Tras cambiar CSS o JavaScript se debe probar la página en una anchura móvil aproximada de 360-430 píxeles y también en orientación horizontal.

## 11. Validación, publicación y recuperación

Comprobación completa en Windows:

```text
tools\run-checks.bat
```

Las pruebas funcionales automatizadas incluyen `tools\smoke-test.php`, que comprueba extensiones PHP, catálogo, XML de datos y resultados JSON. También se validan las rutas HTTP principales y `tools\health-check.php`; la ejecución actual devuelve HTTP 200 en Inicio, resultados y health check, y valida 11 concursos con 130 JSON.

`tools\automated-tests.php` contiene pruebas automatizadas ligeras sin dependencias externas para escape HTML, formato de puntuaciones, validación de identificadores, validación de URLs de YouTube, ordenación de concursos y cálculo de concursos pendientes. Se ejecuta automáticamente desde `tools\run-checks.bat`.

El script realiza:

1. Lint de todas las vistas PHP.
2. Prueba de extensiones y flujo principal.
3. Validación de XML, catálogo, enlaces y JSON.

Crear una copia:

```powershell
powershell -ExecutionPolicy Bypass -File tools\backup-project.ps1
```

Restaurar una copia:

```powershell
powershell -ExecutionPolicy Bypass -File tools\restore-project.ps1 `
  -Archive C:\PROYECTOS\backups\resultados-ecuestres-AAAAMMDD-HHMMSS.zip -Force
```

La restauración sobrescribe archivos del proyecto, pero la copia excluye deliberadamente `config.xml`.

## 14. Mantenimiento y operación

Antes de publicar cambios o datos:

```text
tools\maintenance-check.bat
```

La rutina comprueba que existen `config.xml`, `configuracion.php`, `.htaccess` e `index.php`, y después ejecuta lint PHP, prueba de humo y validación de XML/JSON.

El procedimiento recomendado es:

1. Revisar los logs de error y acceso de Apache y PHP.
2. Ejecutar `tools\maintenance-check.bat`.
3. Crear una copia con `tools\backup-project.ps1`.
4. Publicar los cambios.
5. Comprobar las rutas principales en escritorio y móvil.
6. Conservar la copia hasta confirmar que la publicación funciona.

`tools/health-check.php` devuelve un estado JSON y código de salida para integrarlo con el Programador de tareas de Windows, un monitor HTTP externo o un sistema de alertas. Los errores de aplicación se registran como JSON mediante `database/logger.php`, incluyendo fecha, nivel, ruta, acción y tipo de excepción sin mostrar detalles al usuario.

Además, `database/logger.php` escribe directamente en `logs/application-AAAA-MM-DD.log` y, para errores, en `logs/errors-AAAA-MM-DD.log`. Los archivos son líneas JSON, se bloquean durante la escritura y se conserva `error_log()` como respaldo si la carpeta no está disponible. `logs/.htaccess` impide servirlos directamente.

Todavía no hay monitorización externa, alertas ni rotación automática más allá de la separación diaria por fecha. Los logs deben archivarse o eliminarse mediante la planificación del sistema operativo. No se deben borrar durante una investigación de incidencias.

## 12. Mejoras realizadas

- Front controller con rutas permitidas y respuestas HTTP coherentes.
- Lectura centralizada y validación de JSON.
- Validación automatizada de XML y datos.
- Correcciones de rutas y modelos duplicados.
- Escape de salidas y validación de parámetros.
- Cabeceras de seguridad.
- Configuración de errores separada del código.
- Menú móvil sin dependencia de jQuery.
- Diseño responsive y tablas clásicas verticales.
- Puntuaciones con tres decimales.
- Caché HTTP de recursos estáticos y versionado de assets.
- Prueba de humo, lint y validación ejecutables con un comando.
- Scripts de copia y restauración.
- Eliminación de código de depuración y vistas antiguas.

## 13. Limitaciones actuales y mejoras futuras

Actualmente:

- Los datos se gestionan manualmente mediante archivos.
- No hay base de datos ni transacciones.
- No hay autenticación, roles ni panel de gestión.
- No hay tests automatizados de navegador.
- No hay logs estructurados propios, alertas ni métricas.
- La caché de página completa no está habilitada.
- `config.xml` sigue siendo un punto manual y sensible.
- La dependencia de Apache/XAMPP debe documentarse al migrar a otro servidor.

Como evolución futura se podría incorporar un esquema formal de datos, validación JSON Schema/XML Schema, tests de integración, rotación de logs, monitorización, despliegue automatizado y un panel protegido para gestionar concursos.
