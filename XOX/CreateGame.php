<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$XO = $_GET["XO"];
$autoFlip = $_GET["autoFlip"];

$query = "SELECT * FROM `xoxGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows != "0") {
    echo "Game with specified Id already exists in database";
    exit;
}

// Game not found => create One
$xPass = rand(10000, 99999);
$oPass = rand(10000, 99999);
$xConnected = "false";
$oConnected = "false";
if ($XO == 'X')
{
    $xConnected = "true";
}
if ($XO == 'O')
{
    $oConnected = "true";
}

if ($autoFlip == "1")
{
    $oPass = $xPass;
    $xConnected = "true";
    $oConnected = "true";
}

if ($gameId == "")
{
    $gameIds = array();
    $queryGameNames = "SELECT GameIdName FROM xoxGames";
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


$query  = "INSERT INTO `xoxGames`(`GameIdName`, `Field`, `Password`, `xPass`, `oPass`, `xConnected`, `oConnected`, `BigField`) ";
$query .= "VALUES (\"" . $gameId . "\", ";
$query .= "\"9999@000000000000000000000000000000000000000000000000000000000000000000000000000000000\", ";
$query .= "\"" . $pass . "\",";
$query .= "\"" . $xPass . "\",";
$query .= "\"" . $oPass . "\",";
$query .= "\"" . $xConnected . "\",";
$query .= "\"" . $oConnected . "\",";
$query .= "\"000000000\"";
$query .= ")";

$database->query($query);

if ($XO == "X") {
    echo "{\"GameIdName\":\"" . $gameId . "\", \"xPass\":\"" . $xPass . "\"}";
}

if ($XO == "O") {
    echo "{\"GameIdName\":\"" . $gameId . "\", \"oPass\":\"" . $oPass . "\"}";
}
