<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$Side = $_GET["Side"];
$watch = $_GET["watch"];

$query = "SELECT * FROM citiesGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();

if (((($Side == '1') && ($pass != $gameArray["Pass1"])) ||
     (($Side == '2') && ($pass != $gameArray["Pass2"]))) &&
    $watch != "1")
{
    echo "Не удалось подключится. Неверный пароль.";
    exit;
}

$moves1 = explode("@", $gameArray["Moves1"]);
if ($gameArray["Moves1"] == "")
{
    $moves1 = array();
}

$moves1Cities = array();
$moves1Sorted = array();
$moves1Count = count($moves1);
if ($moves1Count > 0) {
    $moves1In = "WHERE cityId IN (" . implode(", ", $moves1) . ") ";
    $query1Cities = "SELECT * FROM cities " . $moves1In;
    $q1Result = $database->query($query1Cities);
    while ($city = $q1Result->fetch_assoc()) {
        $moves1Cities[$city["cityId"]] = $city;
    }

    for($i = 0; $i < $moves1Count; $i++)
    {
        $moves1Sorted[] = $moves1Cities[$moves1[$i]];
    }
}


$moves2 = explode("@", $gameArray["Moves2"]);
if ($gameArray["Moves2"] == "")
{
    $moves2 = array();
}

$moves2Cities = array();
$moves2Sorted = array();
$moves2Count = count($moves2);
if ($moves2Count > 0) {
    $moves2In = "WHERE cityId IN (" . implode(", ", $moves2) . ") ";
    $query2Cities = "SELECT * FROM cities ";
    if (count($moves2) > 0) {
        $query2Cities .= $moves2In;
    }
    $q2Result = $database->query($query2Cities);

    while ($city = $q2Result->fetch_assoc()) {
        $moves2Cities[$city["cityId"]] = $city;
    }

    for($i = 0; $i < $moves2Count; $i++)
    {
        $moves2Sorted[] = $moves2Cities[$moves2[$i]];
    }
}

require_once "Game.php";

$game = new Game();

$makeMoveButton = "true";
if ($watch == "1")
{
    $makeMoveButton = "false";
}

echo $game->GeneratePositionHtml($moves1Sorted, $moves2Sorted, $makeMoveButton);