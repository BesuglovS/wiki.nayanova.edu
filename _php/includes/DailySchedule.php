<?php
session_start();
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

function ReformatDateToMySQL($date)
{
    // 0123456789
    // 25.10.1995 => 1995-10-25
    $semesterStartsCorrectFormat =
        mb_substr($date, 6, 4) . "-" .
        mb_substr($date, 3, 2) . "-" .
        mb_substr($date, 0, 2);
    return $semesterStartsCorrectFormat;
}

$groupId = $_GET["groupId"];
$dateString = $_GET["date"];

$groupNameQuery = "SELECT studentGroups.Name FROM studentGroups WHERE StudentGroupId = " . $groupId;
$sGroup = $database->query($groupNameQuery);
$groupNameArr = $sGroup->fetch_assoc();
$groupName = $database->real_escape_string($groupNameArr["Name"]);

$studentId = 0;
if (isset($_SESSION['NUlogin']))
{
    $FIO = explode(' ',trim($_SESSION['NUlogin']));
    $F = $FIO[0];

    $MySQLDate = ReformatDateToMySQL($_SESSION['NUpassword']);
    $studentIdQuery  = "SELECT StudentId ";
    $studentIdQuery .= "FROM students ";
    $studentIdQuery .= "WHERE F = '" . $F . "' ";
    $studentIdQuery .= "AND BirthDate = '" . $MySQLDate . "' ";
    $studentResult = $database->query($studentIdQuery);
    $studentIdArray = $studentResult->fetch_assoc();
    $studentId = $studentIdArray["StudentId"];
}

$todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = gmdate("y.m.d H:i:s", $todayStamp);
$statisticQuery  = "INSERT INTO DailyScheduleStats( groupId, date, statDate";
if ($studentId !== 0)
{
    $statisticQuery .= ", StudentId ";
}
$statisticQuery .= ") ";
$statisticQuery .= "VALUES ( \"";
$statisticQuery .= $groupName;
$statisticQuery .= "\", ";
$statisticQuery .= $dateString;
$statisticQuery .= ", \"";
$statisticQuery .= $today . "\"";
if ($studentId !== 0)
{
    $statisticQuery .= ", ";
    $statisticQuery .= $studentId;
}
$statisticQuery .= ")";

if (isset($_SESSION['NUlogin']))
{
    echo $studentId . "<br />";
    echo $statisticQuery . "<br />";
}

$database->query($statisticQuery);


$groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT studentsInGroups.StudentId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "JOIN studentGroups ";
$groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
$groupsQuery .= "JOIN students ";
$groupsQuery .= "ON studentsInGroups.StudentId = students.StudentId ";
$groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $groupId ." ";
$groupsQuery .= "AND students.Expelled = 0 ";
$groupsQuery .= ")";
$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$query  = "SELECT rings.Time, disciplines.Name, teachers.FIO, auditoriums.Name ";
$query .= "FROM lessons ";
$query .= "JOIN calendars ";
$query .= "ON lessons.CalendarId = calendars.CalendarId ";
$query .= "JOIN rings ";
$query .= "ON lessons.RingId = rings.RingId ";
$query .= "JOIN auditoriums ";
$query .= "ON lessons.AuditoriumId = auditoriums.AuditoriumID ";
$query .= "JOIN teacherForDisciplines ";
$query .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
$query .= "JOIN teachers ";
$query .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
$query .= "JOIN disciplines ";
$query .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
$query .= "JOIN studentGroups ";
$query .= "ON disciplines.StudentGroupId = studentGroups.StudentGroupId ";
$query .= "WHERE lessons.IsActive=1 ";
$query .= "AND (" . $groupCondition . ") ";
$query .= "AND calendars.Date = " . $dateString . " ";
$query .= "ORDER BY rings.Time ASC";

$lessonsList = $database->query($query);

if ($lessonsList->num_rows != 0)
{
    echo "<table class=\"DailySchedule\">";
    while($lesson = $lessonsList->fetch_row())
    {
        $lesHour = mb_substr($lesson[0], 0, 2);
        $lesMin = mb_substr($lesson[0], 3, 2);
        $timeDiff = Utilities::DiffTimeWithNowInMinutes($lesHour, $lesMin);

        $today = date("Y-m-d");
        $scheduleDate = mb_substr($dateString, 1, mb_strlen($dateString)-2);

        if (($timeDiff < 0) && ($timeDiff > -80) && ($today == $scheduleDate))
        {
            $onGoing = 1;
        }
        else
        {
            $onGoing = 0;
        }

        echo "<tr>";
        echo "<td";
        if ($onGoing == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo substr($lesson[0], 0, strlen($lesson[0])-3);
        echo "</td>";
        echo "<td";
        if ($onGoing == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo $lesson[1];
        echo "</td>";
        echo "<td";
        if ($onGoing == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo $lesson[2];
        echo "</td>";
        echo "<td";
        if ($onGoing == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo $lesson[3];
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo Utilities::TagMessage("Занятий нет.");
}

?>