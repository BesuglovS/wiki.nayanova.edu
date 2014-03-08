<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");

global $database;

$query = "SELECT StudentGroupId, `Name` FROM studentGroups";

$groupList = $database->query($query);

$studentGroups = array();
while($group = $groupList->fetch_assoc())
{
    $studentGroups[$group["StudentGroupId"]] = $group["Name"];
}
asort($studentGroups);

echo "<select id=\"groupsList\">";
foreach ($studentGroups as $groupId => $groupName)
{
    echo "<option value=\"";
    echo $groupId;
    echo "\">";
    echo $groupName;
    echo "</option>";
}

echo "</select>";

echo "<span id=\"progress\"></span>";

echo "<br /><br />";
echo "<div id=\"groupList\"></div>";

?>