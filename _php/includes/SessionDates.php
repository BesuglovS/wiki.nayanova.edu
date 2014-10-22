<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];

require_once("Database.php");

global $database;

$today  = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
$todayString =  gmdate("d.m.y", $today);

$query  = "SELECT * ";
$query .= "FROM " . $dbPrefix . "exams ";
$query .= "WHERE IsActive = 1";

$examList = $database->query($query);

$dateList = array();
while($exam = $examList->fetch_assoc())
{
    $consDate = explode(" ", $exam["ConsultationDateTime"]);
    $consDate = $consDate[0];

    $examDate = explode(' ', $exam["ExamDateTime"]);
    $examDate = $examDate[0];

    if (!in_array($consDate, $dateList))
    {
        $dateList[] = $consDate;
    }
    if (!in_array($examDate, $dateList))
    {
        $dateList[] = $examDate;
    }
}

asort($dateList);

echo "<select id=\"sessionDate\">";
foreach ($dateList as $date)
{
    echo "<option ";
    echo" value=\"";
    echo $date;
    echo "\">";
    echo $date;
    echo "</option>";
}

echo "</select>";

echo "<span id=\"progress\"></span>";

echo "<br /><br />";
echo "<div id=\"SessionList\"></div>";

?>