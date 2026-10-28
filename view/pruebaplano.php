<?php $activeSection = 'plano'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<div class="row">
    <div class="col s12 center">
        <div class="card grey darken-1">
            <div class="card-content white-text">
                <span class="card-title white-text">PLANO</span>
                <?php $concursoId = $this->m_model->GetConcursoId(); ?>
                <p>
                <div class="row">
                    <div class="col s12 center">
                        <?php
                        $planosDisponibles = [];
                        $planos = $this->m_model->GetPlanos();

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
            <div class="card-action">
                <a href="?c=Prueba&a=MostrarPrueba&id=<?php echo escape_html($concursoId); ?><?php echo $this->m_model->GetJornadaId() !== null ? '&jornada=' . escape_html($this->m_model->GetJornadaId()) : ''; ?>" aria-label="Volver al concurso"><i class="large material-icons" aria-hidden="true">arrow_back</i></a>
            </div>
        </div>
    </div>
</div>
