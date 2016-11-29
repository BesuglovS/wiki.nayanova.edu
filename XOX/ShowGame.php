<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$XO = $_GET["XO"];
$MoveListIndex = $_GET["MoveListIndex"];
$watch = $_GET["watch"];

$query = "SELECT * FROM `xoxGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();

if (((($XO == 'X') && ($pass != $gameArray["xPass"])) ||
    (($XO == 'O') && ($pass != $gameArray["oPass"]))) &&
    $watch != "1")
{
    echo "Не удалось подключится. Неверный пароль.";
    exit;
}

$newMoves = str_split($gameArray["Moves"], 4);
if ($gameArray["Moves"] == "")
{
    $newMoves = array();
}

require_once "Game.php";

$game = new Game();

echo $game->GenerateFieldHtml($newMoves, $MoveListIndex);