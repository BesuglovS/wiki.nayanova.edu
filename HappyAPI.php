<?php
include_once "_php/includes/Database.php";

global $database;

$queryDate = '-' . date("m") . "-" . date("d");

if (isset($_GET["Date"]))
{
    $queryDate = $_GET["Date"];
}

$query = "SELECT COUNT(*) FROM students WHERE Expelled=0 AND BirthDate LIKE '%" . $queryDate . "'";

$queryResult = $database->query($query);

$data = $queryResult->fetch_assoc();

$result = array();
$result["HappyCount"] = $data["COUNT(*)"];

echo json_encode($result);