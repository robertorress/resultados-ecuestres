<?php foreach ($pruebas as $prueba): ?>
    <p><ul><li>
        <a class="waves-effect waves-light btn blue accent-1" href="?c=Resultado&a=Resultado&id=<?php echo escape_html($concursoId); ?>&prueba=<?php echo escape_html($prueba->id); ?>">
            <?php echo escape_html($prueba->titulo); ?>
        </a>
    </li></ul></p>
<?php endforeach; ?>
<p><ul><li>
    <a class="waves-effect waves-light btn pink accent-1" href="?c=ResultadoGeneral&a=ResultadoGeneral&id=<?php echo escape_html($concursoId); ?>">
        RESULTADOS GENERALES
    </a>
</li></ul></p>
<?php if ($this->m_model->ShouldShowResultadosEquipos()): ?>
<p><ul><li>
    <a class="waves-effect waves-light btn purple accent-1" href="?c=ResultadoEquipos&a=ResultadoEquipos&id=<?php echo escape_html($concursoId); ?>">
        RESULTADOS EQUIPOS
    </a>
</li></ul></p>
<?php endif; ?>
