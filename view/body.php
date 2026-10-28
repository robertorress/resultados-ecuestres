<?php $activeSection = 'inicio'; require __DIR__ . '/partials/navigation-concurso.php'; ?>

<style>
    /* Para no traducir los iconos. Vía más rápida por CSS para navegadores modernos: */
    .material-icons {
        translate: no !important;
    }

    .collapsible-header .indicador-despliegue {
        margin-left: auto;
        margin-right: 0;
        transition: transform 0.2s ease;
    }

    .collapsible li.active .indicador-despliegue {
        transform: rotate(180deg);
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

    /* Estilo para hacer el mapa responsivo */
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

    .anuncios-coleccion .collection-item {
        align-items: center;
        display: flex;
        gap: 16px;
        justify-content: space-between;
    }

    .anuncio-texto {
        flex: 1;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .anuncio-contenido {
        flex: 1;
        min-width: 0;
    }

    .anuncio-cabecera {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 4px;
    }

    .anuncio-titulo {
        font-weight: 600;
    }

    .anuncio-fecha {
        color: #757575;
        font-size: 0.85rem;
    }

    .anuncio-tipo {
        border-radius: 3px;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 6px;
    }

    @media only screen and (max-width: 600px) {
        .anuncios-coleccion .collection-item {
            align-items: flex-start;
            flex-direction: column;
        }
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
        <?php $anuncios = $this->m_model->GetAnuncios(); ?>
        <?php
        $anunciosVisibles = [];
        if ($anuncios !== null && $this->m_model->GetAnunciosVisible()) {
            foreach ($anuncios as $anuncio) {
                if ($this->m_model->IsAnuncioVisible($anuncio)) {
                    $anunciosVisibles[] = $anuncio;
                }
            }
        }
        ?>
        <?php if (count($anunciosVisibles) > 0): ?>
            <div class="card grey darken-1">
                <div class="card-content white-text">
                    <?php /* <span class="card-title white-text">ANUNCIOS</span> */ ?>
                    <p>
                    <div class="row">
                        <div class="col s12 center">
							<div class="card-panel blue-grey lighten-1">
								<ul class="collection anuncios-coleccion left-align">
									<?php foreach ($anunciosVisibles as $anuncio): ?>
										<?php
										$tipoAnuncio = strtolower(trim((string)($anuncio->tipo ?? 'info')));
										$tiposAnuncio = [
											'info' => ['icono' => 'info', 'clase' => 'blue darken-2', 'etiqueta' => 'INFO'],
											'none' => ['icono' => 'info', 'clase' => '', 'etiqueta' => ''],
											'importante' => ['icono' => 'priority_high', 'clase' => 'orange darken-2', 'etiqueta' => 'IMPORTANTE'],
											'urgente' => ['icono' => 'warning', 'clase' => 'red darken-2', 'etiqueta' => 'URGENTE'],
											'resultado' => ['icono' => 'assessment', 'clase' => 'green darken-2', 'etiqueta' => 'RESULTADO'],
										];
										$tipoPresentacion = $tiposAnuncio[$tipoAnuncio] ?? $tiposAnuncio['info'];
										$tituloAnuncio = trim((string)($anuncio->titulo ?? ''));
										$fechaAnuncio = trim((string)($anuncio->fecha ?? ''));
										$textoAnuncio = isset($anuncio->texto)
											? trim((string)$anuncio->texto)
											: trim((string)$anuncio);
										$textoEnlace = trim((string)($anuncio->link->texto ?? ''));
										$urlEnlace = trim((string)($anuncio->link->url ?? ''));
										if ($urlEnlace === '') {
											$urlEnlace = trim((string)($anuncio->link ?? ''));
										}
										if ($textoEnlace === '' && $urlEnlace !== '') {
											$textoEnlace = 'VER ENLACE';
										}
										?>
										<?php if ($textoAnuncio !== ''): ?>
											<li class="collection-item">
												<div class="anuncio-contenido">
													<div class="anuncio-cabecera">
														<i class="material-icons" aria-hidden="true"><?php echo escape_html($tipoPresentacion['icono']); ?></i>
														<?php if ($tituloAnuncio !== ''): ?>
															<span class="anuncio-titulo"><?php echo escape_html($tituloAnuncio); ?></span>
														<?php endif; ?>
														<?php if ($tipoPresentacion['etiqueta'] !== ''): ?>
															<span class="anuncio-tipo <?php echo escape_html($tipoPresentacion['clase']); ?>"><?php echo escape_html($tipoPresentacion['etiqueta']); ?></span>
														<?php endif; ?>
														<?php if ($fechaAnuncio !== ''): ?>
															<span class="anuncio-fecha"><?php echo escape_html($fechaAnuncio); ?></span>
														<?php endif; ?>
													</div>
													<div class="anuncio-texto"><?php echo escape_html($textoAnuncio); ?></div>
												</div>
												<?php if ($urlEnlace !== ''): ?>
													<a class="waves-effect waves-light btn blue accent-1" href="<?php echo escape_html($urlEnlace); ?>" target="_blank" rel="noopener noreferrer">
														<?php echo escape_html($textoEnlace); ?>
													</a>
												<?php endif; ?>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>
                        </div>
                    </div>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <?php
        $pruebas = $this->m_model->GetPruebas();
        $concursoId = $this->m_model->GetConcursoId();
        if (count($pruebas) > 0) {
            ?>
            <ul class="collapsible resultados-concurso">
                <li>
                    <div class="collapsible-header blue-grey lighten-1">
                        <i class="material-icons" aria-hidden="true">assessment</i>
                        <span>RESULTADOS</span>
                        <i class="material-icons indicador-despliegue" aria-hidden="true">expand_more</i>
                    </div>
                    <div class="collapsible-body">
                        <div class="card-panel blue-grey lighten-1">
                            <?php require __DIR__ . '/partials/result-test-links.php'; ?>
                        </div>
                    </div>
                </li>
            </ul>
            <?php
        }
        ?>
        <?php
        $lugar = $this->m_model->GetUbicacionNombre();
        if ($this->m_model->GetUbicacionVisible() && $lugar != null) {
            $url_embed_por_nombre = "https://maps.google.com/maps?q=" . urlencode($lugar) . "&output=embed";
            ?>
            <ul class="collapsible ubicacion-concurso">
                <li>
                    <div class="collapsible-header blue-grey lighten-1">
                        <i class="material-icons" aria-hidden="true">location_on</i>
                        <span>UBICACIÓN</span>
                        <i class="material-icons indicador-despliegue" aria-hidden="true">expand_more</i>
                    </div>
                    <div class="collapsible-body">
                        <div class="card-panel blue-grey lighten-1">
                            <div class="map-responsive">
                                <iframe
                                    title="Ubicación del concurso"
                                    src="<?php echo escape_html($url_embed_por_nombre); ?>"
                                    allowfullscreen
                                    loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <?php
        }
        ?>
        <h5>
            <?php echo escape_html($this->m_model->GetFecha()); ?>
        </h5>
        </p>
    </div>

</div>