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
$faculty_id = $_GET["facultyId"];
$dateString = $_GET["date"];

$scheduleDate = DateTime::createFromFormat('Y-m-d', $dateString);
$scheduleDOW = Utilities::$DOWEnToRu[date( "w", $scheduleDate->getTimestamp())];

$facultyGroupsArray = array(
    "1" => array("12 А", "13 А"),
    "2" => array("12 Б", "13 Б", "14 Б", "15 Б"),
    "3" => array("12 В0", "12 В", "13 В", "14 В", "15 В"),
    "4" => array("12 Г", "12 Г(Н)", "13 Г", "13 Г(Н)", "14 Г", "14 Г(Н)"),
    "5" => array("12 Д", "12 Д(Н)", "13 Д", "13 Д(Н)", "14 Д", "14 Д(Н)", "15 Д"),
    "6" => array("12 Е", "12 Е(Н)", "13 Е", "14 Е", "15 Е"),
    "7" => array("12 У", "13 У", "14 У", "15 У"),
    "8" => array("12 Т", "13 Т", "14 Т")
);
$facultyGroups = $facultyGroupsArray[$faculty_id];

$groups = array();
foreach ($facultyGroups as $groupName) {
    $idQuery  = "SELECT studentGroups.StudentGroupId ";
    $idQuery .= "FROM studentGroups ";
    $idQuery .= "WHERE studentGroups.Name = \"" . $groupName . "\"";

    $qResult = $database->query($idQuery);
    $result = $qResult->fetch_assoc();
    $groupId = $result["StudentGroupId"];


    $groups[$groupName] = array (
        "group_id" => $groupId
    );
}

$timeArray = array();

$dayOff = 1;

foreach ($groups as $key => $value) {
    $groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
    $groupsQuery .= "FROM studentsInGroups ";
    $groupsQuery .= "WHERE StudentId ";
    $groupsQuery .= "IN ( ";
    $groupsQuery .= "SELECT studentsInGroups.StudentId ";
    $groupsQuery .= "FROM studentsInGroups ";
    $groupsQuery .= "JOIN studentGroups ";
    $groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
    $groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $value["group_id"] ." ";
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
    $allLessonsQuery .= "teacherForDisciplines.TeacherForDisciplineId as tfdId, studentGroups.Name as groupName ";
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

    $lessonsList = $database->query($allLessonsQuery);

    while($lesson = $lessonsList->fetch_assoc())
    {

        $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["date"]);
        $dow = date( "w", $lessonDate->getTimestamp());

        if ($dow == $scheduleDOW)
        {
            $dayOff = 0;

            $time = mb_substr($lesson["startTime"], 0, 5);
            if (!array_key_exists($time, $groups[$key]["Schedule"]))
            {
                if (!in_array($time, $timeArray))
                {
                    $timeArray[] = $time;
                }
                $groups[$key]["Schedule"][$time] = array();
            }

            $tfd = $lesson["tfdId"];
            if (!array_key_exists($tfd, $groups[$key]["Schedule"][$time]))
            {
                $groups[$key]["Schedule"][$time][$tfd] = array();
            }

            $lessonWeek = Utilities::WeekFromDate($lesson["date"], $semesterStarts);
            $lessonAud = $lesson["auditoriumName"];
            if (!array_key_exists("weeksAndAuds", $groups[$key]["Schedule"][$time][$tfd]))
            {
                $groups[$key]["Schedule"][$time][$tfd]["weeksAndAuds"] = array();
            }
            if (!array_key_exists("lessons", $groups[$key]["Schedule"][$time][$tfd]))
            {
                $groups[$key]["Schedule"][$time][$tfd]["lessons"] = array();
            }
            if (!array_key_exists($lessonAud, $groups[$key]["Schedule"][$time][$tfd]["weeksAndAuds"]))
            {
                $groups[$key]["Schedule"][$time][$tfd]["weeksAndAuds"][$lessonAud] = array();
            }
            $groups[$key]["Schedule"][$time][$tfd]["weeksAndAuds"][$lessonAud][] = $lessonWeek;

            $groups[$key]["Schedule"][$time][$tfd]["lessons"][] = $lesson;
        }
    }
}

uasort($timeArray, "timeCompare");

if ($dayOff == 1)
{
    echo Utilities::TagMessage("ВЫХОДНОЙ!");
    exit;
}

echo "<table id=\"FacultyDOWSchedule\" class=\"DOWSchedule\">";
echo "<tr>";
echo "  <td>Время</td>";
foreach ($groups as $groupKey => $groupData) {
    echo "  <td>" . $groupKey . "</td>";
}
echo "</tr>";

foreach ($timeArray as $time) {
    $lesHour = mb_substr($time, 0, 2);
    $lesMin = mb_substr($time, 3, 2);
    $timeDiff = Utilities::DiffTimeWithNowInMinutes($lesHour, $lesMin);

    $todaysDOW = Utilities::$DOWEnToRu[date( "w", time())];
    if (($timeDiff < 0) && ($timeDiff > -80) && ($todaysDOW == $scheduleDOW))
    {
        $onGoing = 1;
    }
    else
    {
        $onGoing = 0;
    }

    echo "<tr>";
    echo "  <td";
    if ($onGoing == 1)
    {
        echo " style=\"background:#FFFFAA\"";
    }
    echo ">$time</td>";

    foreach ($groups as $groupKey => $groupData) {
        //$groupData["Schedule"][$time]
        echo "  <td";
        if ($onGoing == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        if (!array_key_exists($time, $groupData["Schedule"]))
        {
            echo "&nbsp;";
        }

        $splitCounter = 0;

        usort($groupData["Schedule"][$time], "tfdSort");

        foreach ($groupData["Schedule"][$time] as $tfdId => $tfdData)
        {
            if ($tfdData["lessons"][0]["groupName"] != $groupKey)
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


            // TODO сортировать недели аудиторий по порядку
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

            $cnt = count($groupData["Schedule"][$time]);
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

echo "</table>";

?>