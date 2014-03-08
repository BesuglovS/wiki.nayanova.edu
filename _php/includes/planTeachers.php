<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");

global $database;

$query = "SELECT TeacherId, FIO FROM teachers";

$teachersList = $database->query($query);

$tList = array();
while ($teacher = $teachersList->fetch_assoc())
{
    $tList[$teacher["TeacherId"]] = $teacher["FIO"];
}
asort($tList);

echo "<select id=\"teachersPlanList\">";
foreach ($tList as $id => $FIO)
{
    echo "<option value=\"";
    echo $id;
    echo "\">";
    echo $FIO;
    echo "</option>";
}

echo "</select>";

echo "<span id=\"progress\"></span>";

echo "<br /><br />";
echo "<div id=\"planTeacher\"></div>";

?>