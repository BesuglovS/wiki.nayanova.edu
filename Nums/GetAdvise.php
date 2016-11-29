<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
include_once "ToolBox.php";

global $database;

$gameId = $_GET["id"];
$side = $_GET["side"];

$query = "SELECT * FROM numsGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();

$moves = array();

if ($side == "1") {
    $moves = str_split($gameArray["Moves1"], 7);
    if ($gameArray["Moves1"] == "") {
        $moves = array();
    }
}
if ($side == "2") {
    $moves = str_split($gameArray["Moves2"], 7);
    if ($gameArray["Moves2"] == "") {
        $moves = array();
    }
}

$numsList = array();

for ($i = 1234; $i < 98766; $i++)
{
    $good = "true";
    $num = $i;
    if (strlen($num) == 4)
    {
        $num = "0" . $num;
    }

    if ((isCorrect($num) == "false"))
    {
        $good = "false";
    }


    if ($good == "true") {
        for ($j = 0; $j < count($moves); $j++) {
            $guess = substr($moves[$j], 0, 5);
            $moveCount = MakeCount($guess, $num);
            $recordedCount = substr($moves[$j], 5, 2);

            if ($moveCount !== $recordedCount) {
                $good = "false";
                break;
            }
        }
    }

    if ($good == "true")
    {
        $numsList[] = $num;
    }

}

$NumsCount = count($numsList);

$Num = "";
if ($NumsCount > 0)
{
    $Num =  $numsList[array_rand($numsList)];
}

echo "{\"Count\":\"" . $NumsCount . "\", \"Num\":\"" .  $Num . "\"}";
