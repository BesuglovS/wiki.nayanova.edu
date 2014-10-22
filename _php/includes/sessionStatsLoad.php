<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$startFrom = $_GET["startFrom"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupsQuery = "SELECT StudentGroupId, Name FROM " . $dbPrefix . "studentGroups";
$groupsResult = $database->query($groupsQuery);

$groups = array();
while ($group = $groupsResult->fetch_assoc())
{
    $groups[$group["StudentGroupId"]] = $group["Name"];
}


$query  = "SELECT DateTime, GroupId ";
$query .= "FROM " . $dbPrefix . "sessionStats ";
$query .= "ORDER BY DateTime DESC ";
$query .= "LIMIT " . $startFrom . ", 100";

$events = $database->query($query);

if ($events->num_rows != 0)
{
    echo "<table class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дата и время запроса</td>";
    echo "<td>Группа</td>";
    echo "</tr>";
    while($event = $events->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        echo $event["DateTime"];
        echo "</td>";
        echo "<td>";
        echo $groups[$event["GroupId"]];
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo Utilities::NothingISThereString();
}
?>