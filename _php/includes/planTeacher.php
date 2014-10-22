<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$teacherId = $_GET["id"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$query  = "SELECT " . $dbPrefix . "disciplines.Name, " . $dbPrefix . "disciplines.Attestation, " . $dbPrefix . "disciplines.AuditoriumHours, ";
$query .= $dbPrefix . "studentGroups.Name  AS GroupName, " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId as TFDID ";
$query .= "FROM " . $dbPrefix . "teacherForDisciplines ";
$query .= "JOIN " . $dbPrefix . "disciplines ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
$query .= "JOIN " . $dbPrefix . "studentGroups ";
$query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$query .= "JOIN " . $dbPrefix . "teachers ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
$query .= "WHERE " . $dbPrefix . "teachers.TeacherId = " . $teacherId;

$discList = $database->query($query);

$result = array();
while($disc = $discList->fetch_assoc())
{
    $result[$disc["TFDID"]] = array();
    $result[$disc["TFDID"]]["Name"] = $disc["Name"];
    $result[$disc["TFDID"]]["AuditoriumHours"] = $disc["AuditoriumHours"];
    $result[$disc["TFDID"]]["Attestation"] = $disc["Attestation"];
    $result[$disc["TFDID"]]["GroupName"] = $disc["GroupName"];
}

$monthsArray = array();

foreach ($result as $tfdId => $discData)
{
    $tfdLessonsDates  = "SELECT " . $dbPrefix . "calendars.Date ";
    $tfdLessonsDates .= "FROM " . $dbPrefix . "lessons ";
    $tfdLessonsDates .= "JOIN " . $dbPrefix . "calendars ";
    $tfdLessonsDates .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
    $tfdLessonsDates .= "WHERE " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $tfdId . " ";
    $tfdLessonsDates .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

    $tfdLessonsDatesResult = $database->query($tfdLessonsDates);

    $lessonsByMonth = array();
    while($lesson = $tfdLessonsDatesResult->fetch_assoc()) {
        $month = mb_substr($lesson["Date"], 5, 2, "UTF-8");
        if (!in_array($month, $monthsArray))
        {
            $monthsArray[] =  $month;
        }
        if (!array_key_exists($month, $lessonsByMonth))
        {
            $lessonsByMonth[$month] = 0;
        }
        $lessonsByMonth[$month]++;
    }
    $queryData = $database->query($tfdLessonsDates);
    $row = $queryData->fetch_assoc();

    $result[$tfdId]["hoursCount"] = array_sum($lessonsByMonth);
    foreach ($lessonsByMonth as $month => $hours) {
        $result[$tfdId]["m" . $month] = $hours;
    }
}
asort($monthsArray);

if ($discList->num_rows != 0)
{
    echo "<table id=\"discTable\" class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дисциплина</td>";
    echo "<td>Группа</td>";
    echo "<td>Часов по плану</td>";
    echo "<td>Часов в расписании</td>";
    foreach ($monthsArray as $month) {
        echo "<td>";
        echo $month;
        echo "</td>";
    }
    echo "<td>Отчётность</td>";
    echo "</tr>";
    foreach ($result as $tfdId => $discData)
    {
        echo "<tr>";
        echo "<td title=\"";
        echo $discData["teacherFIO"];
        echo "\">";
        echo $discData["Name"];
        echo "</td>";
        echo "<td>";
        echo $discData["GroupName"];
        echo "</td>";
        echo "<td>";
        echo $discData["AuditoriumHours"];
        echo "</td>";
        echo "<td style=\"background: " . Utilities::GetPercentColorString($discData["AuditoriumHours"],$discData["hoursCount"]*2 ) ."\">";
        echo $discData["hoursCount"]*2;
        echo "</td>";
        foreach ($monthsArray as $month) {
            echo "<td>";
            if (array_key_exists("m" . $month, $discData))
            {
                echo $discData["m" . $month] * 2;
            }
            else
            {
                echo "-";
            }
            echo "</td>";
        }
        echo "<td>";
        echo Utilities::$Attestation[$discData["Attestation"]];
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