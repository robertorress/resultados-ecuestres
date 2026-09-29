<?php
$activeSection = $activeSection ?? '';
$concursoId = escape_html($this->m_model->GetConcursoId());
?>
<nav aria-label="Navegación del concurso">
    <div class="nav-wrapper blue darken-4">
        <ul class="right hide-on-med-and-down">
            <li class="<?php echo $activeSection === 'inicio' ? 'active' : ''; ?>">
                <a href="index.php"><i class="left large material-icons notranslate">home</i>INICIO</a>
            </li>
            <li class="<?php echo $activeSection === 'orden' ? 'active' : ''; ?>">
                <a href="?c=Prueba&a=ListadoOrdenSalida&id=<?php echo $concursoId; ?>">
                    <i class="left large material-icons notranslate">access_time</i>ORDEN DE SALIDA
                </a>
            </li>
            <li class="<?php echo $activeSection === 'resultados' ? 'active' : ''; ?>">
                <a href="?c=Prueba&a=ListadoResultado&id=<?php echo $concursoId; ?>">
                    <i class="left large material-icons notranslate">visibility</i>RESULTADOS
                </a>
            </li>
        </ul>
        <ul id="nav-mobile" class="sidenav blue accent-1">
            <div class="user-view"></div>
            <li class="<?php echo $activeSection === 'inicio' ? 'active' : ''; ?>">
                <a href="index.php"><i class="large material-icons notranslate">home</i>INICIO</a>
            </li>
            <li class="<?php echo $activeSection === 'orden' ? 'active' : ''; ?>">
                <a href="?c=Prueba&a=ListadoOrdenSalida&id=<?php echo $concursoId; ?>">
                    <i class="left large material-icons notranslate">access_time</i>ORDEN DE SALIDA
                </a>
            </li>
            <li class="<?php echo $activeSection === 'resultados' ? 'active' : ''; ?>">
                <a href="?c=Prueba&a=ListadoResultado&id=<?php echo $concursoId; ?>">
                    <i class="left large material-icons notranslate">visibility</i>RESULTADOS
                </a>
            </li>
        </ul>
        <a href="#" data-target="nav-mobile" class="sidenav-trigger" aria-label="Abrir menú de navegación">
            <i class="material-icons notranslate">&#xE5D2;</i>
        </a>
    </div>
</nav>
