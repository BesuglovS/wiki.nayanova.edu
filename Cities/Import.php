<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;

$rest_json = file_get_contents("php://input");
$data = json_decode($rest_json, true);

$query  = "INSERT INTO cities (Name) VALUES ( ? )";
$database->prepare($query);

foreach ($data as $city) {
    $database->bindAndExecute("s", $city);
}

//echo "<pre>";
//echo print_r($data);
//echo "</pre>";
echo "OK";