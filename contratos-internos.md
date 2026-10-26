# Contratos internos

Este documento describe los formatos que comparten el catalogo, los datos, los modelos, los controladores y las vistas.

## Catalogo XML

`config.xml` contiene nodos de anio (`aAAAA`) con `concursos/concurso`. Cada concurso debe proporcionar:

- `id`: entero positivo y unico.
- `nombre`: nombre visible del concurso.
- `fecha`: formato `dd/mm/aaaa`.
- `datos`: ruta relativa al XML del concurso.
- `tipo` y `visible`: metadatos del catalogo.

El catalogo no se modifica desde la aplicacion. `CConcursosWrapper` valida el XML y permite buscar por anio o por ID.

## XML de concurso

El XML referenciado por `datos` debe contener el nodo `concurso`, la fecha, las pruebas y, cuando proceda, las ordenes de salida. Cada prueba incluye al menos un `id`, un titulo y un enlace relativo al JSON de resultados.

Para resultados por equipos, el XML del concurso admite `equipos` (y por compatibilidad `equipo`) con valor `si/yes` o `no`. Si está habilitado, debe existir `equipos.xml` en la misma carpeta.

Para la pantalla **PLANO**, el XML del concurso admite el bloque opcional:

- `<planos><plano>ruta/relativa/al/plano_1.png</plano><plano>ruta/relativa/al/plano_2.png</plano></planos>`

El bloque puede contener uno o varios `plano`. Si no existe el bloque o ningún archivo es válido, la vista muestra una tarjeta vacía con el texto `NO HAY PLANOS DISPONIBLES` y un enlace de regreso al detalle del concurso.

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
