<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;

$query = "SELECT `GameIdName` FROM `xoxGames`";

$queryResult = $database->query($query);

echo "<select id=\"gameList\" size=\"8\">";

while ($game = $queryResult->fetch_assoc()) {
    echo "<option>";
    echo $game["GameIdName"];
    echo  "</option>\n";
}

echo  "</select>";