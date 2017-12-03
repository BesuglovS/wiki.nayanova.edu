<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["id"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$schoolGroup = false;
$smallLessons = false;

$groupNameQuery = "SELECT `Name` FROM " . $dbPrefix ."studentGroups WHERE StudentGroupId = " . $groupId;
$groupNameQueryResult = $database->query($groupNameQuery);
$groupNameQueryResultAssoc = $groupNameQueryResult->fetch_assoc();
$groupName = $groupNameQueryResultAssoc["Name"];
$groupNamePieces = explode(" ", $groupName);
$groupNameNumber = $groupNamePieces[0];
if (is_numeric($groupNameNumber) && intval($groupNameNumber) < 12) {
    $schoolGroup = true;
}
if (is_numeric($groupNameNumber) && intval($groupNameNumber) < 8) {
    $smallLessons = true;
}

$groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
$groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
$groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $groupId ." ";
$groupsQuery .= ")";
$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = "WHERE " . $dbPrefix . "studentGroups.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$query  = "SELECT DISTINCT " . $dbPrefix . "disciplines.Name, " . $dbPrefix . "disciplines.Attestation, " . $dbPrefix . "disciplines.AuditoriumHours, ";
$query .= $dbPrefix . "studentGroups.Name  AS GroupName, " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId as TFDID, ";
$query .= $dbPrefix . "disciplines.AuditoriumHoursPerWeek, ";
$query .= $dbPrefix . "teachers.FIO ";
$query .= "FROM " . $dbPrefix . "disciplines ";
$query .= "LEFT JOIN " . $dbPrefix . "studentGroups ";
$query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$query .= "LEFT JOIN " . $dbPrefix . "teacherForDisciplines ";
$query .= "ON " . $dbPrefix . "disciplines.DisciplineId = " . $dbPrefix . "teacherForDisciplines.DisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "teachers ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId =  " . $dbPrefix . "teachers.TeacherId ";

$query .= $groupCondition;

$discList = $database->query($query);

$FakeTFD = -2;

$result = array();
while($disc = $discList->fetch_assoc())
{
    if ($disc["TFDID"] == "")
    {
        $disc["TFDID"] = $FakeTFD;
        $FakeTFD--;
    }

    $result[$disc["TFDID"]] = array();
    $result[$disc["TFDID"]]["Name"] = $disc["Name"];
    $result[$disc["TFDID"]]["AuditoriumHours"] = $schoolGroup ?
        $disc["AuditoriumHoursPerWeek"] : $disc["AuditoriumHours"];
    $result[$disc["TFDID"]]["Attestation"] = $disc["Attestation"];
    $result[$disc["TFDID"]]["GroupName"] = $disc["GroupName"];
    $result[$disc["TFDID"]]["teacherFIO"] = $disc["FIO"];
}

//echo "<pre>";
//echo print_r($result);
//echo "</pre>";

foreach ($result as $tfdId => $discData)
{
    $hoursQuery  = "SELECT COUNT(*) AS lesCount ";
    $hoursQuery .= "FROM " . $dbPrefix . "lessons ";
    $hoursQuery .= "WHERE " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $tfdId . " ";
    $hoursQuery .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

    $queryData = $database->query($hoursQuery);
    $row = $queryData->fetch_assoc();

    $result[$tfdId]["hoursCount"] = $smallLessons ? $row["lesCount"] : $row["lesCount"]*2;
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
    echo "<td>Часов по плану";
    if ($schoolGroup) {
        echo " в неделю";
    }
    echo "</td>";
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