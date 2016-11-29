<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$Side = $_GET["Side"];
$autoFlip = $_GET["autoFlip"];

$query = "SELECT * FROM citiesGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows != "0") {
    echo "Game with specified Id already exists in database";
    exit;
}

// Game not found => create One
$Pass1 = rand(10000, 99999);
$Pass2 = rand(10000, 99999);

$Connected1 = "false";
$Connected2 = "false";
if ($Side == '1')
{
    $Connected1 = "true";
    $Connected2 = "false";
}
if ($Side == '2')
{
    $Connected1 = "false";
    $Connected2 = "true";
}

if ($autoFlip == "1")
{
    $Pass1 = $Pass2;
    $Connected1 = "true";
    $Connected2 = "true";
}

if ($gameId == "")
{
    $gameIds = array();
    $queryGameNames = "SELECT GameIdName FROM citiesGames";
    $namesQueryResult = $database->query($queryGameNames);
    while ($gameName = $namesQueryResult->fetch_assoc())
    {
        $gameIds[] = $gameName["GameIdName"];
    }

    $gameIndex = 1;
    while ((in_array("Игра " . $gameIndex, $gameIds)) && ($gameIndex < 10000))
    {
        $gameIndex++;
    }

    if ($gameIndex < 10000)
    {
        $gameId = "Игра " . $gameIndex;
    }
}


$query  = "INSERT INTO citiesGames (GameIdName, Pass1, Pass2, Connected1, Connected2, Moves1, Moves2) ";
$query .= "VALUES (\"" . $gameId . "\", ";
$query .= "\"" . $Pass1 . "\",";
$query .= "\"" . $Pass2 . "\",";
$query .= "\"" . $Connected1 . "\",";
$query .= "\"" . $Connected2 . "\",";
$query .= "\"" . "" . "\","; // Moves1
$query .= "\"" . "" . "\" "; // Moves2
$query .= ")";

$database->query($query);


if ($autoFlip == "1") {
    $result = array();
    $result["GameIdName"] = $gameId;
    $result["Pass1"] = $Pass1;
    $result["Pass2"] = $Pass2;
    echo json_encode($result);
    exit;
}

if ($Side == "1") {
    $result = array();
    $result["GameIdName"] = $gameId;
    $result["Pass1"] = $Pass1;
    echo json_encode($result);
}

if ($Side == "2") {
    $result["GameIdName"] = $gameId;
    $result["Pass2"] = $Pass2;
    echo json_encode($result);
}
