<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];

require_once("Database.php");

global $database;

$query = "SELECT StudentGroupId, `Name` FROM " . $dbPrefix . "studentGroups ORDER BY `Name`";

$groupList = $database->query($query);

echo "<select id=\"groupsPlanList\">";
while($group = $groupList->fetch_assoc())
{
    if ((!(strpos($group["Name"], '+') !== FALSE)) && (!(strpos($group["Name"], 'I') !== FALSE)))
    {
        echo "<option value=\"";
        echo $group["StudentGroupId"];
        echo "\">";
        echo $group["Name"];
        echo "</option>";
    }
}

echo "</select>";

echo "<span id=\"progress\"></span>";

echo "<br /><br />";
echo "<div id=\"planGroup\"></div>";

?>