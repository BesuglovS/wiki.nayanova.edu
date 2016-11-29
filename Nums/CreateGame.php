<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
include_once "ToolBox.php";

global $database;

$gameId = $_GET["id"];
$Side = $_GET["Side"];
$autoFlip = $_GET["autoFlip"];
$num = $_GET["num"];

$query = "SELECT * FROM numsGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows != "0") {
    echo "Game with specified Id already exists in database";
    exit;
}

if (($num !== "undefined") && ($num != "") && (isCorrect($num) == "false"))
{
    echo "Erroneous number";
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
    if (($num == "") || ($num == "undefined")) {
        $Num1 = generateNum();
    }
    $Num2 = "";
}
if ($Side == '2')
{
    $Connected1 = "false";
    $Connected2 = "true";
    if (($num == "") || ($num == "undefined")) {
        $Num2 = generateNum();
    }
    $Num1 = "";
}

if ($autoFlip == "1")
{
    $Pass1 = $Pass2;
    $Num1 = generateNum();
    $Num2 = generateNum();
    $Connected1 = "true";
    $Connected2 = "true";
}

if ($gameId == "")
{
    $gameIds = array();
    $queryGameNames = "SELECT GameIdName FROM numsGames";
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


$query  = "INSERT INTO numsGames (GameIdName, Pass1, Pass2, Connected1, Connected2, Num1, Moves1, Num2, Moves2) ";
$query .= "VALUES (\"" . $gameId . "\", ";
$query .= "\"" . $Pass1 . "\",";
$query .= "\"" . $Pass2 . "\",";
$query .= "\"" . $Connected1 . "\",";
$query .= "\"" . $Connected2 . "\",";
$query .= "\"" . $Num1 . "\",";
$query .= "\"" . "" . "\","; // Moves1
$query .= "\"" . $Num2 . "\",";
$query .= "\"" . "" . "\" "; // Moves2
$query .= ")";

$database->query($query);


if ($autoFlip == "1") {
    echo "{\"GameIdName\":\"" . $gameId . "\",";
    echo "\"Num1\":\"" . $Num1 . "\", \"Pass1\":\"" . $Pass1 . "\", ";
    echo "\"Num2\":\"" . $Num2 . "\", \"Pass2\":\"" . $Pass2 . "\"}";
    exit;
}

if ($Side == "1") {
    echo "{\"GameIdName\":\"" . $gameId . "\", \"Num1\":\"" . $Num1 . "\", \"Pass1\":\"" . $Pass1 . "\"}";
}

if ($Side == "2") {
    echo "{\"GameIdName\":\"" . $gameId . "\", \"Num2\":\"" . $Num2 . "\", \"Pass2\":\"" . $Pass2 . "\"}";
}
