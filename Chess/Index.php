<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;

include_once "./php/Game.php";
$game = new Game();
?>
<!DOCTYPE html>
<html>
<head lang="ru">
    <meta charset="UTF-8">
    <title>Шахматы</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="../upload/bootstrap/css/bootstrap.min.css">
    <script src="../upload/bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="./css/bootstrap-dialog.min.css">
    <script src="./js/bootstrap-dialog.min.js"></script>

    <script src="./js/chess.js"></script>
    <script src="./js/chessboard-0.3.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/chessboard-0.3.0.min.css">

    <link href="css/main.css" type="text/css" rel="stylesheet" />
    <script src="js/main.js"></script>
</head>
<body>
<?php
echo($game->CreateConnectPrompt($database));
echo "<table id=\"BigTable\">";
echo "<tr>";
echo "<td>";
echo "<div id=\"board\" style=\"width: 400px\"></div>";
echo "</td>";
echo "<td id=\"MovesTableCell\">";
echo "<select id=\"movesList\" size=\"16\"></select>";
echo "<div id=\"movesArrowsFirstRowDiv\">";
echo "<button type=\"button\" class=\"btn btn-default\" id=\"showPrevMove\"><</button>";
echo "<button type=\"button\" class=\"btn btn-default\" id=\"showNextMove\">></button>";
echo "<button type=\"button\" class=\"btn btn-default\" id=\"refreshMoveList\"><span class='glyphicon glyphicon-refresh'></span></button>";
echo "</div>";
echo "<div id=\"movesArrowsSecondRowDiv\">";
echo "<button type=\"button\" class='btn btn-default' id=\"toBegin\"><<</button>";
echo "<button type=\"button\" class='btn btn-default' id=\"toEnd\">>></button>";
echo "</div>";
echo "</td>";
echo "</tr>";
echo "</table>";

//echo "<div id=\"board\" style=\"width: 600px; margin: 0 auto;\">\n";
//echo "</div>";
?>
</body>
</html>