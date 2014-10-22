<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["id"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$query  = "SELECT F, I, O, Expelled ";
$query .= "FROM " . $dbPrefix . "students ";
$query .= "JOIN " . $dbPrefix . "studentsInGroups ";
$query .= "ON " . $dbPrefix . "students.StudentId = " . $dbPrefix . "studentsInGroups.StudentId ";
$query .= "WHERE " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $groupId . " ";
$query .= "AND " . $dbPrefix . "students.Expelled = false ";
$query .= "ORDER BY " . $dbPrefix . "students.F ";

$studentList = $database->query($query);


if ($studentList->num_rows != 0)
{
    echo "<table id=\"studentListTable\" class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td style=\"text-align:center\">";
    echo "Ф.И.О.";
    echo "</td>";
    echo "</tr>";
    while($student = $studentList->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        echo $student["F"] . " " . $student["I"] . " " . $student["O"];
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