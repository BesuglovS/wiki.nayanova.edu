<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$startFrom = $_GET["startFrom"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$query  = "SELECT DateTime, F, I, O, RemoteAddr, UserAgent, AltUserId ";
$query .= "FROM " . $dbPrefix . "LoginLog ";
$query .= "LEFT JOIN " . $dbPrefix . "students ";
$query .= "ON " . $dbPrefix . "LoginLog.UserId = " . $dbPrefix . "students.StudentId ";
$query .= "ORDER BY DateTime DESC ";
$query .= "LIMIT " . $startFrom . ", 100";

$events = $database->query($query);

if ($events->num_rows != 0)
{
    echo "<table class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дата и время логина</td>";
    echo "<td>USER ID</td>";
    echo "<td>\$_SERVER<br />['REMOTE_ADDR']</td>";
    echo "<td>\$_SERVER<br />['HTTP_USER_AGENT']</td>";
    echo "</tr>";
    while($event = $events->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        echo $event["DateTime"];
        echo "</td>";

        echo "<td>";
        $FIO = $event["F"] . " " . mb_substr($event["I"], 0, 1) . mb_substr($event["O"], 0, 1);
        echo $FIO;
        if ($event["AltUserId"] != "")
        {
            if ($FIO != "")
            {
                echo "<br />";
            }
            echo $event["AltUserId"];
        }

        echo "</td>";
        echo "<td>";
        echo $event["RemoteAddr"];
        echo "</td>";
        echo "<td>";
        echo $event["UserAgent"];
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