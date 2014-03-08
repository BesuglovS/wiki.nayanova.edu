<?php
require_once("Database.php");
global $database;

$query  = "SELECT configs.Key, configs.Value FROM `configs`";

$result = $database->query($query);

$options = array();
while ($option = $result->fetch_assoc())
{
    $options[$option["Key"]] = $option["Value"];
}
?>