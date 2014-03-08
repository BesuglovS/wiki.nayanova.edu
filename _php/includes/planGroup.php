<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupId = $_GET["id"];

$groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT studentsInGroups.StudentId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "JOIN studentGroups ";
$groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
$groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $groupId ." ";
$groupsQuery .= ")";
$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = "WHERE studentGroups.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$query  = "SELECT DISTINCT disciplines.Name, disciplines.Attestation, disciplines.AuditoriumHours, ";
$query .= "studentGroups.Name  AS GroupName, teacherForDisciplines.TeacherForDisciplineId as TFDID, ";
$query .= "teachers.FIO ";
$query .= "FROM disciplines ";
$query .= "JOIN studentGroups ";
$query .= "ON disciplines.StudentGroupId = studentGroups.StudentGroupId ";
$query .= "JOIN teacherForDisciplines ";
$query .= "ON disciplines.DisciplineId = teacherForDisciplines.DisciplineId ";
$query .= "JOIN teachers ";
$query .= "ON teacherForDisciplines.TeacherId =  teachers.TeacherId ";

$query .= $groupCondition;

$discList = $database->query($query);

$result = array();
while($disc = $discList->fetch_assoc())
{
    $result[$disc["TFDID"]] = array();
    $result[$disc["TFDID"]]["Name"] = $disc["Name"];
    $result[$disc["TFDID"]]["AuditoriumHours"] = $disc["AuditoriumHours"];
    $result[$disc["TFDID"]]["Attestation"] = $disc["Attestation"];
    $result[$disc["TFDID"]]["GroupName"] = $disc["GroupName"];
    $result[$disc["TFDID"]]["teacherFIO"] = $disc["FIO"];
}

foreach ($result as $tfdId => $discData)
{
    $hoursQuery  = "SELECT COUNT(*) AS lesCount ";
    $hoursQuery .= "FROM lessons ";
    $hoursQuery .= "WHERE lessons.TeacherForDisciplineId = " . $tfdId . " ";
    $hoursQuery .= "AND lessons.isActive = 1 ";

    $queryData = $database->query($hoursQuery);
    $row = $queryData->fetch_assoc();

    $result[$tfdId]["hoursCount"] = $row["lesCount"]*2;
}

function discNameCompare($a, $b) {
    return strcmp($a["Name"], $b["Name"]);
}

uasort($result, 'discNameCompare');

if ($discList->num_rows != 0)
{
    echo "<table id=\"discTable\" class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дисциплина</td>";
    echo "<td>Группа</td>";
    echo "<td>Часов по плану</td>";
    echo "<td>Часов в расписании</td>";
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
        echo "<td style=\"background: " . Utilities::GetPercentColorString($discData["AuditoriumHours"],$discData["hoursCount"] ) ."\">";
        echo $discData["hoursCount"];
        echo "</td>";
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