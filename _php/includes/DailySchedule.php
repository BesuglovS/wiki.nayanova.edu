<?php
session_start();
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["groupId"];
$dateString = $_GET["date"];

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

$groupNameQuery = "SELECT " . $dbPrefix . "studentGroups.Name FROM " . $dbPrefix . "studentGroups WHERE StudentGroupId = " . $groupId;


$sGroup = $database->query($groupNameQuery);
$groupNameArr = $sGroup->fetch_assoc();
$groupName = $database->real_escape_string($groupNameArr["Name"]);

$studentId = -1;
if ((isset($_SESSION['NUlogin'])) && (!isset($_SESSION['AltUserId'])))
{
    $FIO = explode(' ',trim($_SESSION['NUlogin']));
    $F = $FIO[0];

    $MySQLDate = ReformatDateToMySQL($_SESSION['NUpassword']);
    $studentIdQuery  = "SELECT StudentId ";
    $studentIdQuery .= "FROM " . $dbPrefix . "students ";
    $studentIdQuery .= "WHERE F = '" . $F . "' ";
    $studentIdQuery .= "AND BirthDate = '" . $MySQLDate . "' ";
    $studentResult = $database->query($studentIdQuery);
    $studentIdArray = $studentResult->fetch_assoc();
    $studentId = $studentIdArray["StudentId"];
}

$altUserId = "";
if (isset($_SESSION['AltUserId']))
{
    $altUserId = $_SESSION['AltUserId'];
}

$todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = gmdate("y.m.d H:i:s", $todayStamp);
$statisticQuery  = "INSERT INTO " . $dbPrefix . "DailyScheduleStats( groupId, date, statDate";
$statisticQuery .= ", StudentId ";
$statisticQuery .= ", AltUserId ";
$statisticQuery .= ") ";
$statisticQuery .= "VALUES ( \"";
$statisticQuery .= $groupName;
$statisticQuery .= "\", ";
$statisticQuery .= $dateString;
$statisticQuery .= ", \"";
$statisticQuery .= $today . "\"";
$statisticQuery .= ", ";
$statisticQuery .= $studentId;
$statisticQuery .= ", \"";
$statisticQuery .= $altUserId;
$statisticQuery .= "\" ";
$statisticQuery .= ")";

/*
if (isset($_SESSION['NUlogin']))
{
    echo $studentId . "<br />";
    echo $statisticQuery . "<br />";
}*/

$database->query($statisticQuery);


$groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
$groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
$groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$groupsQuery .= "JOIN " . $dbPrefix . "students ";
$groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentId = " . $dbPrefix . "students.StudentId ";
$groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $groupId ." ";
$groupsQuery .= "AND " . $dbPrefix . "students.Expelled = 0 ";
$groupsQuery .= ")";
$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = $dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$query  = "SELECT " . $dbPrefix . "rings.Time, " . $dbPrefix . "disciplines.Name AS discName, ";
$query .= $dbPrefix . "teachers.FIO, " . $dbPrefix . "auditoriums.Name AS audName, ";
$query .= $dbPrefix . "studentGroups.Name AS groupName ";
$query .= "FROM " . $dbPrefix . "lessons ";
$query .= "JOIN " . $dbPrefix . "calendars ";
$query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
$query .= "JOIN " . $dbPrefix . "rings ";
$query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
$query .= "JOIN " . $dbPrefix . "auditoriums ";
$query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumID ";
$query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
$query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
$query .= "JOIN " . $dbPrefix . "teachers ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
$query .= "JOIN " . $dbPrefix . "disciplines ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
$query .= "JOIN " . $dbPrefix . "studentGroups ";
$query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$query .= "WHERE " . $dbPrefix . "lessons.IsActive=1 ";
$query .= "AND (" . $groupCondition . ") ";
$query .= "AND " . $dbPrefix . "calendars.Date = " . $dateString . " ";
$query .= "ORDER BY " . $dbPrefix . "rings.Time ASC, groupName";

$lessonsList = $database->query($query);

if ($lessonsList->num_rows != 0)
{
    $resultLessons = array();

    while($lesson = $lessonsList->fetch_assoc())
    {
        $resultLesson = array();

        $lesHour = mb_substr($lesson["Time"], 0, 2);
        $lesMin = mb_substr($lesson["Time"], 3, 2);
        $timeDiff = Utilities::DiffTimeWithNowInMinutes($lesHour, $lesMin);

        $today = date("Y-m-d");
        $scheduleDate = mb_substr($dateString, 1, mb_strlen($dateString)-2);

        if (($timeDiff < 0) && ($timeDiff > -80) && ($today == $scheduleDate))
        {
            $resultLesson["onGoing"] = 1;
        }
        else
        {
            $resultLesson["onGoing"] = 0;
        }


        $resultLesson["Time"] = substr($lesson["Time"], 0, strlen($lesson["Time"])-3);
        $resultLesson["discName"] = $lesson["discName"];
        $resultLesson["FIO"] = $lesson["FIO"];
        $resultLesson["audName"] = $lesson["audName"];
        $resultLesson["groupName"] = $lesson["groupName"];

        $resultLessons[] = $resultLesson;
    }

    $startRow = -1;

    for($i = 1; $i < count($resultLessons); $i++)
    {
        if ($resultLessons[$i]["Time"] == $resultLessons[$i-1]["Time"])
        {
            if ($startRow == -1)
            {
                $startRow = $i-1;
                $resultLessons[$i]["omitTime"] = 1;
                $resultLessons[$startRow]["rowspan"] = 2;
            }
            else
            {
                $resultLessons[$i]["omitTime"] = 1;
                $resultLessons[$startRow]["rowspan"] = $i - $startRow + 1;
            }
        }
        else
        {
            $startRow = -1;
        }
    }


    echo "<table class=\"DailySchedule\">";
    for($i = 0; $i < count($resultLessons); $i++)
    {
        echo "<tr>";
        if ($resultLessons[$i]["omitTime"] != 1)
        {
            echo "<td";
            if (array_key_exists("rowspan",$resultLessons[$i]))
            {
                echo " rowspan=\"" . $resultLessons[$i]["rowspan"] . "\"";
            }
            if ($resultLessons[$i]["onGoing"] == 1)
            {
                echo " style=\"background:#FFFFAA\"";
            }
            echo ">";
            echo $resultLessons[$i]["Time"];
            echo "</td>";
        }
        echo "<td";
        if ($resultLessons[$i]["onGoing"] == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo $resultLessons[$i]["discName"];
        if ($groupName != $resultLessons[$i]["groupName"])
        {
            echo " (" . $resultLessons[$i]["groupName"] . ")";
        }
        echo "</td>";
        echo "<td";
        if ($resultLessons[$i]["onGoing"] == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo $resultLessons[$i]["FIO"];
        echo "</td>";
        echo "<td";
        if ($resultLessons[$i]["onGoing"] == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo $resultLessons[$i]["audName"];
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