<?php require_once __DIR__ . '/partials/navigation-home.php'; ?>

<style>
    .material-icons {
        translate: no !important;
    }

    .texto-destacado {
        font-size: 1.25rem !important;
        font-weight: bold !important;
    }

    @media only screen and (max-width: 600px) {
        table.tabla-movil th,
        table.tabla-movil td {
            font-size: 11px !important;
            padding: 5px 2px !important;
        }

        table.tabla-movil th:nth-child(1),
        table.tabla-movil td:nth-child(1) { width: 10% !important; }
        table.tabla-movil th:nth-child(2),
        table.tabla-movil td:nth-child(2) { width: 75% !important; word-break: break-word; }
        table.tabla-movil th:nth-child(3),
        table.tabla-movil td:nth-child(3) { width: 15% !important; text-align: right !important; }
    }
</style>

<div class="row">
    <div class="col s12 center">
        <h1 class="titulo-inicio">RESULTADOS ECUESTRES</h1>
        <p class="subtitulo-inicio">Consulta por fechas y/o pruebas</p>
        <div class="input-field inicio-busqueda">
            <i class="material-icons prefix">search</i>
            <input id="buscar-concursos" type="search" autocomplete="off" aria-label="Buscar concursos por fecha, nombre o tipo">
            <label for="buscar-concursos">Buscar concurso</label>
        </div>
        <div>
            <img class="hipica-logo" loading="lazy" decoding="async" style="border:0;width:auto;max-width:300px;height:150px" src="recursos/imagenes/perfil.png" alt="RECURSOS ECUESTRES" />
        </div>
        <ul class="collapsible">
            <li>
                <?php
                $concursos = $this->m_model->getDisplayConcursos(RESULTADOS_DEFAULT_YEAR);
                $nuevos = count(array_filter($concursos, static function (ConcursoDisplayData $concurso): bool {
                    return $concurso->isUpcoming;
                }));
                ?>
                <div class="collapsible-header blue-grey lighten-1">
                    <i class="large material-icons">favorite</i>
                    CONCURSOS <?php echo RESULTADOS_DEFAULT_YEAR; ?>
                    <?php if ($nuevos > 0): ?>
                        <span class="new badge blue darken-4" data-badge-caption="NUEVO"><?php echo $nuevos; ?></span>
                    <?php endif; ?>
                </div>
                <div class="collapsible-body">
                    <div class="collection">
                        <?php if (count($concursos) > 0): ?>
                            <table class="tabla-movil">
                                <thead><tr>
                                    <th class="center-align hide-on-small-only"></th>
                                    <th class="center-align hide-on-small-only">TIPO</th>
                                    <th class="center-align">FECHA</th>
                                    <th class="center-align">CONCURSO</th>
                                    <th></th>
                                </tr></thead>
                                <tbody>
                                    <?php foreach ($concursos as $concurso): ?>
                                        <tr class="concurso-fila" data-search="<?php echo escape_html($concurso->search); ?>">
                                            <td class="center-align hide-on-small-only">
                                                <img loading="lazy" decoding="async" src="<?php echo escape_html($concurso->statusImage); ?>" alt="<?php echo escape_html($concurso->statusAlt); ?>" width="50" height="50">
                                            </td>
                                            <td class="center-align hide-on-small-only">
                                                <?php if ($concurso->typeImage !== null): ?>
                                                    <img loading="lazy" decoding="async" src="<?php echo escape_html($concurso->typeImage); ?>" alt="<?php echo escape_html($concurso->typeAlt); ?>" width="50" height="50">
                                                <?php else: ?>
                                                    <?php echo escape_html($concurso->tipo); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="center-align<?php echo $concurso->isUpcoming ? ' text-darken-1 texto-destacado' : ''; ?>"><?php echo escape_html($concurso->fecha); ?></td>
                                            <td class="center-align<?php echo $concurso->isUpcoming ? ' text-darken-1 texto-destacado' : ''; ?>"><?php echo escape_html($concurso->nombre); ?></td>
                                            <td class="center-align"><a class="icon-action" href="?c=Prueba&a=MostrarPrueba&id=<?php echo escape_html($concurso->id); ?>" aria-label="Ver pruebas de <?php echo escape_html($concurso->nombre); ?>" title="Ver pruebas">
                                                <i class="medium material-icons<?php echo $concurso->isUpcoming ? ' indigo-text text-darken-1' : ''; ?>"><?php echo escape_html($concurso->actionIcon); ?></i>
                                            </a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <p id="sin-resultados-concursos" class="center-align" hidden>No hay concursos que coincidan con la búsqueda.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var input = document.getElementById('buscar-concursos');
            var rows = document.querySelectorAll('.concurso-fila');
            var empty = document.getElementById('sin-resultados-concursos');

            if (!input) {
                return;
            }

            input.addEventListener('input', function () {
                var query = input.value.trim().toLowerCase();
                var visible = 0;
                input.parentElement.classList.toggle('has-value', input.value.trim() !== '');

                if (query) {
                    var collapsible = document.querySelector('.collapsible');
                    var instance = collapsible ? M.Collapsible.getInstance(collapsible) : null;
                    if (!instance && collapsible) {
                        instance = M.Collapsible.init(collapsible);
                    }
                    if (instance) {
                        instance.open(0);
                    }
                }

                rows.forEach(function (row) {
                    var matches = !query || row.getAttribute('data-search').indexOf(query) !== -1;
                    row.hidden = !matches;
                    if (matches) {
                        visible++;
                    }
                });

                if (empty) {
                    empty.hidden = visible !== 0;
                }
            });
        });
    </script>
</div>
