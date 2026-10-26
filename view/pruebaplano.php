<nav aria-label="Navegación principal">
    <div class="nav-wrapper blue darken-4">

        <ul class="brand-logo center hide-on-med-and-down">
            <li><?php /*echo escape_html($this->m_model->GetConcurso())*/ ?></li>
        </ul>

        <ul class="right hide-on-med-and-down">
            <li><a href="index.php"><i class="left large material-icons notranslate">home</i>INICIO</a></li>
            <li class="active">
                <?php
                echo "<a href=\"?c=Prueba&a=Plano&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">map</i>PLANO</a>";
                ?>
            </li>
            <li>
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
                echo "<a href=\"?c=Prueba&a=Plano&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">map</i>PLANO</a>";
                ?>
            </li>
            <li>
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
                <span class="card-title white-text">PLANO</span>
                <p>
                <div class="row">
                    <div class="col s12 center">
                        <?php
                        $planosDisponibles = [];
                        $planos = $this->m_model->GetPlanos();
                        $id = $this->m_model->GetConcursoId();

                        foreach ($planos as $plano) {
                            $rutaPlano = trim((string)$plano);
                            if ($rutaPlano === '') {
                                continue;
                            }

                            $planoNormalizado = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $rutaPlano), DIRECTORY_SEPARATOR);
                            $planoAbsoluto = dirname(__DIR__) . DIRECTORY_SEPARATOR . $planoNormalizado;
                            if (is_file($planoAbsoluto)) {
                                $planosDisponibles[] = $rutaPlano;
                            }
                        }

                        if (count($planosDisponibles) === 0) {
                            echo "<div class=\"card-panel blue-grey lighten-1\">";
                            echo "<h5>NO HAY PLANOS DISPONIBLES</h5>";
                            echo "<div class=\"center-align\" style=\"margin-top:20px;\">";
                            echo "<a href=\"?c=Prueba&a=MostrarPrueba&id=" . escape_html($id) . "\" aria-label=\"Volver al concurso\"><i class=\"large material-icons\" aria-hidden=\"true\">arrow_back</i></a>";
                            echo "</div>";
                            echo "</div>";
                        } else {
                            foreach ($planosDisponibles as $planoMostrado) {
                                echo "<div class=\"card-panel blue-grey lighten-1\">";
                                echo "<img loading=\"lazy\" decoding=\"async\" style=\"border:0;width:85%;height:auto\" src=\"" . escape_html($planoMostrado) . "\" alt=\"PLANO\" />";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                </p>
            </div>
        </div>
    </div>
</div>
