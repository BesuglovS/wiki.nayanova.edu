<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$wb = $_GET["wb"];
$MoveListIndex = $_GET["MoveListIndex"];
$watch = $_GET["watch"];

$query = "SELECT * FROM `chessGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();

if (((($wb == 'white') && ($pass != $gameArray["wPass"])) ||
        (($wb == 'black') && ($pass != $gameArray["bPass"]))) &&
    ($watch != "1"))
{
    echo "Не удалось подключится. Неверный пароль.";
    exit;
}

require_once "Game.php";

$game = new Game();

echo $game->GenerateFieldHtml($gameArray["Field"], $gameArray["History"], $MoveListIndex);