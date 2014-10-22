<?php
$dbPrefix = "s_";

$login = $_POST["Login"];

require_once("../_php/includes/Database.php");

$removeQuery  = "DELETE FROM " . $dbPrefix . "LoginAccounts ";
$removeQuery .= "WHERE Login = '" . $login . "'";

$database->query($removeQuery);