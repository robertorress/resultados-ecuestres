<?php $activeSection = 'resultados'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<style>
    .tabla-movil-equipos,
    .tabla-equipo-detalle {
        width: 100%;
    }

    .tabla-movil-equipos th:nth-child(1),
    .tabla-movil-equipos td:nth-child(1) {
        width: 12%;
    }

    .tabla-movil-equipos th:nth-child(2),
    .tabla-movil-equipos td:nth-child(2) {
        width: 63%;
        word-break: break-word;
    }

    .tabla-movil-equipos th:nth-child(3),
    .tabla-movil-equipos td:nth-child(3) {
        width: 25%;
        text-align: right;
        white-space: nowrap;
    }

    .resultado-equipo-detalle td {
        padding-top: 4px;
        padding-bottom: 4px;
        font-size: 0.92rem;
    }

    .resultado-equipo-detalle .detalle-texto {
        padding-left: 1.5rem;
    }

    .tabla-equipo-detalle {
        margin: 0.5rem 0 0 0;
        table-layout: auto;
    }

    .tabla-equipo-detalle th,
    .tabla-equipo-detalle td {
        font-size: 0.9rem;
        padding: 6px 8px;
        vertical-align: top;
    }

    .tabla-equipo-detalle th {
        font-weight: 400;
    }

    .puntos-no-sumados {
        text-decoration: line-through;
        opacity: 0.85;
    }

    .tabla-equipo-detalle th:nth-child(2),
    .tabla-equipo-detalle td:nth-child(2),
    .tabla-equipo-detalle th:nth-child(4),
    .tabla-equipo-detalle td:nth-child(4),
    .tabla-equipo-detalle th:nth-child(6),
    .tabla-equipo-detalle td:nth-child(6) {
        white-space: nowrap;
    }

    .tabla-equipo-detalle th:nth-child(1),
    .tabla-equipo-detalle td:nth-child(1),
    .tabla-equipo-detalle th:nth-child(3),
    .tabla-equipo-detalle td:nth-child(3),
    .tabla-equipo-detalle th:nth-child(5),
    .tabla-equipo-detalle td:nth-child(5) {
        word-break: break-word;
    }

    .tabla-equipo-detalle th:nth-child(1),
    .tabla-equipo-detalle td:nth-child(1) {
        width: 24%;
    }

    .tabla-equipo-detalle th:nth-child(2),
    .tabla-equipo-detalle td:nth-child(2) {
        width: 12%;
    }

    .tabla-equipo-detalle th:nth-child(3),
    .tabla-equipo-detalle td:nth-child(3) {
        width: 24%;
    }

    .tabla-equipo-detalle th:nth-child(4),
    .tabla-equipo-detalle td:nth-child(4) {
        width: 12%;
    }

    .tabla-equipo-detalle th:nth-child(5),
    .tabla-equipo-detalle td:nth-child(5) {
        width: 18%;
    }

    .tabla-equipo-detalle th:nth-child(6),
    .tabla-equipo-detalle td:nth-child(6) {
        width: 10%;
    }

    @media only screen and (max-width: 600px) and (orientation: portrait) {
        .resultado-equipo-detalle {
            display: none;
        }

        .tabla-movil-equipos th,
        .tabla-movil-equipos td {
            font-size: 11px !important;
            padding: 5px 2px !important;
        }

        .tabla-movil-equipos th:nth-child(1),
        .tabla-movil-equipos td:nth-child(1) {
            width: 10% !important;
        }

        .tabla-movil-equipos th:nth-child(2),
        .tabla-movil-equipos td:nth-child(2) {
            width: 70% !important;
        }

        .tabla-movil-equipos th:nth-child(3),
        .tabla-movil-equipos td:nth-child(3) {
            width: 20% !important;
        }
    }

    @media only screen and (max-width: 900px) and (orientation: landscape) {
        .tabla-equipo-detalle th,
        .tabla-equipo-detalle td {
            font-size: 0.7rem;
            padding: 3px 4px;
        }
    }
</style>

<div class="row">
    <div class="col s12 center">
        <div class="card grey darken-1">
            <div class="card-content">
                <span class="card-title white-text">RESULTADOS EQUIPOS</span>
                <span class="card-title white-text">(PROVISIONALES)</span>

                <div class="row">
                    <div class="col s12 center">
                        <div class="card-panel blue-grey lighten-1">
                            <?php
                            $id = request_positive_int('id');

                            if ($this->m_model->ShouldShowResultadosEquipos() !== true) {
                                echo '<div class="status-message info" role="status"><i class="material-icons" aria-hidden="true">info</i>';
                                echo 'Este concurso no tiene habilitada la clasificación por equipos.';
                                echo '</div>';
                            } else {
                                try {
                                    $clasificacion = $this->m_model->getClasificacionEquipos();
                                    $equipos = $clasificacion['clasificacion'];
                                    $equiposInvalidos = $clasificacion['invalidos'];

                                    if (count($equipos) === 0) {
                                        echo '<div class="status-message info" role="status"><i class="material-icons" aria-hidden="true">info</i>';
                                        echo 'No hay equipos válidos para calcular la clasificación.';
                                        echo '</div>';
                                    } else {
                                        echo "<div>";
                                        echo "<table class=\"highlight tabla-movil-equipos\">";
                                        echo "<thead><tr>";
                                        echo "<th>PUESTO</th>";
                                        echo "<th>EQUIPO</th>";
                                        echo "<th class=\"right-align\">PUNTOS TOTALES</th>";
                                        echo "</tr></thead>";
                                        echo "<tbody>";

                                        $position = 1;
                                        foreach ($equipos as $equipo) {
                                            echo "<tr>";
                                            echo "<td>" . $position . "</td>";
                                            echo "<td><strong>" . escape_html($equipo['nombre']) . "</strong></td>";
                                            echo "<td class=\"right-align\"><strong>" . format_score($equipo['puntos_totales']) . "</strong></td>";
                                            echo "</tr>";

                                            echo "<tr class=\"resultado-equipo-detalle\">";
                                            echo "<td></td>";
                                            echo "<td colspan=\"2\">";
                                            echo "<table class=\"highlight tabla-equipo-detalle\">";
                                            echo "<thead><tr>";
                                            echo "<th>JINETE</th>";
                                            echo "<th>LICENCIA JINETE</th>";
                                            echo "<th>CABALLO</th>";
                                            echo "<th>LICENCIA CABALLO</th>";
                                            echo "<th>REPRISE</th>";
                                            echo "<th class=\"right-align\">PUNTOS</th>";
                                            echo "</tr></thead>";
                                            echo "<tbody>";

                                            foreach ($equipo['miembros'] as $miembro) {
                                                if (count($miembro['resultados']) === 0) {
                                                    $puntosHtml = format_score(0);
                                                    if (isset($miembro['sumado']) && $miembro['sumado'] !== true) {
                                                        $puntosHtml = '<span class="puntos-no-sumados">' . $puntosHtml . '</span>';
                                                    }
                                                    echo "<tr>";
                                                    echo "<td>" . escape_html($miembro['jinete']) . "</td>";
                                                    echo "<td>" . escape_html($miembro['jinete_licencia']) . "</td>";
                                                    echo "<td>" . escape_html($miembro['caballo']) . "</td>";
                                                    echo "<td>" . escape_html($miembro['caballo_licencia']) . "</td>";
                                                    echo "<td>-</td>";
                                                    echo "<td class=\"right-align\">" . $puntosHtml . "</td>";
                                                    echo "</tr>";
                                                    continue;
                                                }

                                                foreach ($miembro['resultados'] as $resultado) {
                                                    $puntosHtml = format_score($resultado['puntos']);
                                                    if (isset($miembro['sumado']) && $miembro['sumado'] !== true) {
                                                        $puntosHtml = '<span class="puntos-no-sumados">' . $puntosHtml . '</span>';
                                                    }
                                                    echo "<tr>";
                                                    echo "<td>" . escape_html($miembro['jinete']) . "</td>";
                                                    echo "<td>" . escape_html($miembro['jinete_licencia']) . "</td>";
                                                    echo "<td>" . escape_html($miembro['caballo']) . "</td>";
                                                    echo "<td>" . escape_html($miembro['caballo_licencia']) . "</td>";
                                                    echo "<td>" . escape_html($resultado['prueba']) . "</td>";
                                                    echo "<td class=\"right-align\">" . $puntosHtml . "</td>";
                                                    echo "</tr>";
                                                }
                                            }

                                            echo "</tbody>";
                                            echo "</table>";
                                            echo "</td>";
                                            echo "</tr>";

                                            $position++;
                                        }

                                        echo "</tbody>";
                                        echo "</table>";
                                        echo "</div>";
                                        echo "<div class=\"section left-align\">";
                                        echo "<p><strong>Leyenda:</strong></p>";
                                        echo "<p>1) Se usa la mejor nota de cada binomio entre todas las pruebas.</p>";
                                        echo "<p>2) En equipos de cuatro binomios solo suman tres; el no sumado se muestra <span class=\"puntos-no-sumados\">tachado</span>.</p>";
										echo "<p>3) Se tienen en cuenta las tres mejores notas.</span>.</p>";
                                        echo "</div>";

                                    }

                                    if (count($equiposInvalidos) > 0) {
                                        foreach ($equiposInvalidos as $equipoInvalido) {
                                            echo '<div class="status-message warning left-align" role="alert"><i class="material-icons" aria-hidden="true">warning</i>';
                                            echo '<strong>' . escape_html($equipoInvalido['nombre']) . ':</strong> ';
                                            echo escape_html(implode(' ', $equipoInvalido['errores']));
                                            echo '</div>';
                                        }
                                    }
                                } catch (Exception $exception) {
                                    echo '<div class="status-message error" role="alert"><i class="material-icons" aria-hidden="true">error</i>';
                                    echo escape_html($exception->getMessage());
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            echo "<div class=\"card-action\">";
            echo "<a href=\"?c=Prueba&a=MostrarPrueba&id=" . $id . ($this->m_model->GetJornadaId() !== null ? '&jornada=' . rawurlencode($this->m_model->GetJornadaId()) : '') . "\" aria-label=\"Volver al concurso\"><i class=\"large material-icons\" aria-hidden=\"true\">arrow_back</i></a>";
            echo "</div>";
            echo "<p class=\"show-on-small hide-on-med-and-up\">Ponga el dispositivo en horizontal para ver más detalles</p>";
            ?>
        </div>
    </div>
</div>
