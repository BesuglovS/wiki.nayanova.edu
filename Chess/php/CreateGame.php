<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$wb = $_GET["wb"];

$query = "SELECT * FROM `chessGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows != "0") {
    echo "Game with specified Id already exists in database";
    exit;
}

// Game not found => create One
$wPass = rand(10000, 99999);
$bPass = rand(10000, 99999);
$wConnected = "false";
$bConnected = "false";
if ($wb == 'w')
{
    $wConnected = "true";
}
if ($wb == 'b')
{
    $bConnected = "true";
}

if ($gameId == "")
{
    $gameIds = array();
    $queryGameNames = "SELECT GameIdName FROM chessGames";
    $namesQueryResult = $database->query($queryGameNames);
    while ($gameName = $namesQueryResult->fetch_assoc())
    {
        $gameIds[] = $gameName["GameIdName"];
    }

    $gameIndex = 1;
    while ((in_array("Игра " . $gameIndex, $gameIds)) && ($gameIndex < 1000000))
    {
        $gameIndex++;
    }

    if ($gameIndex < 1000000)
    {
        $gameId = "Игра " . $gameIndex;
    }
}


$query  = "INSERT INTO `chessGames` (`GameIdName`, `Field`, `wPass`, `bPass`, `wConnected`, `bConnected`) ";
$query .= "VALUES (\"" . $gameId . "\", ";
$query .= "\"rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1\", ";
$query .= "\"" . $wPass . "\",";
$query .= "\"" . $bPass . "\",";
$query .= "\"" . $wConnected . "\",";
$query .= "\"" . $bConnected . "\"";
$query .= ")";

$database->query($query);

if ($wb == "w") {
    echo "{\"GameIdName\":\"" . $gameId . "\", \"wPass\":\"" . $wPass . "\"}";
}

if ($wb == "b") {
    echo "{\"GameIdName\":\"" . $gameId . "\", \"bPass\":\"" . $bPass . "\"}";
}
