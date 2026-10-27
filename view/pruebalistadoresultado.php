<?php $activeSection = 'resultados'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<div class="row">
    <div class="col s12 center">
        <div class="card grey darken-1">
            <div class="card-content">
                <span class="card-title white-text">RESULTADOS PROVISIONALES</span>
                <p class="detalle-seccion-titulo"><?php echo escape_html($this->m_model->GetFecha()); ?></p>

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
            <div class="card-action">
                <a href="?c=Prueba&a=MostrarPrueba&id=<?php echo escape_html($concursoId); ?>" aria-label="Volver al concurso"><i class="large material-icons" aria-hidden="true">arrow_back</i></a>
            </div>
        </div>
    </div>
</div>