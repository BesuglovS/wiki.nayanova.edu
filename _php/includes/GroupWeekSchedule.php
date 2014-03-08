<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("ConfigOptions.php");
require_once("Utilities.php");

global $database;

function tfdSort($a, $b){
    $minWeekA = 20;
    foreach ($a["weeksAndAuds"] as $curAud => $weekArray)
    {
        foreach ($weekArray as $weekNum)
        {
            if ($weekNum < $minWeekA)
            {
                $minWeekA = $weekNum;
            }
        }
    }

    $minWeekB = 20;
    foreach ($b ["weeksAndAuds"] as $curAud => $weekArray)
    {
        foreach ($weekArray as $weekNum)
        {
            if ($weekNum < $minWeekB)
            {
                $minWeekB = $weekNum;
            }
        }
    }

    if ($minWeekA == $minWeekB) {
        return 0;
    }
    return ($minWeekA < $minWeekB) ? -1 : 1;
}

function timeCompare($a, $b)
{
    $parsed_time_A = date_parse($a . ":00");
    $parsed_time_B = date_parse($b . ":00");
    if ($parsed_time_A["hour"] > $parsed_time_B["hour"])
    {
        return 1;
    }
    else
    {
        if ($parsed_time_A["hour"] < $parsed_time_B["hour"])
        {
            return -1;
        }
        else
        {
            if ($parsed_time_A["minute"] > $parsed_time_B["minute"])
            {
                return 1;
            }
            else
            {
                if ($parsed_time_A["minute"] < $parsed_time_B["minute"])
                {
                    return -1;
                }
                else
                {
                    return 0;
                }
            }
        }
    }
}

$semesterStarts = $options["Semester Starts"];
$group_id = $_GET["groupId"];

$groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT studentsInGroups.StudentId ";
$groupsQuery .= "FROM studentsInGroups ";
$groupsQuery .= "JOIN studentGroups ";
$groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
$groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $group_id ." ";
$groupsQuery .= ")";

$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groups[$key]["groupListForGroup"] = $groupIdsArray;

$groups[$key]["Schedule"] = array();

$groupCondition = "WHERE disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " ) ";

$allLessonsQuery  = "SELECT disciplines.Name as discName, rings.Time as startTime, ";
$allLessonsQuery .= "calendars.Date as date, teachers.FIO as teacherFIO, auditoriums.Name as auditoriumName, ";
$allLessonsQuery .= "teacherForDisciplines.TeacherForDisciplineId as tfdId, studentGroups.Name as groupName, ";
$allLessonsQuery .= "studentGroups.StudentGroupId as groupId ";
$allLessonsQuery .= "FROM lessons ";
$allLessonsQuery .= "JOIN teacherForDisciplines ";
$allLessonsQuery .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
$allLessonsQuery .= "JOIN teachers ";
$allLessonsQuery .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
$allLessonsQuery .= "JOIN disciplines ";
$allLessonsQuery .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
$allLessonsQuery .= "JOIN studentGroups ";
$allLessonsQuery .= "ON disciplines.StudentGroupId = studentGroups.StudentGroupId ";
$allLessonsQuery .= "JOIN calendars ";
$allLessonsQuery .= "ON lessons.CalendarId = calendars.calendarId ";
$allLessonsQuery .= "JOIN auditoriums ";
$allLessonsQuery .= "ON lessons.auditoriumId = auditoriums.AuditoriumId ";
$allLessonsQuery .= "JOIN rings ";
$allLessonsQuery .= "ON lessons.ringId = rings.ringId ";
$allLessonsQuery .= $groupCondition;
$allLessonsQuery .= "AND lessons.isActive = 1 ";

$allLessonsQueryResult = $database->query($allLessonsQuery);


$lessons = array("1" => array(), "2" => array(), "3" => array(), "4" => array(),
                 "5" => array(), "6" => array(), "7" => array());
$timeArray = array();

while($lesson = $allLessonsQueryResult->fetch_assoc())
{
    $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["date"]);
    $dow = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];

    $time = mb_substr($lesson["startTime"], 0, 5);
    if (!array_key_exists($time, $lessons[$dow]))
    {
        if (!in_array($time, $timeArray))
        {
            $timeArray[] = $time;
        }
        $lessons[$dow][$time] = array();
    }


    $tfd = $lesson["tfdId"];
    if (!array_key_exists($tfd, $lessons[$dow][$time]))
    {
        $lessons[$dow][$time][$tfd] = array();
    }

    $lessonWeek = Utilities::WeekFromDate($lesson["date"], $semesterStarts);
    $lessonAud = $lesson["auditoriumName"];
    if (!array_key_exists("weeksAndAuds", $lessons[$dow][$time][$tfd]))
    {
        $lessons[$dow][$time][$tfd]["weeksAndAuds"] = array();
    }
    if (!array_key_exists("lessons", $lessons[$dow][$time][$tfd]))
    {
        $lessons[$dow][$time][$tfd]["lessons"] = array();
    }
    if (!array_key_exists($lessonAud, $lessons[$dow][$time][$tfd]["weeksAndAuds"]))
    {
        $lessons[$dow][$time][$tfd]["weeksAndAuds"][$lessonAud] = array();
    }
    $lessons[$dow][$time][$tfd]["weeksAndAuds"][$lessonAud][] = $lessonWeek;

    $lessons[$dow][$time][$tfd]["lessons"][] = $lesson;
}

uasort($timeArray, "timeCompare");

echo "<table id=\"FacultyDOWSchedule\" class=\"DOWSchedule\">";
echo "<tr>";
echo "  <td>Время</td>";
for ($i = 1; $i <= 6; $i++) {
    echo "  <td>" . Utilities::$DOW[$i] . "</td>";
}
echo "</tr>";


foreach ($timeArray as $time) {

    echo "<tr>";
    echo "  <td>$time</td>";


    for ($dayOW = 1; $dayOW <= 6; $dayOW++) {

        echo "  <td>";
        if (!array_key_exists($time, $lessons[$dayOW]))
        {
            echo "&nbsp;";
        }

        $splitCounter = 0;

        usort($lessons[$dayOW][$time], "tfdSort");

        foreach ($lessons[$dayOW][$time] as $tfdId => $tfdData)
        {
            if ($tfdData["lessons"][0]["groupId"] != $group_id)
            {
                echo $tfdData["lessons"][0]["groupName"] . "<br />";
            }

            echo $tfdData["lessons"][0]["discName"] . "<br />";
            echo $tfdData["lessons"][0]["teacherFIO"] . "<br />";



            $commonWeeks = array();
            foreach ($tfdData["weeksAndAuds"] as $curAud => $weekArray)
            {
                foreach ($weekArray as $weekNum) {
                    $commonWeeks[] = $weekNum;
                }
            }

            echo "    ( " . Utilities::GatherWeeksToString($commonWeeks) . " )<br />";

            if (count($tfdData["weeksAndAuds"]) > 1)
            {
                foreach ($tfdData["weeksAndAuds"] as $audName => $currentWeekList)
                {
                    echo Utilities::GatherWeeksToString($currentWeekList) . " - ";
                    echo $audName . "<br />";
                }
            }
            else
            {
                foreach ($tfdData["weeksAndAuds"] as $audName => $weekList)
                {
                    echo $audName . "<br />";
                }
            }

            $cnt = count($lessons[$dayOW][$time]);
            if (($cnt != 1) && ($splitCounter != $cnt-1))
            {
                echo "<hr />";
            }

            $splitCounter++;
        }

        echo "</td>";
    }
    echo "</tr>";
}



?>