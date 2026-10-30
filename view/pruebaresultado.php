<?php require_once 'database/resultadojson.php'; ?>
<?php $activeSection = 'resultados'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<style>
	.material-icons {
		translate: no !important;
	}

	@media only screen and (max-width: 600px) {
		table.tabla-movil th,
		table.tabla-movil td {
			font-size: 11px !important;
			padding: 5px 2px !important;
		}

		table.tabla-movil th:nth-child(1),
		table.tabla-movil td:nth-child(1) {
			width: 10% !important;
		}

		table.tabla-movil th:nth-child(2),
		table.tabla-movil td:nth-child(2) {
			width: 75% !important;
			word-break: break-word;
		}

		table.tabla-movil th:nth-child(3),
		table.tabla-movil td:nth-child(3) {
			width: 15% !important;
			text-align: right !important;
		}
	}
</style>

<div class="row">
	<div class="col s12 center">
        <?php
        $fechaInicio = trim((string)$this->m_model->GetFechaInicio());
        $fechaFin = trim((string)$this->m_model->GetFechaFin());
        $fechaConcurso = $fechaInicio === $fechaFin || $fechaFin === ''
            ? $fechaInicio
            : $fechaInicio . ' - ' . $fechaFin;
        $lugar = $this->m_model->GetUbicacionNombre();
        $estadoConcurso = $this->m_model->GetEstadoConcurso();
        $etiquetasEstado = [
            'proximo' => 'PRÓXIMO',
            'en-curso' => 'EN CURSO',
            'finalizado' => 'FINALIZADO',
        ];
        ?>
        <section class="concurso-portada" aria-labelledby="titulo-concurso">
            <div class="concurso-portada-decoracion" aria-hidden="true"></div>
            <div class="concurso-portada-contenido">
                <div class="concurso-portada-logo">
                    <img
                        class="hipica-logo"
                        loading="lazy"
                        decoding="async"
                        src="<?php echo escape_html($this->m_model->GetLogo()); ?>"
                        alt="Logo de <?php echo escape_html($this->m_model->GetConcurso()); ?>"
                    />
                </div>
                <div class="concurso-portada-datos">
                    <h1 id="titulo-concurso"><?php echo escape_html($this->m_model->GetConcurso()); ?></h1>
                    <div class="concurso-portada-meta">
                        <?php if ($fechaConcurso !== ''): ?>
                            <span>
                                <i class="material-icons" aria-hidden="true">event</i>
                                <?php echo escape_html($fechaConcurso); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($lugar !== null && trim((string)$lugar) !== ''): ?>
                            <span>
                                <i class="material-icons" aria-hidden="true">location_on</i>
                                <?php echo escape_html($lugar); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="concurso-estado concurso-estado-<?php echo escape_html($estadoConcurso); ?>">
                        <?php echo escape_html($etiquetasEstado[$estadoConcurso] ?? 'CONCURSO'); ?>
                    </span>
                </div>
            </div>
        </section>
		<br>
		<div class="card grey darken-1">
			<div class="card-content">
				<?php

				$id = request_positive_int('id');
				$prueba_id = request_positive_int('prueba');
				$jornadaQuery = $this->m_model->GetJornadaId() !== null
					? '&jornada=' . rawurlencode($this->m_model->GetJornadaId())
					: '';
				if ($id !== null && $prueba_id !== null) {
					$prueba = $this->m_model->GetPrueba($prueba_id);
					$json_file = false;
					$json_error = null;

					try {
						$reprises = CResultadoJson::load((string)$prueba->link);
						$json_file = true;
					} catch (Exception $exception) {
						$json_error = $exception->getMessage();
					}

					echo "<span class=\"card-title white-text\">RESULTADOS PROVISIONALES</span>";
					echo "<span class=\"card-title white-text\">\"" . escape_html($prueba->titulo) . "\"</span>";

					echo "<div class=\"row\">";
					echo	"<div class=\"col s12 center\">";

					echo "<div class=\"card-panel blue-grey lighten-1\">";

					//Vemos si se ha cargado bien el archivo
					if ($json_file == false) {
						echo '<div class="status-message error" role="alert"><i class="material-icons" aria-hidden="true">error</i>';
						echo htmlspecialchars($json_error ?? 'No se pudo cargar el archivo de resultados', ENT_QUOTES, 'UTF-8') . '</div>';
					} else {


						$concurso = $reprises["concurso"];
						$jueces = $reprises["jueces"];
						$delegado = $reprises["delegado"];
						$resultados_cabecera = $reprises["resultados"]["cabecera"];
						$resultados_datos = $reprises["resultados"]["datos"];


						//resultados
						echo "<div class=\"resultados-controles\">";
						echo "<div class=\"input-field\">";
						echo "<i class=\"material-icons prefix\">search</i>";
						echo "<input id=\"buscar-resultados\" type=\"search\" autocomplete=\"off\" aria-label=\"Buscar atleta o caballo\">";
						echo "<label for=\"buscar-resultados\">Buscar atleta o caballo</label>";
						echo "</div>";
						echo "</div>";
						echo "<div class=\"tabla-movil-wrapper\">";
						echo "<table class=\"tabla-movil tabla-resultados\">";

						//Inicio cabecera
						echo  "<thead><tr>";
						echo "<th></th>";
						echo "<th class=\"center-align\">ATLETA<br>CABALLO</th>";
						if ($concurso["potros"] == 1) {
							echo "<th class=\"center-align hide-on-small-only\">TROTE</th>";
							echo "<th class=\"center-align hide-on-small-only\">PASO</th>";
							echo "<th class=\"center-align hide-on-small-only\">GALOPE</th>";
							echo "<th class=\"center-align hide-on-small-only\">SUMISIÓN</th>";
							echo "<th class=\"center-align hide-on-small-only\">EXP. FUTURO</th>";
							echo "<th class=\"center-align hide-on-small-only\">P</th>";
							echo "<th class=\"center-align\">TOTAL</th>";
						} else if ($concurso["infantiles"] == 1) {
							echo "<th class=\"center-align hide-on-small-only\">POSICIÓN</th>";
							echo "<th class=\"center-align hide-on-small-only\">AYUDAS</th>";
							echo "<th class=\"center-align hide-on-small-only\">PRECISIÓN</th>";
							echo "<th class=\"center-align hide-on-small-only\">IMP. GENERAL</th>";
							echo "<th class=\"center-align hide-on-small-only\">CALIDAD</th>";
							echo "<th class=\"center-align hide-on-small-only\">TÉCNICA</th>";
							echo "<th class=\"center-align hide-on-small-only\">P.</th>";
							echo "<th class=\"center-align\">TOTAL</th>";
						} else if ($concurso["siete_annos"] == 1) {
							echo "<th class=\"center-align hide-on-small-only\">TROTE</th>";
							echo "<th class=\"center-align hide-on-small-only\">PASO</th>";
							echo "<th class=\"center-align hide-on-small-only\">GALOPE</th>";
							echo "<th class=\"center-align hide-on-small-only\">SUMISIÓN</th>";
							echo "<th class=\"center-align hide-on-small-only\">EXP. FUTURO</th>";
							echo "<th class=\"center-align hide-on-small-only\">CALIDAD</th>";
							echo "<th class=\"center-align hide-on-small-only\">TÉCNICA</th>";
							echo "<th class=\"center-align hide-on-small-only\">P.</th>";
							echo "<th class=\"center-align\">TOTAL</th>";
						} else {
							foreach ($resultados_cabecera as $cabecera) {
								//Si no hay juez, hay un "-" en el campo
								if ((str_contains($cabecera["nombre"], "JUEZ")) || (str_contains($cabecera["nombre"], "PRESIDENTE")) || (str_contains($cabecera["nombre"], "VOCAL"))) {
									echo "<th class=\"center-align hide-on-small-only\">" . $cabecera["nombre"] . "</th>";
								}
							}
							echo "<th class=\"center-align\">TOTAL</th>";
						}
						//Fin cabecera
						echo  "</tr></thead>";

						//datos
						echo  "<tbody>";
						foreach ($resultados_datos as $dato) {
							//notas
							$textoBusqueda = strtolower((string)$dato["jinete_nombre"] . ' ' . (string)$dato["caballo_nombre"]);
							echo "<tr class=\"resultado-fila\" data-search=\"" . escape_html($textoBusqueda) . "\">";
							echo "<td class=\"center-align\">" . $dato["puesto"] . "</td>";
							echo "<td>";
							echo "<p>";
							echo $dato["jinete_nombre"];
							echo "<br>";
							echo $dato["caballo_nombre"];
							echo "</p>";
							echo "</td>";

							//Es un reprise normal
							if (($concurso["potros"] == 0) && ($concurso["infantiles"] == 0) && ($concurso["siete_annos"] == 0)) {
								foreach ($dato["notas"] as $nota) {
									//Si no hay pos, entonces no hay nota de juez, está vacío.
									if (is_numeric($nota["pos"]) && ($nota["pos"] > 0)) {
										//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
										echo "<td class=\"center-align hide-on-small-only\">";
										echo number_format(bcdiv($nota["nota"], 1, 3), 3, ',', '.');
										echo "<br>";
										echo "(" . $nota["pos"] . ")";
										echo  "</td>";
									}
								}

								//Total con tres decimales (bcdiv) y en formato español (number_format, "." => miles y "," => decimales)
								echo "<td class=\"center-align\">";
								if (is_numeric($dato["puesto"]) && ($dato["puesto"] > 0)) {
									echo "<a class = \"puntuacion-final waves-effect waves-teal btn-small blue accent-1\" href = \"?c=ResultadoIndividual&a=ResultadoIndividual&id=";
									echo $id . $jornadaQuery . "&prueba=" . $prueba_id . "&jinete=" . $dato["jinete_licencia"] . "&caballo=" . $dato["caballo_licencia"] . "\">";
									echo format_score($dato["total"]);
									echo "</a>";
								}
							} else if ($concurso["potros"] == 1) {
								if (is_numeric($dato["puesto"]) && ($dato["puesto"] > 0)) {
									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["potros"]["trote_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["potros"]["trote_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["potros"]["paso_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["potros"]["paso_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["potros"]["galope_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["potros"]["galope_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["potros"]["sumision_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["potros"]["sumision_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["potros"]["expectativa_futuro_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["potros"]["expectativa_futuro_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["potros"]["errores"], 1, 1), 1, ',', '.');
									echo  "</td>";

									//Total
									echo "<td class=\"center-align\">";
									echo "<span class=\"puntuacion-final\">" . format_score($dato["potros"]["total"]) . "</span>";
									echo  "</td>";
								}
							} else if ($concurso["infantiles"] == 1) {
								if (is_numeric($dato["puesto"]) && ($dato["puesto"] > 0)) {
									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["jinete_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["infantiles"]["jinete_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["ayudas_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["infantiles"]["ayudas_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["precision_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["infantiles"]["precision_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["impresion_general_nota"], 1, 1), 1, ',', '.');
									echo "<br>";
									echo "(" . $dato["infantiles"]["impresion_general_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["calidad_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["infantiles"]["calidad_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["tecnica_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["infantiles"]["tecnica_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["infantiles"]["errores"], 1, 1), 1, ',', '.');
									echo  "</td>";

									//Total
									echo "<td class=\"center-align\">";
									echo "<a class = \"puntuacion-final waves-effect waves-teal btn-small blue accent-1\" href = \"?c=ResultadoIndividual&a=ResultadoIndividual&id=";
									echo $id . $jornadaQuery . "&prueba=" . $prueba_id . "&jinete=" . $dato["jinete_licencia"] . "&caballo=" . $dato["caballo_licencia"] . "\">";
									echo format_score($dato["infantiles"]["total"]);
									echo "</a>";
								}
							} else if ($concurso["siete_annos"] == 1) {
								if (is_numeric($dato["puesto"]) && ($dato["puesto"] > 0)) {
									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["trote_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["trote_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["paso_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["paso_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["galope_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["galope_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["sumision_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["sumision_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["expectativa_futuro_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["expectativa_futuro_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["calidad_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["calidad_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["tecnica_nota"], 1, 3), 3, ',', '.');
									echo "<br>";
									echo "(" . $dato["siete_annos"]["tecnica_pos"] . ")";
									echo  "</td>";

									echo "<td class=\"center-align hide-on-small-only\">";
									echo number_format(bcdiv($dato["siete_annos"]["errores"], 1, 1), 1, ',', '.');
									echo  "</td>";

									//Total
									echo "<td class=\"center-align\">";
									echo "<a class = \"puntuacion-final waves-effect waves-teal btn-small blue accent-1\" href = \"?c=ResultadoIndividual&a=ResultadoIndividual&id=";
									echo $id . $jornadaQuery . "&prueba=" . $prueba_id . "&jinete=" . $dato["jinete_licencia"] . "&caballo=" . $dato["caballo_licencia"] . "\">";
									echo format_score($dato["siete_annos"]["total"]);
									echo "</a>";
								}
							}

							echo "</td>";
							echo "</tr>";
						}
						echo  "</tbody>";

						echo "</table>";
						echo "<p id=\"sin-resultados\" class=\"center-align\" hidden>No hay resultados que coincidan con la búsqueda.</p>";
						echo "</div>";

						echo "<div class=\"divider\"></div>";
						echo "<div class=\"section\">";
						echo "<div class=\"row\">";
						echo "<div class=\"col s12\">";
						echo "<h5>JUECES</h5>";
						echo "<ul class=\"collection left-align\" style=\"border: none;\">";
						foreach ($jueces as $juez) {
							if ($juez["nombre"] != "") {
								echo "<li class=\"collection-item avatar transparent\" style=\"border: none;\">";
								echo "<i class=\"material-icons circle\">person</i>";
								echo "<span class=\"title\">";
								echo "<p>" . $juez["nombre"] . "</p>";
								echo "</span>";
								echo "<p>" . str_replace(":", "", $juez["puesto"]) . "</p>";
								echo "</li>";
							}
						}
						echo "</ul>";
						echo "</div>";
						echo "<div class=\"col s12\">";
						echo "<h5>LEYENDA</h5>";
						echo "<ul class=\"collection left-align\" style=\"border: none;\">";
						echo "<li class=\"collection-item transparent\" style=\"border: none;\">";
						echo "<span class=\"title\">NOP</span>";
						echo "<p>NO PRESENTADO</p>";
						echo "</li>";
						echo "<li class=\"collection-item transparent\" style=\"border: none;\">";
						echo "<span class=\"title\">ELI</span>";
						echo "<p>ELIMINADO</p>";
						echo "</li>";
						echo "<li class=\"collection-item transparent\" style=\"border: none;\">";
						echo "<span class=\"title\">RET</span>";
						echo "<p>RETIRADO</p>";
						echo "</li>";
						echo "<li class=\"collection-item transparent\" style=\"border: none;\">";
						echo "<span class=\"title\">FDC</span>";
						echo "<p>FUERA DE CONCURSO</p>";
						echo "</li>";
						echo "<li class=\"collection-item transparent\" style=\"border: none;\">";
						echo "<span class=\"title\">DSC</span>";
						echo "<p>DESCALIFICADO</p>";
						echo "</li>";
						echo "</ul>";
						echo "</div>";
						echo "</div>";
						echo "</div>";

						echo "<p>Última actualización: " . $concurso["fecha_actualizacion"] . "</p>";
					}

					echo "</div>";

					//back button.
					echo "<div class=\"card-action\">";
					echo "<a href=\"?c=Prueba&a=MostrarPrueba&id=" . $id . $jornadaQuery . "\" aria-label=\"Volver al concurso\"><i class=\"large material-icons\" aria-hidden=\"true\">arrow_back</i></a>";
					echo "</div>";

					echo "<p class=\"show-on-small hide-on-med-and-up\">Ponga el dispositivo en horizontal para ver más detalles</p>";

					echo "</div>";
					echo "</div>";
				}

				?>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function () {
					var input = document.getElementById('buscar-resultados');
					var table = document.querySelector('.tabla-resultados');
					var empty = document.getElementById('sin-resultados');

					if (!input || !table) {
						return;
					}

					var body = table.tBodies[0];
					var rows = Array.from(body.querySelectorAll('.resultado-fila'));

					function updateResults() {
						var query = input.value.trim().toLowerCase();
						var visible = 0;
						input.parentElement.classList.toggle('has-value', input.value.trim() !== '');
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
					}

					input.addEventListener('input', updateResults);
				});
			</script>
		</div>
	</div>
</div>