<?php
mb_internal_encoding('utf-8');
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$side = $_GET["side"];
$move = $_GET["move"];

$query = "SELECT * FROM citiesGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

$gameString="";

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();


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


//echo "Игрок уже выиграл!";
//exit;

$moves1 = explode("@",$gameArray["Moves1"]);
$moveCount1 = 0;
if ($gameArray["Moves1"] != "")
{
    $moveCount1 = count($moves1);
}

$moves2 = explode("@",$gameArray["Moves2"]);
$moveCount2 = 0;
if ($gameArray["Moves2"] != "")
{
    $moveCount2 = count($moves2);
}

if ((($side == "1") && ($moveCount1 == $moveCount2 + 1)) ||
    (($side == "2") && ($moveCount1 == $moveCount2))) {
    echo "Сейчас не ваш ход.";
    exit;
}


$opponentMoves = "";
$firstMove = "false";
if ($side == "1")
{
    $opponentMoves = $gameArray["Moves2"];
    if ($gameArray["Moves1"] == "")
    {
        $firstMove = "true";
    }
}
if ($side == "2")
{
    $opponentMoves = $gameArray["Moves1"];
    if ($gameArray["Moves2"] == "")
    {
        $firstMove = "true";
    }
}

if ($opponentMoves != "") {
    $lastMove = end(explode('@', $opponentMoves));

    $queryLastMoveString = "SELECT * FROM cities WHERE cityId = " . $lastMove;
    $qResult = $database->query($queryLastMoveString);

    $lastMoveObject = $qResult->fetch_assoc();
    $lastMoveString = $lastMoveObject["Name"];

    $lastLetter = mb_substr($lastMoveString, -1);
    $transitLetter = mb_strtoupper($lastLetter);

    if (mb_substr($move, 0, 1) != $transitLetter) {
        echo "Неверная начальная буква. ";
        exit;
    }
}

$queryCity = "SELECT * FROM cities WHERE Name = '" . $move . "'";
$qResult = $database->query($queryCity);

if ($qResult->num_rows == 0) {
    echo "Такого города не существует.";
    exit;
}

$NameObject = $qResult->fetch_assoc();
$moveId = $NameObject["cityId"];

if ((in_array($moveId, $moves1)) || (in_array($moveId, $moves2)))
{
    echo "Этот город уже был.";
    exit;
}



if ($side == "1")
{
    if ($firstMove == "true")
    {
        $gameArray["Moves1"] = $moveId;
    }
    else
    {
        $gameArray["Moves1"] .= "@" . $moveId;
    }
}

if ($side == "2")
{
    if ($firstMove == "true")
    {
        $gameArray["Moves2"] = $moveId;
    }
    else
    {
        $gameArray["Moves2"] .= "@" . $moveId;
    }
}


$updateQuery  = "UPDATE citiesGames SET ";
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

