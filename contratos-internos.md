# Contratos internos

Este documento describe los formatos que comparten el catalogo, los datos, los modelos, los controladores y las vistas.

## Catalogo XML

`config.xml` contiene nodos de anio (`aAAAA`) con `concursos/concurso`. Cada concurso debe proporcionar:

- `id`: entero positivo y unico.
- `nombre`: nombre visible del concurso.
- `fecha`: formato `dd/mm/aaaa`.
- `fecha_inicio` y `fecha_fin`: opcionales para concursos de varios días; deben aparecer juntas y conservar el mismo formato.
- `datos`: ruta relativa al XML del concurso.
- `tipo` y `visible`: metadatos del catalogo.

El catalogo no se modifica desde la aplicacion. `CConcursosWrapper` valida el XML y permite buscar por anio o por ID.

## XML de concurso

El XML referenciado por `datos` debe contener el nodo `concurso`, la fecha, las pruebas y, cuando proceda, las ordenes de salida. Cada prueba incluye al menos un `id`, un titulo y un enlace relativo al JSON de resultados.

El formato antiguo utiliza `<fecha>` y un único bloque `<pruebas>`. Para un concurso de varios días se puede utilizar `<fecha_inicio>`, `<fecha_fin>` y `<jornadas>`. Cada `<jornada>` debe tener un `id` único, una `fecha` dentro del intervalo del concurso y su propio bloque `<pruebas>`. Los identificadores de prueba solo deben ser únicos dentro de su jornada, porque cada jornada representa resultados independientes.

La aplicación muestra una sección desplegable `RESULTADOS - fecha` por cada jornada. Si no existe `<jornadas>`, el formato antiguo se interpreta como una única jornada y se mantiene la sección `RESULTADOS` original. La validación de datos comprueba fechas, jornadas, identificadores duplicados, enlaces a JSON y que no falten pruebas.

Para resultados por equipos, el XML del concurso admite `equipos` (y por compatibilidad `equipo`) con valor `si/yes` o `no`. Si está habilitado, debe existir `equipos.xml` en la misma carpeta.

Para la pantalla **PLANO**, el XML del concurso admite el bloque opcional:

- `<planos><plano>ruta/relativa/al/plano_1.png</plano><plano>ruta/relativa/al/plano_2.png</plano></planos>`

El bloque puede contener uno o varios `plano`. Si no existe el bloque o ningún archivo es válido, la vista muestra una tarjeta vacía con el texto `NO HAY PLANOS DISPONIBLES` y un enlace de regreso al detalle del concurso.

## Lectura concurrente de datos

Las peticiones normales son de solo lectura. Los wrappers XML mantienen una caché independiente por ruta y los resultados JSON se reutilizan mientras el archivo conserve la misma versión (fecha de modificación y tamaño). Esto evita lecturas y parseos repetidos sin compartir estado mutable entre usuarios o peticiones PHP.

Los procesos de publicación deben escribir primero un archivo temporal y sustituir el archivo final con un renombrado atómico. La validación no debe ejecutarse sobre un archivo que todavía se esté escribiendo.

## Ubicación del concurso

El bloque opcional `<ubicacion>` puede contener `<nombre>` y `<visible>`. Si `<visible>` vale `si`, `sí` o `yes` (sin distinguir mayúsculas), la vista `view/body.php` muestra un bloque desplegable con el icono `location_on`. El mapa se carga dentro del bloque y puede mostrarse u ocultarse al pulsar el encabezado; el contenido desplegado no añade texto adicional al mapa. Si `<visible>` vale `no`, la sección no se renderiza. Cuando se omite `<visible>`, se mantiene visible por compatibilidad.

## Anuncios

El bloque opcional `<anuncios>` puede contener `<visible>` para mostrar u ocultar toda la colección. Cada `<anuncio>` también puede contener `<visible>`: `si`, `sí` o `yes` lo muestran y `no` lo oculta, sin distinguir mayúsculas y aceptando formas acentuadas o sin acento. Si se omite la etiqueta individual, el anuncio se mantiene visible por compatibilidad. Si todos los anuncios quedan ocultos, la tarjeta completa no se renderiza.

## XML de equipos

`equipos.xml` define `equipos/equipo` con esta estructura mínima:

- `equipo/nombre`
- `equipo/binomios/binomio`
- `binomio/jinete/(licencia y/o nombre)`
- `binomio/caballo/(licencia y/o nombre)`

Reglas de negocio:

- El equipo debe tener 3 o 4 binomios.
- No se permite repetir binomios dentro del mismo equipo.
- Si hay 4 binomios, en la suma final del equipo solo cuentan 3 (los de mayor puntuación).

## JSON de resultados

`CResultadoJson::load()` devuelve un array con esta estructura minima:

```text
{
  "concurso": { ... },
  "resultados": {
    "cabecera": [ ... ],
    "datos": [ ... ]
  }
}
```

Los consumidores deben tratar `cabecera` y `datos` como arrays. Los campos adicionales dependen del tipo de prueba, por ejemplo `notas`, `puesto`, `total`, `jueces`, `errores` y `observaciones`.

## Peticiones HTTP

- `c`: controlador permitido por la lista blanca de `index.php`.
- `a`: accion permitida por el controlador.
- `id`: entero positivo de concurso.
- `prueba`: identificador de prueba validado por el modelo correspondiente.
- `jinete` y `caballo`: identificadores alfanumericos normalizados antes de crear enlaces.

Los valores que se insertan en HTML pasan por `escape_html()`. Las rutas de recursos y datos se resuelven mediante `CProjectPath`.

## Capas de aplicacion

- `index.php` valida ruta y accion, y crea el controlador.
- `controller/` coordina la peticion, el modelo y la vista.
- `model/` prepara datos y no genera HTML.
- `database/` valida y carga XML/JSON, resuelve rutas y registra errores.
- `view/` presenta datos ya preparados y escapa cualquier valor dinamico.

### Contrato de navegacion de vistas

Las vistas internas de concurso reutilizan `view/partials/navigation-concurso.php` para renderizar navegacion de escritorio y movil.

Contrato de uso:

- La vista define `$activeSection` antes del `require`.
- Valores permitidos: `inicio`, `plano`, `orden`, `resultados`.
- El partial resuelve los enlaces con `GetConcursoId()` y aplica la clase `active` en la seccion correspondiente.
- Cualquier cambio de opciones o etiquetas del menu se hace en ese partial, no en cada vista.

Los cambios de estos contratos deben reflejarse en `tools/validate-data.php`, `tools/automated-tests.php` y este documento.

## Compatibilidad de URLs

La lista blanca de `config/routes.php` conserva los nombres de controladores y acciones usados por las URLs existentes. Las pruebas automatizadas verifican las rutas publicas principales antes de publicar cambios.

Las mejoras internas deben mantener estos parametros y combinaciones:

- `Concursos/Index`
- `Prueba/MostrarPrueba`
- `Prueba/Plano`
- `Resultado/Resultado`
- `ResultadoGeneral/ResultadoGeneral`
- `ResultadoEquipos/ResultadoEquipos`
- `ResultadoIndividual/ResultadoIndividual`
- `OrdenSalida/Listado`

El nombre del controlador se normaliza a minusculas para localizar la ruta, pero el nombre de la accion se conserva tal como esta definido para evitar aceptar acciones no previstas.
