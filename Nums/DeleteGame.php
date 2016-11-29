<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;

$gameId =  $_GET["id"];
if ($gameId == "undefined")
{
    $gameId = "";
}

$query = "DELETE FROM numsGames WHERE GameIdName = \"" . $gameId . "\"";

echo $query;

$queryResult = $database->query($query);