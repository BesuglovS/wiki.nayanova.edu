<?php

header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupId = $_GET["groupId"];
$tomorrow = $_GET["tomorrow"];
$dateString = $_GET["date"];

$groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT studentsInGroups.StudentId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "JOIN studentGroups ";
$groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
$groupsQuery .= "WHERE studentGroups.StudentGroupId = '". $groupId ."' ";
$groupsQuery .= ")";

$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

$tomorrowDateString  = date("Y") . "-" . date("m") . "-" . (date("d")+1);

$query  = "SELECT lessonLogEvents.DateTime ";
$query .= "FROM lessonLogEvents ";

$query .= "LEFT JOIN lessons AS newLesson ";
$query .= "ON lessonLogEvents.NewLessonId = newLesson.LessonId ";
$query .= "LEFT JOIN calendars AS newCalendar ";
$query .= "ON newLesson.CalendarId = newCalendar.CalendarId ";
$query .= "LEFT JOIN teacherForDisciplines AS newTFD ";
$query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN disciplines AS newDics ";
$query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";
$query .= "LEFT JOIN studentGroups AS newGroup ";
$query .= "ON newDics.StudentGroupId = newGroup.StudentGroupId ";

$query .= "LEFT JOIN lessons AS oldLesson ";
$query .= "ON lessonLogEvents.OldLessonId = oldLesson.LessonId ";
$query .= "LEFT JOIN calendars AS oldCalendar ";
$query .= "ON oldLesson.CalendarId = oldCalendar.CalendarId ";
$query .= "LEFT JOIN teacherForDisciplines AS oldTFD ";
$query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN disciplines AS oldDics ";
$query .= "ON oldTFD.DisciplineId = oldDics.DisciplineId ";
$query .= "LEFT JOIN studentGroups AS oldGroup ";
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
$query .= "ORDER BY lessonLogEvents.DateTime DESC ";
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