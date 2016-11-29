<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupName = $_GET["groupName"];
//$groupName=iconv("WINDOWS-1251", "UTF-8", $groupName);
$dateString = $_GET["date"];



$groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT studentsInGroups.StudentId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "JOIN studentGroups ";
$groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
$groupsQuery .= "WHERE studentGroups.Name = ". $groupName ." ";
$groupsQuery .= ")";
$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$query  = "SELECT rings.Time, disciplines.Name, teachers.FIO, auditoriums.Name ";
$query .= "FROM lessons ";
$query .= "JOIN calendars ";
$query .= "ON lessons.CalendarId = calendars.CalendarId ";
$query .= "JOIN rings ";
$query .= "ON lessons.RingId = rings.RingId ";
$query .= "JOIN auditoriums ";
$query .= "ON lessons.AuditoriumId = auditoriums.AuditoriumID ";
$query .= "JOIN teacherForDisciplines ";
$query .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
$query .= "JOIN teachers ";
$query .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
$query .= "JOIN disciplines ";
$query .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
$query .= "JOIN studentGroups ";
$query .= "ON disciplines.StudentGroupId = studentGroups.StudentGroupId ";
$query .= "WHERE lessons.IsActive=1 ";
$query .= "AND (" . $groupCondition . ") ";
$query .= "AND calendars.Date = " . $dateString . " ";
$query .= "ORDER BY rings.Time ASC";

$lessonsList = $database->query($query);

if ($lessonsList->num_rows != 0)
{
    echo "<table class=\"DailySchedule\">";
    while($lesson = $lessonsList->fetch_row())
    {
        echo "<tr>";
        echo "<td>";
        echo substr($lesson[0], 0, strlen($lesson[0])-3);
        echo "</td>";
        echo "<td>";
        echo $lesson[1];
        echo "</td>";
        echo "<td>";
        echo $lesson[2];
        echo "</td>";
        echo "<td>";
        echo $lesson[3];
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