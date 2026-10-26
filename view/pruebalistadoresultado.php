<nav aria-label="Navegación principal">
    <div class="nav-wrapper blue darken-4">

        <ul class="brand-logo center hide-on-med-and-down">
            <li><?php /*echo escape_html($this->m_model->GetConcurso())*/ ?></li>
        </ul>

        <ul class="right hide-on-med-and-down">
            <li><a href="index.php"><i class="left large material-icons notranslate">home</i>INICIO</a></li>
            <li>
                <?php
                echo "<a href=\"?c=Prueba&a=ListadoOrdenSalida&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">access_time</i>ORDEN DE SALIDA</a>";
                ?>
            </li>
            <li class="active">
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
            <li>
                <?php
                echo "<a href=\"?c=Prueba&a=ListadoOrdenSalida&id=" . $this->m_model->GetConcursoId() . "\"><i class=\"left large material-icons notranslate\">access_time</i>ORDEN DE SALIDA</a>";
                ?>
            </li>
            <li class="active">
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
            <div class="card-content">
                <span class="card-title white-text">RESULTADOS PROVISIONALES</span>

                <div class="row">
                    <div class="col s12 center">
                        <div class="card-panel blue-grey lighten-1">
                            <?php
                            $pruebas = $this->m_model->GetPruebas();
                            $concursoId = $this->m_model->GetConcursoId();
                            require __DIR__ . '/partials/result-test-links.php';
                            ?>
                        </div>
                        <p>
                            Pulse sobre la prueba para ver los resultados provisionales
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>