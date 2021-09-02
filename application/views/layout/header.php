<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes</title>
    <? $links = array(
            array(
                'href'  => 'assets/paper/css/bootstrap.min.css',
                'rel'   => 'stylesheet',
                'type'  => 'text/css'
            ),
            array(
                'href'  => 'assets/paper/css/paper-dashboard.css',
                'rel'   => 'stylesheet',
                'type'  => 'text/css'
            ),
            array(
                'href'  => 'css/printer.css',
                'rel'   => 'stylesheet',
                'type'  => 'text/css'
            ),
            array(
                'href'  => 'css/printer.css',
                'rel'   => 'stylesheet',
                'type'  => 'text/css'
            ),
        );
        foreach ($links as $ai => $link) {
            echo link_tag($link);
        }
    ?>
</head