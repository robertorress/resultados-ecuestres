<nav aria-label="Navegación principal">
    <div class="nav-wrapper blue darken-4">

        <ul class="brand-logo center hide-on-med-and-down">
            <li><?php /*echo escape_html($this->m_model->GetConcurso())*/ ?></li>
        </ul>
		
        <ul class="right hide-on-med-and-down">
            <li><a href="index.php"><i class="left large material-icons notranslate">home</i>INICIO</a></li>
            <li class="active">
                <?php
                echo "<a href=\"?c=Prueba&a=ListadoOrdenSalida&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">access_time</i>ORDEN DE SALIDA</a>";
                ?>
            </li>
            <li>
                <?php
                echo "<a href=\"?c=Prueba&a=ListadoResultado&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">visibility</i>RESULTADOS</a>";
                ?>
            </li>
        </ul>

        <ul class="right show-on-medium-and-down" style="display:none;padding-right:5%">
            <?php echo escape_html($this->m_model->GetConcurso()) ?>
        </ul>

        <ul id="nav-mobile" class="sidenav blue accent-1">
            <div class="user-view">
            </div>
            <li><a href="index.php"><i class="large material-icons notranslate">home</i>INICIO</a></li>
            <li class="active">
                <?php
                echo "<a href=\"?c=Prueba&a=ListadoOrdenSalida&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">access_time</i>ORDEN DE SALIDA</a>";
                ?>
            </li>
            <li>
                <?php
                echo "<a href=\"?c=Prueba&a=ListadoResultado&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">visibility</i>RESULTADOS</a>";
                ?>
            </li>
        </ul>

        <!-- Cambiamos la palabra 'menu' por su código Unicode universal &#xE5D2;-->
        <a href="#" data-target="nav-mobile" class="sidenav-trigger" aria-label="Abrir menú de navegación"><i class="material-icons notranslate">&#xE5D2;</i></a>

    </div>

</nav>

<div class="row">
    <div class="col s12 center">
        <div class="card grey darken-1">
            <div class="card-content white-text">
                <span class="card-title white-text">ORDEN DE SALIDA</span>
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
        </div>
    </div>
</div>