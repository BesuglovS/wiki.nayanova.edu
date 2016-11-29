<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$Side = $_GET["Side"];
$watch = $_GET["watch"];

$query = "SELECT * FROM numsGames WHERE GameIdName = \"" . $gameId . "\"";

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

$moves1 = str_split($gameArray["Moves1"], 7);
if ($gameArray["Moves1"] == "")
{
    $moves1 = array();
}
$moves2 = str_split($gameArray["Moves2"], 7);
if ($gameArray["Moves2"] == "")
{
    $moves2 = array();
}

require_once "Game.php";

$game = new Game();

$makeMoveButton = "true";
if ($watch == "1")
{
    $makeMoveButton = "false";
}
if ((($Side == "1") && (substr($gameArray["Moves1"], -1) == "5")) ||
    (($Side == "2") && (substr($gameArray["Moves2"], -1) == "5")))
{
    $makeMoveButton = "false";
}

echo $game->GeneratePositionHtml($moves1, $moves2, $makeMoveButton);