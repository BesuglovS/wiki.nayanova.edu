<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$startFrom = $_GET["startFrom"];

$query  = "SELECT statId, groupId, date, statDate ";
$query .= "FROM `DailyScheduleStats` ";
$query .= "ORDER BY statDate DESC ";
$query .= "LIMIT " . $startFrom . ", 100";

$events = $database->query($query);

if ($events->num_rows != 0)
{
    echo "<table class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дата и время запроса</td>";
    echo "<td>Группа</td>";
    echo "<td>Дата запрашиваемого расписания</td>";
    echo "</tr>";
    while($event = $events->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        echo $event["statDate"];
        echo "</td>";
        echo "<td>";
        echo $event["groupId"];
        echo "</td>";
        echo "<td>";
        echo $event["date"];
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