<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
include_once "ToolBox.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$side = $_GET["side"];
$guess = $_GET["guess"];

if (strlen($guess) == 4)
{
    $guess = "0" . $guess;
}


$query = "SELECT * FROM numsGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

$gameString="";


if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();

$firstPlayerWins = "0";
$secondPlayerWins = "0";
if (substr($gameArray["Moves1"], -1) == "5")
{
    $firstPlayerWins = "1";
}
if (substr($gameArray["Moves2"], -1) == "5")
{
    $secondPlayerWins = "1";
}

if (($gameArray["Connected1"] == "false") || ($gameArray["Connected2"] == "false")) {
    echo "Нельзя ходить пока соперник не подключился.";
    exit;
}


if ((($side == '1') && ($pass != $gameArray["Pass1"])) ||
    (($side == '2') && ($pass != $gameArray["Pass2"])))
{
    echo "Не удалось подключится. Неверный пароль.";
    exit;
}

$ideal = "";

if ($side == "1")
{
    $ideal = $gameArray["Num2"];
}

if (($side == "1") && ($gameArray["Moves1"] !== "") && (substr($gameArray["Moves1"], -1) == "5"))
{
    echo "Игрок уже выиграл!";
    exit;
}

if ($side == "2")
{
    $ideal = $gameArray["Num1"];
}
if (($side == "2") && ($gameArray["Moves2"] !== "") && (substr($gameArray["Moves2"], -1) == "5"))
{
    echo "Игрок уже выиграл!";
    exit;
}

if ((($side == "1") && (!$secondPlayerWins) && (strlen($gameArray["Moves1"]) !== strlen($gameArray["Moves2"]))) ||
    (($side == "2") && (!$firstPlayerWins) && ((strlen($gameArray["Moves1"]) - 7 ) !== strlen($gameArray["Moves2"]))))
{
    echo "Сейчас не ваш ход.";
    exit;
}

if (isCorrect($guess) == "false")
{
    echo "Erroneous number";
    exit;
}

$cnt = MakeCount($guess, $ideal);

$moveString = $guess . $cnt;

$updateQuery  = "UPDATE numsGames SET ";
if ($side == "1")
{
    $updateQuery .= "`Moves1`=\"" . $gameArray["Moves1"] . $moveString . "\" ";
}
if ($side == "2")
{
    $updateQuery .= "`Moves2`=\"" . $gameArray["Moves2"] . $moveString . "\" ";
}
$updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";

$database->query($updateQuery);


echo "{\"firstPlayerWins\":\"" . $firstPlayerWins . "\", \"secondPlayerWins\":\"" .  $secondPlayerWins . "\"}";