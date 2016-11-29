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
    <title>XOX</title>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../upload/bootstrap/css/bootstrap.min.css">
    <script src="../upload/bootstrap/js/bootstrap.min.js"></script>

    <link href="xox.css" type="text/css" rel="stylesheet" />

    <script src="xox.js"></script>
    <style>
        th, td {
            padding: 1em;
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


$query = "SELECT * FROM `xoxGames`";

echo "<table id='infoTable' class='table-bordered table-hover'>";
echo "<tr>";
echo "<th>Имя игры</th>";
echo "<th>xPass</th>";
echo "<th>oPass</th>";
echo "<th>xConnected</th>";
echo "<th>oConnected</th>";
echo "<th>Удалить</th>";
echo "</tr>";
$result = $database->query($query);
while ($game = $result->fetch_assoc())
{
    echo "<tr>";
    echo "<td>" . $game["GameIdName"] . "</td>";
    echo "<td>" . $game["xPass"] . "</td>";
    echo "<td>" . $game["oPass"] . "</td>";
    echo "<td>" . $game["xConnected"] . "</td>";
    echo "<td>" . $game["oConnected"] . "</td>";
    echo "<td><button type=\"button\" onclick='DeleteGame(\"" . $game["GameIdName"] . "\");'>Удалить</button></td>";
    echo "</tr>";
}
echo "</table>";
?>
</div>
</body>
</html>