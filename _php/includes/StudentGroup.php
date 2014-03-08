<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupId = $_GET["id"];

$query  = "SELECT students.F, students.I, students.O, students.Expelled ";
$query .= "FROM students ";
$query .= "JOIN studentsInGroups ";
$query .= "ON students.StudentId = studentsInGroups.StudentId ";
$query .= "WHERE studentsInGroups.StudentGroupId = " . $groupId . " ";
$query .= "AND students.Expelled = false ";
$query .= "ORDER BY students.F ";

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