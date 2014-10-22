<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$faculty_id = $_GET["facultyId"];
$scheduleDOW = $_GET["dow"];

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

if (!function_exists('mb_str_replace')) {
    function mb_str_replace($search, $replace, $subject, &$count = 0) {
        if (!is_array($subject)) {
            // Normalize $search and $replace so they are both arrays of the same length
            $searches = is_array($search) ? array_values($search) : array($search);
            $replacements = is_array($replace) ? array_values($replace) : array($replace);
            $replacements = array_pad($replacements, count($searches), '');

            foreach ($searches as $key => $search) {
                $parts = mb_split(preg_quote($search), $subject);
                $count += count($parts) - 1;
                $subject = implode($replacements[$key], $parts);
            }
        } else {
            // Call mb_str_replace for each subject in array, recursively
            foreach ($subject as $key => $value) {
                $subject[$key] = mb_str_replace($search, $replace, $value, $count);
            }
        }

        return $subject;
    }
}

$semesterStarts = $options["Semester Starts"];

$facultyGroupsQuery  = "SELECT " . $dbPrefix . "GroupsInFaculties.StudentGroupId, Name ";
$facultyGroupsQuery .= "FROM " . $dbPrefix . "GroupsInFaculties ";
$facultyGroupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
$facultyGroupsQuery .= "ON " . $dbPrefix . "GroupsInFaculties.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$facultyGroupsQuery .= "WHERE FacultyId = " . $faculty_id;

$facultyGroupsResult = $database->query($facultyGroupsQuery);

$groups = array();
while ($group = $facultyGroupsResult->fetch_assoc())
{
    $groups[$group["Name"]] = array (
        "group_id" => $group["StudentGroupId"]
    );
}

$timeArray = array();

$dayOff = 1;

foreach ($groups as $key => $value) {
    $groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
    $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
    $groupsQuery .= "WHERE StudentId ";
    $groupsQuery .= "IN ( ";
    $groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
    $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
    $groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
    $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
    $groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $value["group_id"] ." ";
    $groupsQuery .= ")";

    $groupIdsResult = $database->query($groupsQuery);

    $groupIdsArray = array();
    while ($id = $groupIdsResult->fetch_assoc())
    {
        $groupIdsArray[] = $id["StudentGroupId"];
    }
    $groups[$key]["groupListForGroup"] = $groupIdsArray;

    $groups[$key]["Schedule"] = array();

    $groupCondition = "WHERE " . $dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " ) ";

    $allLessonsQuery  = "SELECT " . $dbPrefix . "disciplines.Name as discName, " . $dbPrefix . "rings.Time as startTime, ";
    $allLessonsQuery .= $dbPrefix . "calendars.Date as date, " . $dbPrefix . "teachers.FIO as teacherFIO, " . $dbPrefix . "auditoriums.Name as auditoriumName, ";
    $allLessonsQuery .= $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId as tfdId, " . $dbPrefix . "studentGroups.Name as groupName ";
    $allLessonsQuery .= "FROM " . $dbPrefix . "lessons ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
    $allLessonsQuery .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "teachers ";
    $allLessonsQuery .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "disciplines ";
    $allLessonsQuery .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
    $allLessonsQuery .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "calendars ";
    $allLessonsQuery .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.calendarId ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "auditoriums ";
    $allLessonsQuery .= "ON " . $dbPrefix . "lessons.auditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
    $allLessonsQuery .= "JOIN " . $dbPrefix . "rings ";
    $allLessonsQuery .= "ON " . $dbPrefix . "lessons.ringId = " . $dbPrefix . "rings.ringId ";
    $allLessonsQuery .= $groupCondition;
    $allLessonsQuery .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

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
    $gName = mb_ereg_replace(" (+Н)", "", $groupKey);
    echo "  <td>" . mb_str_replace(" (+Н)", "", $groupKey) . "</td>";
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