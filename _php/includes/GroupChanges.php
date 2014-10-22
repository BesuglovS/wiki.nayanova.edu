<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["groupId"];
$startFrom = $_GET["startFrom"];
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

$query  = "SELECT " . $dbPrefix . "lessonLogEvents.DateTime, " . $dbPrefix . "lessonLogEvents.PublicComment, ";
$query .= $dbPrefix . "lessonLogEvents.NewLessonId, " . $dbPrefix . "lessonLogEvents.OldLessonId, ";
$query .= "newCalendar.Date AS newDate, newRing.Time AS newTime, newDics.Name AS newDicsName, ";
$query .= "newTeachers.FIO AS newTeacherFIO, newAud.Name AS newAudName, ";
$query .= "oldCalendar.Date AS oldDate, oldRing.Time AS oldTime, oldDics.Name AS oldDicsName, ";
$query .= "oldTeachers.FIO AS oldTeacherFIO, oldAud.Name AS oldAudName ";
$query .= "FROM " . $dbPrefix . "lessonLogEvents ";

$query .= "LEFT JOIN " . $dbPrefix . "lessons AS newLesson ";
$query .= "ON " . $dbPrefix . "lessonLogEvents.NewLessonId = newLesson.LessonId ";
$query .= "LEFT JOIN " . $dbPrefix . "calendars AS newCalendar ";
$query .= "ON newLesson.CalendarId = newCalendar.CalendarId ";
$query .= "LEFT JOIN " . $dbPrefix . "rings AS newRing ";
$query .= "ON newLesson.RingId = newRing.RingId ";
$query .= "LEFT JOIN " . $dbPrefix . "auditoriums AS newAud ";
$query .= "on newLesson.AuditoriumId = newAud.AuditoriumId ";
$query .= "LEFT JOIN " . $dbPrefix . "teacherForDisciplines AS newTFD ";
$query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "teachers AS newTeachers ";
$query .= "ON newTFD.TeacherId = newTeachers.TeacherId ";
$query .= "LEFT JOIN " . $dbPrefix . "disciplines AS newDics ";
$query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "studentGroups AS newGroup ";
$query .= "ON newDics.StudentGroupId = newGroup.StudentGroupId ";

$query .= "LEFT JOIN " . $dbPrefix . "lessons AS oldLesson ";
$query .= "ON " . $dbPrefix . "lessonLogEvents.OldLessonId = oldLesson.LessonId ";
$query .= "LEFT JOIN " . $dbPrefix . "calendars AS oldCalendar ";
$query .= "ON oldLesson.CalendarId = oldCalendar.CalendarId ";
$query .= "LEFT JOIN " . $dbPrefix . "rings AS oldRing ";
$query .= "ON oldLesson.RingId = oldRing.RingId ";
$query .= "LEFT JOIN " . $dbPrefix . "auditoriums AS oldAud ";
$query .= "on oldLesson.AuditoriumId = oldAud.AuditoriumId ";
$query .= "LEFT JOIN " . $dbPrefix . "teacherForDisciplines AS oldTFD ";
$query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN " . $dbPrefix . "teachers AS oldTeachers ";
$query .= "ON oldTFD.TeacherId = oldTeachers.TeacherId ";
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
$query .= "LIMIT " . $startFrom  . ", 10 ";

$events = $database->query($query);

if ($events->num_rows != 0)
{
    echo "<table class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "<td>Дата и время события</td>";
    echo "<td>Тип</td>";
    echo "<td>Урок</td>";
    echo "<td>Комментарий</td>";
    echo "</tr>";
    while($event = $events->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>";
        echo $event["DateTime"];
        echo "</td>";
        echo "<td>";
        if ($event["OldLessonId"] == -1)
        {
            echo "<img src=\"upload/images/green_add_plus_32.png\" title=\"Добавлена пара\" height=\"32\" width=\"32\">";
        }
        else
        {
            if ($event["NewLessonId"] == -1)
            {
                echo "<img src=\"upload/images/minus.png\" title=\"Отменена пара\" height=\"32\" width=\"32\">";
            }
            else
            {
                if ($event["oldAudName"] != $event["newAudName"])
                {
                    echo "<img src=\"upload/images/a-green-icon.png\" title=\"Изменена аудитория\" height=\"32\" width=\"32\">";
                }
            }
        }

        echo "</td>";
        echo "<td>";
        if (($event["OldLessonId"] != -1) && ($event["NewLessonId"] == -1))
        {
            echo $event["oldDate"] . " - " . mb_substr($event["oldTime"], 0, 5) . "<br />";
            echo $event["oldDicsName"]  . "<br />";
            echo $event["oldTeacherFIO"] . "<br />";
            echo $event["oldAudName"];
        }
        if (($event["NewLessonId"] != -1) && ($event["OldLessonId"] == -1))
        {
            echo $event["newDate"] . " - " . mb_substr($event["newTime"], 0, 5) . "<br />";
            echo $event["newDicsName"]  . "<br />";
            echo $event["newTeacherFIO"] . "<br />";
            echo $event["newAudName"];
        }
        if (($event["OldLessonId"] != -1) &&($event["NewLessonId"] != -1))
        {
            echo $event["oldDate"] . " - " . mb_substr($event["oldTime"], 0, 5);
            if ($event["oldDate"] != $event["newDate"])
            {
                echo " => " . $event["newDate"] . " - " . mb_substr($event["newTime"], 0, 5);
            }
            echo "<br />";

            echo $event["oldDicsName"];
            if ($event["oldDicsName"] != $event["newDicsName"])
            {
                echo " => " . $event["newDicsName"];
            }
            echo "<br />";

            echo $event["oldTeacherFIO"];
            if ($event["oldTeacherFIO"] != $event["newTeacherFIO"])
            {
                echo " => " . $event["newTeacherFIO"];
            }
            echo "<br />";

            echo $event["oldAudName"];
            if ($event["oldAudName"] != $event["newAudName"])
            {
                echo " => " . $event["newAudName"];
            }
        }
        echo "</td>";
        echo "<td>";
        echo $event["PublicComment"];
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