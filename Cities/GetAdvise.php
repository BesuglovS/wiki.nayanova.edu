<?php
mb_internal_encoding('utf-8');

include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$side = $_GET["side"];

$query = "SELECT * FROM citiesGames WHERE GameIdName = \"" . $gameId . "\"";

$result = $database->query($query);

if ($result->num_rows == 0) {
    echo "Игры с таким именем не существует.";
    exit;
}

$gameArray = $result->fetch_assoc();
$moves1 = explode("@", $gameArray["Moves1"]);
$moves2 = explode("@", $gameArray["Moves1"]);

$opponentMoves = "";
if ($side == "1")
{
    $opponentMoves = $gameArray["Moves2"];

}
if ($side == "2")
{
    $opponentMoves = $gameArray["Moves1"];
}

if ($opponentMoves != "") {
    $lastMove = end(explode('@', $opponentMoves));

    $queryLastMoveString = "SELECT * FROM cities WHERE cityId = " . $lastMove;
    $qResult = $database->query($queryLastMoveString);

    $lastMoveObject = $qResult->fetch_assoc();
    $lastMoveString = $lastMoveObject["Name"];

    $lastLetter = mb_substr($lastMoveString, -1);
    $transitLetter = mb_strtoupper($lastLetter);

    $result = array();
    $likeQuery = "SELECT * FROM `cities` WHERE `Name` LIKE '" . $transitLetter . "%'";
    $qResult = $database->query($likeQuery);

    $advice = array();
    if ($qResult->num_rows > 0) {
        $advice["count"] = $qResult->num_rows;

        while($city = $qResult->fetch_assoc())
        {
            $result[] = $city["Name"];
        }

        $advice["city"] = $result[array_rand($result)];
    }
    else
    {
        $advice["count"] = 0;
        $advice["city"] = "";
    }

    echo json_encode($advice);
}