<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;

include_once "Game.php";
$game = new Game();
?>
<!DOCTYPE html>
<html>
<head lang="ru">
    <meta charset="UTF-8">
    <title>Города</title>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../upload/bootstrap/css/bootstrap.min.css">
    <script src="../upload/bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="bootstrap-dialog.min.css">
    <script src="bootstrap-dialog.min.js"></script>

    <link href="Cities.css" type="text/css" rel="stylesheet" />

    <script src="Cities.js"></script>
    <script src="jquery.hotkeys.js"></script>
</head>
<body>
<?php
echo($game->CreateConnectPrompt($database));
echo "<div id=\"board\">\n";
echo "</div>";
?>
</body>
</html>