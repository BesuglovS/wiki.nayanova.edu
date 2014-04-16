<?php
require_once("_php/includes/Database.php");
require_once("_php/includes/statsCore.php");

global $stats;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Диспетчерская учебного отдела СГОАН - Login LOG</title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Google -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />
    <!-- Main -->
    <script src="upload/_js/stat.js"></script>
    <link rel="stylesheet" type="text/css" href="upload/_css/main.css">

</head>
<body>
<div id="container">
    <header class="cf">
        <img src="upload/images/DVZ-beta.png" id="headerLogo" width="150" height="150">
        <h1>Диспетчерская учебного отдела СГОАН - Login LOG<h1>
    </header>
    <section id="content" class="cf" style="padding: 1em;">
        <?php echo $stats->LoginLOG(); ?>
    </section>
    <footer>
        <p>
            &copy; Диспетчерская учебного отдела СГОАН, <?php echo date("Y"); ?>
        </p>
    </footer>
</div><!-- end .container -->
<div id="scheduleBox" style="display: none"></div>
</body>
</html>