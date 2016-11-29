<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];

$XO = $_GET["XO"];

$i = $_GET["i"];
$j = $_GET["j"];
$k = $_GET["k"];
$l = $_GET["l"];

$query = "SELECT * FROM `xoxGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

$gameString="";

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();

if ((($XO == 'X') && ($pass != $gameArray["xPass"])) ||
    (($XO == 'O') && ($pass != $gameArray["oPass"])))
{
    echo "Не удалось подключится. Неверный пароль.";
    exit;
}

$gameString = $gameArray["Field"];
$lastMoveString = substr($gameString, 0, 4);
$fieldString = substr($gameString, 5);

// Checking state of the game
$bigField = $gameArray["BigField"];

/**
 * @param $bigField
 * @return array
 */
function CheckFieldState($bigField)
{
    for ($ee = 0; $ee < 3; $ee++) {

        if (($bigField[$ee * 3 + 0] == $bigField[$ee * 3 + 1]) &&
            ($bigField[$ee * 3 + 1] == $bigField[$ee * 3 + 2])
        ) {
            if ($bigField[$ee * 3 + 0] != "0") {
                $state = $bigField[$ee * 3 + 0];
            }
        }

        if (($bigField[0 * 3 + $ee] == $bigField[1 * 3 + $ee]) &&
            ($bigField[1 * 3 + $ee] == $bigField[2 * 3 + $ee])
        ) {
            if ($bigField[0 * 3 + $ee] != "0") {
                $state = $bigField[0 * 3 + $ee];
            }
        }
    }

    if ((($bigField[0 * 3 + 0] == $bigField[1 * 3 + 1]) &&
            ($bigField[1 * 3 + 1] == $bigField[2 * 3 + 2])) ||
        (($bigField[0 * 3 + 2] == $bigField[1 * 3 + 1]) &&
            ($bigField[1 * 3 + 1] == $bigField[2 * 3 + 0]))
    ) {
        if ($bigField[1 * 3 + 1] != "0") {
            $state = $bigField[1 * 3 + 1];
        }
    }

    if ($state == "0") {
        $full = true;
        for ($bb = 0; $bb < 2; $bb++) {
            for ($nn = 0; $nn < 2; $nn++) {
                if ($bigField[$bb * 3 + $nn] == "0") {
                    $full = false;
                }
            }
        }

        if ($full) {
            $state = 3;
            return $state;
        }
        return $state;
    }
    return $state;
}

if ($bigField[$i * 3 + $j] == "0") {
    // Checking state on bigField
    $state = 0;
    $state = CheckFieldState($bigField);
}

if ($state == "1")
{
    echo "Крестики уже выиграли!";
    exit;
}

if ($state == "2")
{
    echo "Нолики уже выиграли!";
    exit;
}



$cellSign = $fieldString[$i*27 + $j * 9 + $k * 3 + $l];
if ($cellSign != 0)
{
    echo "Поле уже занято.";
    exit;
}

$XCount = substr_count($fieldString, '1');
$OCount = substr_count($fieldString, '2');

$signNumber = -1;
if ($XCount == $OCount)
{
    $signNumber = 1;
}
if ($XCount == $OCount+1)
{
    $signNumber = 2;
}

$MoveNumber = -1;
if ($XO == "X") {
    $MoveNumber = 1;
}
if ($XO == "O") {
    $MoveNumber = 2;
}

if ($signNumber != $MoveNumber)
{
    echo "Сейчас не ваш ход.";
    exit;
}

$lastMoveK = $lastMoveString[2];
$lastMoveL = $lastMoveString[3];

$smallFieldFull = true;
for($sfi = 0; $sfi < 3; $sfi++){
    for($sfj = 0; $sfj < 3; $sfj++){
        if ($fieldString[$lastMoveK*27 + $lastMoveL * 9 + $sfi * 3 + $sfj] == 0)
        {
            $smallFieldFull = false;
            break;
        }

        if (!$smallFieldFull)
        {
            break;
        }
    }
}

$firstMove = false;
if ($lastMoveString == "9999")
{
    $firstMove = true;
}


if ((($i != $lastMoveString[2]) || ($j != $lastMoveString[3])) && !$smallFieldFull && !$firstMove)
{
    echo "Ход не в то поле." . $lastMoveString[2] . $lastMoveString[3];
    exit;
}

$gameString[5 + $i*27 + $j * 9 + $k * 3 + $l] = $signNumber;
$fieldString[$i*27 + $j * 9 + $k * 3 + $l] = $signNumber;

$gameString[0] = $i;
$gameString[1] = $j;
$gameString[2] = $k;
$gameString[3] = $l;

$smallFieldState = "0";
for($ee = 0; $ee < 3; $ee++) {

    if (($fieldString[$i * 27 + $j * 9 + $ee * 3 + 0] == $fieldString[$i * 27 + $j * 9 + $ee * 3 + 1]) &&
        ($fieldString[$i * 27 + $j * 9 + $ee * 3 + 1] == $fieldString[$i * 27 + $j * 9 + $ee * 3 + 2])
    ) {
        if ($fieldString[$i * 27 + $j * 9 + $ee * 3 + 0] != "0") {
            $smallFieldState = $fieldString[$i * 27 + $j * 9 + $ee * 3 + 0];
        }
    }

    if (($fieldString[$i * 27 + $j * 9 + 0 * 3 + $ee] == $fieldString[$i * 27 + $j * 9 + 1 * 3 + $ee]) &&
        ($fieldString[$i * 27 + $j * 9 + 1 * 3 + $ee] == $fieldString[$i * 27 + $j * 9 + 2 * 3 + $ee])
    ) {
        if ($fieldString[$i * 27 + $j * 9 + 0 * 3 + $ee] != "0") {
            $smallFieldState = $fieldString[$i * 27 + $j * 9 + 0 * 3 + $ee];
        }
    }
}

if ((($fieldString[$i * 27 + $j * 9 + 0 * 3 + 0] == $fieldString[$i * 27 + $j * 9 + 1 * 3 + 1]) &&
        ($fieldString[$i * 27 + $j * 9 + 1 * 3 + 1] == $fieldString[$i * 27 + $j * 9 + 2 * 3 + 2])) ||
    (($fieldString[$i * 27 + $j * 9 + 0 * 3 + 2] == $fieldString[$i * 27 + $j * 9 + 1 * 3 + 1]) &&
        ($fieldString[$i * 27 + $j * 9 + 1 * 3 + 1] == $fieldString[$i * 27 + $j * 9 + 2 * 3 + 0])))
{
    if ($fieldString[$i * 27 + $j * 9 + 1 * 3 + 1] != "0") {
        $smallFieldState = $fieldString[$i * 27 + $j * 9 + 1 * 3 + 1];
    }
}

if (($smallFieldState != "0") && ($bigField[$i*3 + $j] == "0"))
{
    $bigField[$i*3 + $j] = $smallFieldState;
}

$newMove = $i . $j . $k . $l;

$gameArray["Moves"] .= $newMove;

$updateQuery  = "UPDATE `xoxGames` SET ";
$updateQuery .= "`Field`=\"" . $gameString . "\", ";
$updateQuery .= "`Moves`=\"" . $gameArray["Moves"] . "\" ";
if ($smallFieldState != "0") {
    $updateQuery .= ",`BigField`=\"" . $bigField . "\" ";
}
$updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";

$database->query($updateQuery);

$state = 0;
$state = CheckFieldState($bigField);

if (($state == "1") || ($state == "2"))
{
    echo "@" . $state;
}

require_once "Game.php";

$game = new Game();

$newMoves = str_split($gameArray["Moves"], 4);

echo $game->GenerateFieldHtml($newMoves,'End');