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
		<div class="card grey darken-1">
			<div class="card-content">
				<?php

				$id = request_positive_int('id');
				$prueba_id = request_positive_int('prueba');
				$jinete_licencia = preg_replace('/[^0-9A-Za-z_-]/', '', (string)($_GET['jinete'] ?? ''));
				$caballo_licencia = preg_replace('/[^0-9A-Za-z_-]/', '', (string)($_GET['caballo'] ?? ''));

				if ($id !== null && $prueba_id !== null && $jinete_licencia !== '' && $caballo_licencia !== '') {
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
						$resultado = null;

						$resultados_datos = $reprises["resultados"]["datos"];
						foreach ($resultados_datos as $item) {
							if (trim($item['jinete_licencia']) === trim($jinete_licencia) && trim($item['caballo_licencia']) === trim($caballo_licencia)) {
								$resultado = $item;
								break;
							}
						}

						if ($resultado == null) {
							echo '<div class="status-message info" role="status">No se encontró un resultado para el jinete y caballo seleccionados.</div>';
						} else {
							$concurso = $reprises["concurso"];
							$jueces = $reprises["jueces"];
							$delegado = $reprises["delegado"];
							$potros = $resultado["potros"];
							$infantiles = $resultado["infantiles"];
							$siete_annos = $resultado["siete_annos"];
							$movimientos = $resultado["movimientos"];
							$movimientos_cabecera = $resultado["movimientos"]["cabecera"];
							$movimientos_notas = $resultado["movimientos"]["notas"];
							$movimientos_notas_conjunto = $resultado["movimientos"]["notas_conjunto"];
							$errores = $resultado["errores"];
							$errores_vaquera = $resultado["errores_vaquera"];
							$observaciones = $resultado["observaciones"];
							$jueces_notas = $resultado["notas"];

							//Datos
							echo "<div class=\"section\">";
							echo "<div class=\"row\">";
							echo "<div class=\"col s12\">";
							echo "<table class=\"tabla-movil\">";
							echo "<tr>";
							echo "<td>" . escape_html($concurso["nombre"]) . "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>" . escape_html($concurso["prueba"]) . " (" . escape_html($concurso["categoria"]) . ")" . "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>" . escape_html($resultado["jinete_nombre"]) . " (" . escape_html($resultado["jinete_licencia"]) . ")" . "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>" . escape_html($resultado["caballo_nombre"]) . " (" . escape_html($resultado["caballo_licencia"]) . ")" . "</td>";
							echo "</tr>";
							echo "</table>";
							echo "</div>";
							echo "</div>";
							echo "</div>";

							//Jueces
							echo "<div class=\"divider\"></div>";
							echo "<div class=\"section\">";
							echo "<div class=\"row\">";
							echo "<div class=\"col s12\">";
							echo "<h5 class=\"detalle-seccion-titulo\">Resumen y jueces</h5>";
							echo "<table class=\"tabla-movil tabla-jueces-individual\">";
							echo "<colgroup><col class=\"juez-nombre-col\"><col class=\"juez-nota-col\"></colgroup>";
							echo "<tr>";
							echo "<th> PUESTO </th>";
							echo "<th class=\"center-align\">" . escape_html($resultado["puesto"]) . "</th>";
							echo "</tr>";
							echo "<tr>";
							echo "<th> TOTAL </th>";
							echo "<th class=\"center-align\">" . number_format(bcdiv($resultado["total"], 1, 3), 3, ',', '.') . "</th>";
							echo "</tr>";
							if ($concurso["infantiles"] == 1) {
								echo "<tr>";
								echo "<td> TÉCNICA </td>";
								echo "<td class=\"center-align\">" . number_format(bcdiv($resultado["infantiles"]["tecnica_nota"], 1, 3), 3, ',', '.') . "<br>(" . $resultado["infantiles"]["tecnica_pos"] . ")</td>";
								echo "</tr>";
								echo "<tr>";
								echo "<td> CALIDAD </td>";
								echo "<td class=\"center-align\">" . number_format(bcdiv($resultado["infantiles"]["calidad_nota"], 1, 3), 3, ',', '.') . "<br>(" . $resultado["infantiles"]["calidad_pos"] . ")</td>";
								echo "</tr>";
							} else if ($concurso["siete_annos"] == 1) {
								echo "<tr>";
								echo "<td> TÉCNICA </td>";
								echo "<td class=\"center-align\">" . number_format(bcdiv($resultado["siete_annos"]["tecnica_nota"], 1, 3), 3, ',', '.') . "<br>(" . $resultado["siete_annos"]["tecnica_pos"] . ")</td>";
								echo "</tr>";
								echo "<tr>";
								echo "<td> CALIDAD </td>";
								echo "<td class=\"center-align\">" . number_format(bcdiv($resultado["siete_annos"]["calidad_nota"], 1, 3), 3, ',', '.') . "<br>(" . $resultado["siete_annos"]["calidad_pos"] . ")</td>";
								echo "</tr>";
							} else {
								foreach ($jueces as $juez) {
									if ($juez["nombre"] != "") {
										foreach ($jueces_notas as $juez_nota) {
											if (($juez_nota["juez"] != "") && ($juez["licencia"] == $juez_nota["licencia"])) {
												echo "<tr>";
												echo "<td>";
												echo "<p>";
												echo escape_html($juez["nombre"]) . "<br>" . escape_html(str_replace(":", "", $juez_nota["juez"]));
												echo "</p>";
												echo "</td>";
												echo "<td class=\"center-align\">";
												echo "<p>";
												echo number_format(bcdiv($juez_nota["nota"], 1, 3), 3, ',', '.') . "<br>(" . escape_html($juez_nota["pos"]) . ")";
												echo "</p>";
												echo "</td>";
												echo "</tr>";
											}
										}
									}
								}
							}

							echo "</table>";
							echo "</div>";
							echo "</div>";
							echo "</div>";

							//Notas
							echo "<div class=\"divider\"></div>";
							echo "<div class=\"section\">";
							echo "<div class=\"row\">";
							echo "<div class=\"col s12\">";
							echo "<h5 class=\"detalle-seccion-titulo\">Notas de los movimientos</h5>";
							echo "<div class=\"tabla-movil-wrapper\">";
							echo "<table class=\"tabla-movil tabla-notas-individual\">";

							//cabecera
							echo  "<thead><tr>";
							foreach ($movimientos_cabecera as $cabecera) {
								if (!str_contains($cabecera["nombre"], "-") && (!empty($cabecera["nombre"]))) {
									echo "<th class=\"center-align\">" . $cabecera["nombre"] . "</th>";
								}
							}
							echo  "</tr></thead>";

							//datos
							foreach ($movimientos_notas as $movimiento) {
								echo "<tr>";

								echo "<td class=\"center-align\">";
								echo $movimiento["orden"];
								if ($movimiento["coeficiente"] > 1) {
									echo "<sup>(x" . $movimiento["coeficiente"] . ")</sup>";
								}
								echo "</td>";
								echo "<td>" . $movimiento["texto"] . "</td>";

								if ($movimiento["nota_juez_1"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_1"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_2"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_2"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_3"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_3"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_4"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_4"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_5"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_5"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}
								echo "</tr>";
							}

							//Separador entre notas/nota de calidad/notas de conjunto
							echo "<tr><td></td></tr>";

							if (($concurso["infantiles"] == 1) || ($concurso["siete_annos"] == 1)) {
								echo "<tr>";
								echo "<th colspan=\"5\" class=\"center-align\"> CALIDAD </th>";
								echo "</tr>";
							}

							//notas de conjunto
							foreach ($movimientos_notas_conjunto as $movimiento) {
								echo "<tr>";

								echo "<td class=\"center-align\">";
								echo $movimiento["orden"];
								if ($movimiento["coeficiente"] > 1) {
									echo "<sup>(x" . $movimiento["coeficiente"] . ")</sup>";
								}
								echo "</td>";
								echo "<td>" . $movimiento["texto"] . "</td>";

								if ($movimiento["nota_juez_1"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_1"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_2"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_2"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_3"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_3"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_4"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_4"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}

								if ($movimiento["nota_juez_5"] >= 0) {
									//Redondeamos al tercer número (bcdiv) y formatemos (number_format) en español (. => miles y , => decimales)
									echo "<td class=\"center-align\">";
									echo number_format(bcdiv($movimiento["nota_juez_5"], 1, 1), 1, ',', '.');
									echo  "</td>";
								}
								echo "</tr>";
							}
							echo "</table>";

							//Separador para los errores
							echo "<table>";
							echo "<tr><td></td></tr>";
							echo "<tr>";
							echo "<th colspan=\"5\" class=\"center-align\"> ERRORES </th>";
							echo "</tr>";
							echo "</table>";

							//errores
							if ($concurso["vaquera"] == 0) {
								echo "<ul class=\"collection left-align\" style=\"border: none;\">";
								echo "<li class=\"collection-item transparent\" style=\"border: none;\">1<sup>er</sup> Error de recorrido: ";
								if (is_numeric($errores["error_recorrido_1"])) {
									echo number_format(bcdiv($errores["error_recorrido_1"], 1, 1), 1, ',', '.');
								} else {
									echo $errores["error_recorrido_1"];
								}
								echo "</li>";

								echo "<li class=\"collection-item transparent\" style=\"border: none;\">2º Error de reccoriddo: ";
								if (is_numeric($errores["error_recorrido_2"])) {
									echo number_format(bcdiv($errores["error_recorrido_2"], 1, 1), 1, ',', '.');
								} else {
									echo $errores["error_recorrido_2"];
								}
								echo "</li>";

								echo "<li class=\"collection-item transparent\" style=\"border: none;\">3º Error de recorrido: ";
								if (is_numeric($errores["error_recorrido_3"])) {
									echo number_format(bcdiv($errores["error_recorrido_3"], 1, 1), 1, ',', '.');
								} else {
									echo $errores["error_recorrido_3"];
								}

								echo "</li>";
								echo "<li class=\"collection-item transparent\" style=\"border: none;\">Otros errores: ";
								if (is_numeric($errores["otros_errores"])) {
									echo number_format(bcdiv($errores["otros_errores"], 1, 1), 1, ',', '.');
								} else {
									echo $errores["otros_errores"];
								}

								echo "</li>";
								echo "</ul>";
							} else {
								echo "<ul class=\"collection left-align\" style=\"border: none;\">";

								foreach ($errores_vaquera as $error) {
									if (strlen(trim($error["texto"])) > 0) {
										echo "<li class=\"collection-item transparent\" style=\"border: none;\">" . $error["texto"] . ": ";
										if (is_numeric($error["error_juez_1"])) {
											echo number_format(bcdiv($error["error_juez_1"], 1, 1), 1, ',', '.');
										} else {
											echo $error["error_juez_1"];
										}
										echo "</li>";
									}
								}

								echo "</li>";
								echo "</ul>";
							}

							echo "</div>";
							echo "</div>";
							echo "</div>";
							echo "</div>";
						}
					}

					echo "</div>";

					//back button.
					echo "<div class=\"card-action detalle-acciones\">";
					echo "<a href=\"?c=Resultado&a=Resultado&id=" . $id . ($this->m_model->GetJornadaId() !== null ? '&jornada=' . rawurlencode($this->m_model->GetJornadaId()) : '') . "&prueba=" . $prueba_id . "\" aria-label=\"Volver a resultados de la prueba\"><i class=\"large material-icons\" aria-hidden=\"true\">arrow_back</i></a>";
					echo "</div>";

					echo "</div>";
					echo "</div>";
				}

				?>
			</div>
		</div>
	</div>
</div>