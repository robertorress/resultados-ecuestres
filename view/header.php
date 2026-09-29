<?php require_once "view/tools.php"; ?>

<html lang="es">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="materialize/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection" />
    <link href="recursos/css/app.css?v=<?php echo RESULTADOS_APP_CSS_VERSION; ?>" type="text/css" rel="stylesheet" media="screen,projection" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="recursos/imagenes/sombrero_doma_clasica.ico" type="image/x-icon">

    <title>
        <?php echo escape_html($this->m_model->GetConcurso()) ?>
    </title>

</head>

<body class="site-shell">
    <a class="skip-link" href="#contenido">Saltar al contenido</a>
    <?php require __DIR__ . '/partials/navigation-sidenav.php'; ?>

    <div id="contenido" class="section" tabindex="-1">