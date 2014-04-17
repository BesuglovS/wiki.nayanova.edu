<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$startFrom = $_GET["startFrom"];


$query  = "SELECT DateTime, F, I, O, RemoteAddr ";
$query .= "FROM LoginLog ";
$query .= "JOIN students ";
$query .= "ON LoginLog.StudentId = students.StudentId ";
$query .= "ORDER BY DateTime DESC ";
$query .= "LIMIT " . $startFrom . ", 100";

$events = $database->query($query);

if ($events->num_rows != 0)
{
    echo "<table class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дата и время логина</td>";
    echo "<td>ID студента</td>";
    echo "<td>\$_SERVER['REMOTE_ADDR']</td>";
    echo "</tr>";
    while($event = $events->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        echo $event["DateTime"];
        echo "</td>";

        echo "<td>";
        echo $event["F"] . " " . mb_substr($event["I"], 0, 2) . mb_substr($event["O"], 0, 2);
        echo "</td>";
        echo "<td>";
        echo $event["RemoteAddr"];
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