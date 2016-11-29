<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$XO = $_GET["XO"];
$num = $_GET["num"];

$query = "SELECT * FROM `xoxGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

$gameString="";

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();
if ((($XO == 'X') && ($gameArray["xConnected"] == "true")) ||
    (($XO == 'O') && ($gameArray["oConnected"] == "true")))
{
    if ((($XO == "X") && $pass == $gameArray["xPass"]) ||
        (($XO == "O") && $pass == $gameArray["oPass"])) {
        echo "Success";
        exit;
    }
    else
    {
        echo "Знак уже занят. Неверный пароль.";
        exit;
    }
}

if ((($XO == 'X') && ($gameArray["xConnected"] == "false")) ||
    (($XO == 'O') && ($gameArray["oConnected"] == "false")))
{
    if ($XO == "X") {
        echo "{\"xPass\":\"" . $gameArray["xPass"] . "\"}";

        $updateQuery  = "UPDATE `xoxGames` SET ";
        $updateQuery .= "`xConnected`=\"true\" ";
        $updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";
    }

    if ($XO == "O") {
        echo "{\"oPass\":\"" . $gameArray["oPass"] . "\"}";

        $updateQuery  = "UPDATE `xoxGames` SET ";
        $updateQuery .= "`oConnected`=\"true\" ";
        $updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";
    }

    $database->query($updateQuery);
}


