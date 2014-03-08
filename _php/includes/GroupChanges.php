<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupId = $_GET["groupId"];
$startFrom = $_GET["startFrom"];
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

$query  = "SELECT lessonLogEvents.DateTime, lessonLogEvents.PublicComment, ";
$query .= "lessonLogEvents.NewLessonId, lessonLogEvents.OldLessonId, ";
$query .= "newCalendar.Date AS newDate, newRing.Time AS newTime, newDics.Name AS newDicsName, ";
$query .= "newTeachers.FIO AS newTeacherFIO, newAud.Name AS newAudName, ";
$query .= "oldCalendar.Date AS oldDate, oldRing.Time AS oldTime, oldDics.Name AS oldDicsName, ";
$query .= "oldTeachers.FIO AS oldTeacherFIO, oldAud.Name AS oldAudName ";
$query .= "FROM lessonLogEvents ";

$query .= "LEFT JOIN lessons AS newLesson ";
$query .= "ON lessonLogEvents.NewLessonId = newLesson.LessonId ";
$query .= "LEFT JOIN calendars AS newCalendar ";
$query .= "ON newLesson.CalendarId = newCalendar.CalendarId ";
$query .= "LEFT JOIN rings AS newRing ";
$query .= "ON newLesson.RingId = newRing.RingId ";
$query .= "LEFT JOIN auditoriums AS newAud ";
$query .= "on newLesson.AuditoriumId = newAud.AuditoriumId ";
$query .= "LEFT JOIN teacherForDisciplines AS newTFD ";
$query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN teachers AS newTeachers ";
$query .= "ON newTFD.TeacherId = newTeachers.TeacherId ";
$query .= "LEFT JOIN disciplines AS newDics ";
$query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";
$query .= "LEFT JOIN studentGroups AS newGroup ";
$query .= "ON newDics.StudentGroupId = newGroup.StudentGroupId ";

$query .= "LEFT JOIN lessons AS oldLesson ";
$query .= "ON lessonLogEvents.OldLessonId = oldLesson.LessonId ";
$query .= "LEFT JOIN calendars AS oldCalendar ";
$query .= "ON oldLesson.CalendarId = oldCalendar.CalendarId ";
$query .= "LEFT JOIN rings AS oldRing ";
$query .= "ON oldLesson.RingId = oldRing.RingId ";
$query .= "LEFT JOIN auditoriums AS oldAud ";
$query .= "on oldLesson.AuditoriumId = oldAud.AuditoriumId ";
$query .= "LEFT JOIN teacherForDisciplines AS oldTFD ";
$query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
$query .= "LEFT JOIN teachers AS oldTeachers ";
$query .= "ON oldTFD.TeacherId = oldTeachers.TeacherId ";
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