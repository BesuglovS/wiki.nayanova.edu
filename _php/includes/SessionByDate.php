<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");

global $database;

$schedulePrefix = "old_";

$date = $_GET["date"];

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}

$query  = "SELECT ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId, ";
$query .= $schedulePrefix . "disciplines.Name as DisciplineName, ";
$query .= $schedulePrefix . "teachers.FIO, " . $schedulePrefix . "studentGroups.Name as GroupName ";
$query .= "FROM exams ";
$query .= "JOIN " . $schedulePrefix . "disciplines ";
$query .= "ON exams.DisciplineId = " . $schedulePrefix . "disciplines.DisciplineId ";
$query .= "JOIN " . $schedulePrefix . "studentGroups ";
$query .= "ON " . $schedulePrefix . "disciplines.StudentGroupId = " . $schedulePrefix . "studentGroups.StudentGroupId ";
$query .= "JOIN " . $schedulePrefix . "teacherForDisciplines  ";
$query .= "ON " . $schedulePrefix . "disciplines.DisciplineId = " . $schedulePrefix . "teacherForDisciplines.DisciplineId ";
$query .= "JOIN " . $schedulePrefix . "teachers  ";
$query .= "ON " . $schedulePrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . "teachers.TeacherId ";
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
