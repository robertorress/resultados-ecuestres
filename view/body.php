<?php $activeSection = 'inicio'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<style>
    /* Para no traducir los iconos. Vía más rápida por CSS para navegadores modernos: */
    .material-icons {
        translate: no !important;
    }

    /* Estilo personalizado para hacer el mapa responsivo y mantener proporción 16:9 de Google*/
    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    /* Estilo para hacer el mapa el mapa responsivo de ChatGPT */
    .map-responsive {
        position: relative;
        overflow: hidden;
        width: 100%;
        padding-top: 56.25%;
        /* 16:9 */
        border-radius: 12px;
    }

    .map-responsive iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>

<div class="row">

    <div class="col s12 center">
        <p>
        <h2>
            <?php
            echo escape_html($this->m_model->GetConcurso());
            ?>
        </h2>
        <p>
            <img class="hipica-logo" loading="lazy" decoding="async" style="border:0;width:auto;max-width:300px;height:150px" src="<?php echo escape_html($this->m_model->GetLogo()) ?>" alt="<?php echo escape_html($this->m_model->GetConcurso()) ?>" />
        </p>
        <br>
        <?php
        $lugar = $this->m_model->GetUbicacionNombre();
        if ($lugar != null) {
            /**
             * OPCIÓN A: Usar directamente el nombre del sitio
             * Es la forma más limpia si la base de datos almacena el texto.
             */
            $url_embed_por_nombre = "https://maps.google.com/maps?q=" . urlencode($lugar) . "&output=embed";

            echo "<p>";
            echo "<div class=\"map-responsive\">";
            echo "<iframe title=\"Ubicación del concurso\" src=\"" . escape_html($url_embed_por_nombre) . "\" allowfullscreen loading=\"lazy\"></iframe>";
            echo "</div>";
            echo "</p>";
        }
        ?>
        <h5>
            <?php echo escape_html($this->m_model->GetFecha()); ?>
        </h5>
        </p>
    </div>

</div>