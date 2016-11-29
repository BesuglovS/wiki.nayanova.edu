<?php

$valid_passwords = array ("admin" => "321");
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Not authorized");
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Chess</title>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../upload/bootstrap/css/bootstrap.min.css">
    <script src="../upload/bootstrap/js/bootstrap.min.js"></script>

    <style>
        th, td {
            padding: 1em;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        body {
            background-color: white;
        }
    </style>
    <script>
        function DeleteGame(id) {
            $.get( "DeleteGame.php?id=" + id,
                function() {
                    location.reload(true);
                }
            );
        }
    </script>
</head>
<body>
<div style="margin: 0 auto; width: 400px; ">
    <?php
    echo "<h1>Многа игр</h1>";


    $query = "SELECT * FROM `chessGames`";

    echo "<table id='infoTable' class='table-bordered table-hover'>";
    echo "<tr>";
    echo "<th>Имя игры</th>";
    echo "<th>wPass</th>";
    echo "<th>bPass</th>";
    echo "<th>wConnected</th>";
    echo "<th>bConnected</th>";
    echo "<th>Удалить</th>";
    echo "</tr>";
    $result = $database->query($query);
    while ($game = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>" . $game["GameIdName"] . "</td>";
        echo "<td>" . $game["wPass"] . "</td>";
        echo "<td>" . $game["bPass"] . "</td>";
        echo "<td>" . $game["wConnected"] . "</td>";
        echo "<td>" . $game["bConnected"] . "</td>";
        echo "<td><button type=\"button\" onclick='DeleteGame(\"" . $game["GameIdName"] . "\");'>Удалить</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>
</div>
</body>
</html>