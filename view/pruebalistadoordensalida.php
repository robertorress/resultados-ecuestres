<?php $activeSection = 'orden'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<div class="row">
    <div class="col s12 center">
        <div class="card grey darken-1">
            <div class="card-content white-text">
                <span class="card-title white-text">ORDEN DE SALIDA</span>
                <?php $concursoId = $this->m_model->GetConcursoId(); ?>
                <p class="detalle-seccion-titulo"><?php echo escape_html($this->m_model->GetFecha()); ?></p>
                <p>
                <div class="row">
                    <div class="col s12 center">
                        <?php
                        $ordenesSalida = $this->m_model->GetOrdenSalidas();
                        if (count($ordenesSalida) === 0) {
                            echo '<div class="status-message info" role="status"><i class="material-icons" aria-hidden="true">info</i>No hay órdenes de salida disponibles.</div>';
                        }
                        foreach ($ordenesSalida as $ordensalida) {
                            echo "<div class=\"card-panel blue-grey lighten-1\">";
                            echo "<img loading=\"lazy\" decoding=\"async\" style=\"border:0;width:85%;height:auto\" src=\"" . escape_html($ordensalida) . "\" alt=\"ORDEN SALIDA\" />";
                            echo "</div>";
                        }

                        //Generamos el enlace al vídeo del orde de salida.
                        $sorteo = $this->m_model->GetSorteoOrdenSalida();
                        if ($sorteo && isset($sorteo) && trim((string)$sorteo) !== '') {

                            $url_video = $this->m_model->GetSorteoOrdenSalida();

                            // 1. Extraemos los parámetros de la URL
                            $id_video = youtube_video_id($url_video);

                            // No bloqueamos el renderizado haciendo una petición externa.
                            $video_disponible = !empty($id_video);

                            $url_embed = "https://www.youtube.com/embed/" . $id_video;

                            echo "<div class=\"card-panel blue-grey lighten-1\">";
                            echo "<h4>SORTEO DEL ORDEN DE SALIDA</h4>";

                            if ($video_disponible) {
                                echo "<div class=\"video-container\">";
                                // Corregido el cierre del iframe que tenía un "?" extra
                                echo "<iframe title=\"Sorteo del orden de salida\" loading=\"lazy\" src=\"" . htmlspecialchars($url_embed, ENT_QUOTES, 'UTF-8') . "\" allow=\"accelerometer; clipboard-write; encrypted-media; gyroscope;\" allowfullscreen></iframe>";
                                echo "</div>";
                            } else {
                                // MENSAJE DE ERROR: Si el video no carga o no existe
                                echo "<div class=\"card-panel red lighten-2 white-text\">";
                                echo "<i class=\"material-icons left\">error</i>";
                                echo "El video no está disponible en este momento o la URL es incorrecta.";
                                echo "</div>";

                                // Botón de respaldo por si el bloqueo es solo por el iframe
                                echo "<div class=\"center-align\" style=\"margin-top:20px;\">";
                                echo "<a class=\"waves-effect waves-light btn blue accent-1\" href=\"" . escape_html($url_video) . "\" target=\"_blank\" rel=\"noopener noreferrer\">VER EN YOUTUBE</a>";
                                echo "</div>";
                            }

                            echo "</div>";
                            echo "</div>";
                        }

                        ?>
                    </div>
                </div>
                </p>
            </div>
            <div class="card-action">
                <?php echo "<a href=\"?c=Prueba&a=MostrarPrueba&id=" . escape_html($concursoId) . "\" aria-label=\"Volver al concurso\"><i class=\"large material-icons\" aria-hidden=\"true\">arrow_back</i></a>"; ?>
            </div>
        </div>
    </div>
</div>