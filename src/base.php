<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=0.333" />
        <link href='http://fonts.googleapis.com/css?family=Ubuntu:300,400,700' rel='stylesheet' type='text/css' />
        <link href='/web/css/icomoon.css<?php echo $devSuffix; ?>' rel='stylesheet' type='text/css' media="all" />
        <link href='/web/css/global.css<?php echo $devSuffix; ?>' rel='stylesheet' type='text/css' media="all" />
        <?php
            foreach($css as $cssFile) {
                echo "<link href='/web/css/";
                echo $cssFile . ".css" . $devSuffix;
                echo "' rel='stylesheet' type='text/css' media='all' />\n";
            }
        ?>
        <link rel="icon" type="image/png" href="/web/img/favicon.png<?php echo $devSuffix; ?>" />
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
        <script src="/web/script/autocomplete.js<?php echo $devSuffix; ?>"></script>
        <script src="/web/script/cookies.js<?php echo $devSuffix; ?>"></script>
        <script src="/web/script/api.js<?php echo $devSuffix; ?>"></script>
        <script src="/web/script/logger.js<?php echo $devSuffix; ?>"></script>
    </head>
    <body>
        <?php include("web/" . $content . ".php"); ?>
    </body>
</html>