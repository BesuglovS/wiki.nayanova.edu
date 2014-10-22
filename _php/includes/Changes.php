<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["groupId"];
$tomorrow = $_GET["tomorrow"];
$dateString = $_GET["date"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
$groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
$groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = '". $groupId ."' ";
$groupsQuery .= ")";

$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

$tomorrowDateString  = date("Y") . "-" . date("m") . "-" . (date("d")+1);

$query  = "SELECT " . $dbPrefix . "lessonLogEvents.DateTime ";
$query .= "FROM " . $dbPrefix . "lessonLogEvents ";

$query .= "LEFT JOIN " . $dbPrefix . "lessons AS newLesson ";
$query .= "ON " . $dbPrefix . "lessonLogEvents.NewLessonId = newLesson.LessonId ";
$query .= "LEFT JOIN " . $dbPrefix . "calendars AS newCalendar ";
$query .= "ON newLesson.CalendarId = newCalendar.CalendarId ";
$query .= "LEFT JOIN " . $dbPrefix . "teacherForDisciplines AS newTFD ";
$query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "disciplines AS newDics ";
$query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "studentGroups AS newGroup ";
$query .= "ON newDics.StudentGroupId = newGroup.StudentGroupId ";

$query .= "LEFT JOIN " . $dbPrefix . "lessons AS oldLesson ";
$query .= "ON " . $dbPrefix . "lessonLogEvents.OldLessonId = oldLesson.LessonId ";
$query .= "LEFT JOIN " . $dbPrefix . "calendars AS oldCalendar ";
$query .= "ON oldLesson.CalendarId = oldCalendar.CalendarId ";
$query .= "LEFT JOIN " . $dbPrefix . "teacherForDisciplines AS oldTFD ";
$query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "disciplines AS oldDics ";
$query .= "ON oldTFD.DisciplineId = oldDics.DisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "studentGroups AS oldGroup ";
$query .= "ON oldDics.StudentGroupId = oldGroup.StudentGroupId ";

$query .= "WHERE ((oldDics.StudentGroupId " . $groupCondition . ") ";
if ($tomorrow == "true")
{
    $query .= " AND ( oldCalendar.Date = \"" . $dateString . "\")";
}
$query .= ") ";
$query .= "OR ((newDics.StudentGroupId " . $groupCondition . ") ";
if ($tomorrow == "true")
{
    $query .= " AND ( newCalendar.Date = \"" . $dateString . "\")";
}
$query .= ") ";
$query .= "ORDER BY " . $dbPrefix . "lessonLogEvents.DateTime DESC ";
$query .= ", oldCalendar.Date DESC";

$events = $database->query($query);

$eventCount = $events->num_rows;

if ($eventCount == 0)
{
    echo Utilities::TagMessage("Событий нет.");
    exit;
}

$counter = 0;
$indexArray = array();
$eventRangeStarts = array();
$eventRangeEnds = array();
while(($event = $events->fetch_assoc()) != null)
{
    if($counter % 10 == 0)
    {
        $eventRangeStarts[] = $event["DateTime"];
        $indexArray[] = $counter;
    }

    if (($counter % 10 == 9) || ($counter == $eventCount-1))
    {
        $eventRangeEnds[] = $event["DateTime"];
    }

    $counter++;
}


echo "<select id=\"eventsIndexList\">";
for($i = 0; $i < count($eventRangeStarts); $i++)
{
    $start = $eventRangeStarts[$i];
    $end = $eventRangeEnds[$i];

    echo "<option value=\"";
    echo $indexArray[$i];
    echo "\">";
    echo $start . " - " . $end;
    echo "</option>";
}
echo "</select>";

echo "<span id=\"progress\"></span>";

echo "<br /><br />";
echo "<div id=\"eventList\"></div>";
?>