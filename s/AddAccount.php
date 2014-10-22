<?php
$dbPrefix = "s_";

$login = $_POST["Login"];
$pass = $_POST["Pass"];

require_once("../_php/includes/Database.php");


$insertQuery  = "INSERT INTO " . $dbPrefix . "LoginAccounts (Login ,Password)";
$insertQuery .= "VALUES (\"" . $login . "\", \"" . $pass . "\")";

$database->query($insertQuery);