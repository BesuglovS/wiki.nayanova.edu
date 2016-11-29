<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$date = $_GET["date"];
//$schedulePrefix = "old_";
$schedulePrefix = "";

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once("Database.php");

global $database;

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . $dbPrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}

$query  = "SELECT ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId, ";
$query .= $schedulePrefix . $dbPrefix . "disciplines.Name as DisciplineName, ";
$query .= $schedulePrefix . $dbPrefix . "teachers.FIO, " . $schedulePrefix . $dbPrefix . "studentGroups.Name as GroupName ";
$query .= "FROM " . $dbPrefix . "exams ";
$query .= "JOIN " . $schedulePrefix . $dbPrefix . "disciplines ";
$query .= "ON " . $dbPrefix . "exams.DisciplineId = " . $schedulePrefix . $dbPrefix . "disciplines.DisciplineId ";
$query .= "JOIN " . $schedulePrefix . $dbPrefix . "studentGroups ";
$query .= "ON " . $schedulePrefix . $dbPrefix . "disciplines.StudentGroupId = " . $schedulePrefix . $dbPrefix . "studentGroups.StudentGroupId ";
$query .= "JOIN " . $schedulePrefix . $dbPrefix . "teacherForDisciplines  ";
$query .= "ON " . $schedulePrefix . $dbPrefix . "disciplines.DisciplineId = " . $schedulePrefix . $dbPrefix . "teacherForDisciplines.DisciplineId ";
$query .= "JOIN " . $schedulePrefix . $dbPrefix . "teachers  ";
$query .= "ON " . $schedulePrefix . $dbPrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . $dbPrefix . "teachers.TeacherId ";
$query .= "WHERE IsActive = 1";

$examList = $database->query($query);

$eventList = array();
while($exam = $examList->fetch_assoc())
{
    $consDate = explode(" ", $exam["ConsultationDateTime"]);
    $consDate = $consDate[0];
    $consTime = mb_substr($exam["ConsultationDateTime"], mb_strlen($consDate) + 1);

    $examDate = explode(" ", $exam["ExamDateTime"]);
    $examDate = $examDate[0];
    $examTime = mb_substr($exam["ExamDateTime"], mb_strlen($examDate) + 1);

    if (($consDate == $date) || ($examDate == $date))
    {
        $event = array();

        if ($consDate == $date)
        {
            $event["Auditorium"] = $auditoriums[$exam["ConsultationAuditoriumId"]];
            $event["type"] = "К";
            $event["Time"] = $consTime;
        }
        if ($examDate == $date)
        {
            $event["Auditorium"] = $auditoriums[$exam["ExamAuditoriumId"]];
            $event["type"] = "Э";
            $event["Time"] = $examTime;
        }

        $event["disciplineName"] = $exam["DisciplineName"];
        $event["teacherFIO"] = $exam["FIO"];
        $event["groupName"] = $exam["GroupName"];

        $eventList[] = $event;
    }
}

function events_sort($a, $b) {
    $aAud = $a["Auditorium"];
    $bAud = $b["Auditorium"];

    return strcmp($aAud, $bAud);
}

usort($eventList, 'events_sort');

if (count($eventList) != 0)
{
    echo "<table id=\"studentListTable\" class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td style=\"text-align:center\">";
    echo "Аудитория";
    echo "</td>";
    echo "<td>";
    echo "К / Э";
    echo "</td>";
    echo "<td>";
    echo "Время";
    echo "</td>";
    echo "<td>";
    echo "Дисциплина";
    echo "</td>";
    echo "<td>";
    echo "Группа";
    echo "</td>";
    echo "<td>";
    echo "Преподаватель";
    echo "</td>";
    echo "</tr>";
    for ($i = 0; $i < count($eventList); $i++)
    {
        echo "<tr>";
        echo "<td>";
        echo $eventList[$i]["Auditorium"];
        echo "</td>";
        if ($eventList[$i]["type"] == "К")
        {
            echo "<td style=\"background:#ff0\">";
        }
        if ($eventList[$i]["type"] == "Э")
        {
            echo "<td style=\"background:#0f0\">";
        }
        echo $eventList[$i]["type"];
        echo "</td>";
        echo "<td>";
        echo $eventList[$i]["Time"];
        echo "</td>";
        echo "<td>";
        echo $eventList[$i]["disciplineName"];
        echo "</td>";
        echo "<td>";
        echo $eventList[$i]["groupName"];
        echo "</td>";
        echo "<td>";
        echo $eventList[$i]["teacherFIO"];
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo Utilities::NothingISThereString();
}
