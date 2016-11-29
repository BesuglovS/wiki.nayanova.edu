<?php
require_once("_php/includes/Database.php");

$query  = "SELECT `UserAgent`, count(`UserAgent`) FROM LoginLog  GROUP by `UserAgent` ";

$agents = $database->query($query);
$result = array();
while ($agent = $agents->fetch_assoc())
{
    $result[] = $agent["UserAgent"];
	echo $agent["count(`UserAgent`)"];
	echo "@@@";
	echo $agent["UserAgent"];
	echo "<br />";
}

// echo "<pre>";
// echo print_r($result);
// echo "</pre>";