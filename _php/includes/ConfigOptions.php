<?php
require_once("Database.php");
global $database;
global $dbPrefix;

$configsTableName = $dbPrefix . "configs";

$query  = "SELECT ". $configsTableName . ".Key, " . $configsTableName . ".Value FROM " . $configsTableName;

$result = $database->query($query);

$options = array();
while ($option = $result->fetch_assoc())
{
    $options[$option["Key"]] = $option["Value"];
}
?>