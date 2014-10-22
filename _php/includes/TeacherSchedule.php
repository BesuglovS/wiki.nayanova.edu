<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$teacherId = $_GET["teacherId"];

require_once("Database.php");
require_once("ConfigOptions.php");
require_once("Utilities.php");

global $database;

$query  = "SELECT " . $dbPrefix . "calendars.Date, " . $dbPrefix . "rings.Time, " . $dbPrefix . "studentGroups.Name as groupName, ";
$query .= $dbPrefix . "disciplines.Name as disciplineName, " . $dbPrefix . "auditoriums.Name as auditoriumName, ";
$query .= $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
$query .= "FROM " . $dbPrefix . "lessons ";
$query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
$query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
$query .= "JOIN " . $dbPrefix . "teachers ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
$query .= "JOIN " . $dbPrefix . "calendars ";
$query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
$query .= "JOIN " . $dbPrefix . "rings ";
$query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
$query .= "JOIN " . $dbPrefix . "disciplines ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
$query .= "JOIN " . $dbPrefix . "studentGroups ";
$query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$query .= "JOIN " . $dbPrefix . "auditoriums ";
$query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
$query .= "WHERE " . $dbPrefix . "teachers.TeacherId=" . $teacherId . " ";
$query .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

$queryResult = $database->query($query);

$lessonsArray = array();
while ($lesson = $queryResult->fetch_assoc())
{
    $lessonsArray[] = $lesson;
}

$semesterStarts = $options["Semester Starts"];

$result = array();
foreach ($lessonsArray as $lesson) {
    $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["Date"]);
    $dow = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];
    $time = mb_substr($lesson["Time"], 0, 5);
    $dowAndTime = $dow . " " . $time;
    if(!array_key_exists($dowAndTime, $result))
    {
        $result[$dowAndTime] = array();
    }

    $tfd = $lesson["TeacherForDisciplineId"];
    if (!array_key_exists($tfd, $result[$dowAndTime]))
    {
        $result[$dowAndTime][$tfd] = array();
    }

    $result[$dowAndTime][$tfd][] = $lesson;
}
ksort($result);


if (!empty($result))
{
    echo "<table id=\"TeacherSchedule\" class=\"redHeadWhiteBodyTable\">";
    echo "<tr>";
    echo "  <td>День недели<br />Время</td>";
    echo "  <td>Занятия</td>";
    echo "</tr>";

    foreach($result as $dowTime => $tfdLessons)
    {
        $dowNumber = mb_substr($dowTime, 0, 1);
        $dowStartTime = mb_substr($dowTime, 1);

        $lesHour = mb_substr($dowStartTime, 1, 2);
        $lesMin = mb_substr($dowStartTime, 4, 2);
        $timeDiff = Utilities::DiffTimeWithNowInMinutes($lesHour, $lesMin);
        $todaysDOW = Utilities::$DOWEnToRu[date( "w", time())];

        if (($timeDiff < 0) && ($timeDiff > -80) && ($todaysDOW == $dowNumber))
        {
            $onGoing = 1;
        }
        else
        {
            $onGoing = 0;
        }

        $dowTimeRU = Utilities::$DOW[$dowNumber] . "<br />" . $dowStartTime;
        $tfdCount = count(array_keys($tfdLessons));

        if ($tfdCount > 1)
        {
            $tfdKeys = array_keys($tfdLessons);

            echo "<tr>";
            echo "  <td";
            if ($onGoing == 1)
            {
                echo " style=\"background:#FFFFAA\"";
            }
            echo " rowspan=\"" . $tfdCount . "\">";
            echo "    " . $dowTimeRU;
            echo "  </td>";
            echo "  <td";
            if ($onGoing == 1)
            {
                echo " style=\"background:#FFFFAA\"";
            }
            echo ">";
            echo "    " . $tfdLessons[$tfdKeys[0]][0]["groupName"] . "<br />";
            echo "    " . $tfdLessons[$tfdKeys[0]][0]["disciplineName"] . "<br />";

            // Недели
            $weeks = array();
            foreach ($tfdLessons[$tfdKeys[0]] as $lesson) {
                $weeks[] = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
            }
            echo "    ( " . Utilities::GatherWeeksToString($weeks) . " )<br />";
            // Недели

            // Аудитории
            $auditoriums = array();
            foreach ($tfdLessons[$tfdKeys[0]] as $lesson) {
                $audName = $lesson["auditoriumName"];
                if (!array_key_exists($audName, $auditoriums))
                {
                    $auditoriums[$audName] = array();
                }
                $auditoriums[$audName][] = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
            }

            if (count(array_keys($auditoriums)) === 1)
            {
                $keys = array_keys($auditoriums);
                $aud = $keys[0];
                echo "    " . $aud . "<br />";
            }
            else
            {
                foreach ($auditoriums as $aud => $audWeeks) {
                    echo "    ";
                    echo " " . Utilities::GatherWeeksToString($audWeeks) . " ";
                    echo " - ";
                    echo $aud;
                    echo "<br />";
                }
            }
            // Аудитории

            echo "  </td>";
            echo "</tr>";

            for ($i = 1; $i < count($tfdKeys); $i++)
            {
                echo "<tr>";
                echo "  <td";
                if ($onGoing == 1)
                {
                    echo " style=\"background:#FFFFAA\"";
                }
                echo ">";
                echo "    " . $tfdLessons[$tfdKeys[$i]][0]["groupName"] . "<br />";
                echo "    " . $tfdLessons[$tfdKeys[$i]][0]["disciplineName"] . "<br />";
                $weeks = array();
                foreach ($tfdLessons[$tfdKeys[$i]] as $lesson) {
                    $weeks[] = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
                }
                echo "    ( " . Utilities::GatherWeeksToString($weeks) . " )<br />";

                // Аудитории
                $auditoriums = array();
                foreach ($tfdLessons[$tfdKeys[$i]] as $lesson) {
                    $audName = $lesson["auditoriumName"];
                    if (!array_key_exists($audName, $auditoriums))
                    {
                        $auditoriums[$audName] = array();
                    }
                    $auditoriums[$audName][] = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
                }

                if (count(array_keys($auditoriums)) === 1)
                {
                    $keys = array_keys($auditoriums);
                    $aud = $keys[0];
                    echo "    " . $aud . "<br />";
                }
                else
                {
                    foreach ($auditoriums as $aud => $audWeeks) {
                        echo "    ";
                        echo " " . Utilities::GatherWeeksToString($audWeeks) . " ";
                        echo " - ";
                        echo $aud;
                        echo "<br />";
                    }
                }
                // Аудитории

                echo "  </td>";
                echo "</tr>";
            }

        }
        else
        {
            $tfdKeys = array_keys($tfdLessons);
            $i = 0;


            echo "<tr>";
            echo "  <td";
            if ($onGoing == 1)
            {
                echo " style=\"background:#FFFFAA\"";
            }
            echo ">";
            echo "    " . $dowTimeRU;
            echo "  </td>";
            echo "  <td";
            if ($onGoing == 1)
            {
                echo " style=\"background:#FFFFAA\"";
            }
            echo ">";
            echo "    " . $tfdLessons[$tfdKeys[$i]][0]["groupName"] . "<br />";
            echo "    " . $tfdLessons[$tfdKeys[$i]][0]["disciplineName"] . "<br />";

            $weeks = array();
            foreach ($tfdLessons[$tfdKeys[$i]] as $lesson) {
                $weeks[] = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
            }
            echo "    ( " . Utilities::GatherWeeksToString($weeks) . " )<br />";

            // Аудитории
            $auditoriums = array();
            foreach ($tfdLessons[$tfdKeys[$i]] as $lesson) {
                $audName = $lesson["auditoriumName"];
                if (!array_key_exists($audName, $auditoriums))
                {
                    $auditoriums[$audName] = array();
                }
                $auditoriums[$audName][] = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
            }

            if (count(array_keys($auditoriums)) === 1)
            {
                $keys = array_keys($auditoriums);
                $aud = $keys[0];
                echo "    " . $aud . "<br />";
            }
            else
            {
                foreach ($auditoriums as $aud => $audWeeks) {
                    echo "    ";
                    echo " " . Utilities::GatherWeeksToString($audWeeks) . " ";
                    echo " - ";
                    echo $aud;
                    echo "<br />";
                }
            }
            // Аудитории

            echo "  </td>";
            echo "</tr>";

        }

    }

    echo "</table>";
}
else
{
    echo Utilities::NothingISThereString();
}

?>