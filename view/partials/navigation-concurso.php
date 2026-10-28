<?php
$activeSection = $activeSection ?? '';
$concursoId = escape_html($this->m_model->GetConcursoId());
$navigationItems = [
    [
        'section' => 'inicio',
        'href' => 'index.php',
        'icon' => 'home',
        'label' => 'INICIO',
    ],
    [
        'section' => 'plano',
        'href' => '?c=Prueba&a=Plano&id=' . $concursoId,
        'icon' => 'map',
        'label' => 'PLANO',
    ],
    [
        'section' => 'orden',
        'href' => '?c=Prueba&a=ListadoOrdenSalida&id=' . $concursoId,
        'icon' => 'access_time',
        'label' => 'ORDEN DE SALIDA',
    ],
];
$renderNavigationItems = function ($items, $activeSection, $isMobile = false) {
    foreach ($items as $item) {
        $isActive = $activeSection === $item['section'];
        $iconClasses = $isMobile && $item['section'] === 'inicio'
            ? 'large material-icons notranslate'
            : 'left large material-icons notranslate';
        ?>
        <li class="<?php echo $isActive ? 'active' : ''; ?>">
            <a href="<?php echo $item['href']; ?>">
                <i class="<?php echo $iconClasses; ?>"><?php echo $item['icon']; ?></i><?php echo $item['label']; ?>
            </a>
        </li>
        <?php
    }
};
?>
<nav aria-label="Navegación del concurso">
    <div class="nav-wrapper blue darken-4">
        <ul class="right hide-on-med-and-down">
            <?php $renderNavigationItems($navigationItems, $activeSection); ?>
        </ul>
        <ul id="nav-mobile" class="sidenav blue accent-1">
            <div class="user-view"></div>
            <?php $renderNavigationItems($navigationItems, $activeSection, true); ?>
        </ul>
        <a href="#" data-target="nav-mobile" class="sidenav-trigger" aria-label="Abrir menú de navegación">
            <i class="material-icons notranslate">&#xE5D2;</i>
        </a>
    </div>
</nav>
