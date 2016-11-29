<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
include_once "ToolBox.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$Side = $_GET["Side"];
$num = $_GET["num"];

if (($num !== "undefined") && ($num != "") && (isCorrect($num) == "false"))
{
    echo "Erroneous number";
    exit;
}

if (($num == "") || ($num == "undefined")) {
    $num = generateNum();
}


$query = "SELECT * FROM numsGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

$gameString="";

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();
if ((($Side == '1') && ($gameArray["Connected1"] == "true")) ||
    (($Side == '2') && ($gameArray["Connected2"] == "true")))
{
    if ((($Side == "1") && $pass == $gameArray["Pass1"]) ||
        (($Side == "2") && $pass == $gameArray["Pass2"])) {
        echo "Success";
        exit;
    }
    else
    {
        echo "Сторона уже занята. Неверный пароль.";
        exit;
    }
}

if ((($Side == '1') && ($gameArray["Connected1"] == "false")) ||
    (($Side == '2') && ($gameArray["Connected2"] == "false")))
{
    if ($Side == "1") {
        echo "{\"Pass1\":\"" . $gameArray["Pass1"] . "\", \"Num1\":\"" . $num . "\"}";

        $updateQuery  = "UPDATE numsGames SET ";
        $updateQuery .= "`Connected1`=\"true\", ";
        $updateQuery .= "`Num1`=\"" . $num . "\" ";
        $updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";
    }

    if ($Side == "2") {
        echo "{\"Pass2\":\"" . $gameArray["Pass2"] . "\", \"Num2\":\"" . $num . "\"}";

        $updateQuery  = "UPDATE numsGames SET ";
        $updateQuery .= "`Connected2`=\"true\", ";
        $updateQuery .= "`Num2`=\"" . $num . "\" ";
        $updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";
    }

    $database->query($updateQuery);
}