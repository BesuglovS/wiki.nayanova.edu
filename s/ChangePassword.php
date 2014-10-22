<?php
$dbPrefix = "s_";

$login = $_POST["Login"];
$pass = $_POST["Pass"];

require_once("../_php/includes/Database.php");


$updateQuery  = "UPDATE " . $dbPrefix . "LoginAccounts SET Password='" . $pass . "' ";
$updateQuery .= "WHERE Login='" . $login . "'";

echo $updateQuery;

$database->query($updateQuery);