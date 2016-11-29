<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

$gameId = $_GET["id"];
$wb = $_GET["wb"];
$pass = $_GET["password"];

$Fen = $_GET["FEN"];
$History = $_GET["History"];

$updateQuery  = "UPDATE `chessGames` SET ";
$updateQuery .= "`Field`=\"" . $Fen . "\", ";
$updateQuery .= "`History`=\"" . $database->real_escape_string($History) . "\" ";
$updateQuery .= "WHERE `GameIdName`=\"" . $gameId . "\" ";

$database->query($updateQuery);

echo "Success" . $History;

