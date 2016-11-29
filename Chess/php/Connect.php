<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$pass = $_GET["password"];
$wb = $_GET["wb"];

$query = "SELECT * FROM `chessGames` WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

$gameString="";

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();
if ((($wb == 'w') && ($gameArray["wConnected"] == "true")) ||
    (($wb == 'b') && ($gameArray["bConnected"] == "true")))
{
    if ((($wb == "w") && $pass == $gameArray["wPass"]) ||
        (($wb == "b") && $pass == $gameArray["bPass"])) {
        echo "Success";
        exit;
    }
    else
    {
        echo "Знак уже занят. Неверный пароль.";
        exit;
    }
}

//echo "<pre>";
//echo print_r($gameArray);
//echo "</pre>";
//echo "bConnected:" . $gameArray["bConnected"] == "false";
//exit;

if ((($wb == 'w') && ($gameArray["wConnected"] == "false")) ||
    (($wb == 'b') && ($gameArray["bConnected"] == "false")))
{
    if ($wb == "w") {
        echo "{\"wPass\":\"" . $gameArray["wPass"] . "\"}";

        $updateQuery  = "UPDATE `chessGames` SET ";
        $updateQuery .= "`wConnected`=\"true\" ";
        $updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";
    }

    if ($wb == "b") {
        echo "{\"bPass\":\"" . $gameArray["bPass"] . "\"}";

        $updateQuery  = "UPDATE `chessGames` SET ";
        $updateQuery .= "`bConnected`=\"true\" ";
        $updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";
    }

    $database->query($updateQuery);
}


