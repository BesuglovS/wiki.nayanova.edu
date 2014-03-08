<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$building = $_GET["building"];
$dateString = $_GET["date"];


$query  = "SELECT rings.Time, auditoriums.Name, studentGroups.Name, disciplines.Name, teachers.FIO ";
$query .= "FROM lessons ";
$query .= "JOIN teacherForDisciplines ";
$query .= "ON lessons.`TeacherForDisciplineId` = teacherForDisciplines.`TeacherForDisciplineId` ";
$query .= "JOIN disciplines ";
$query .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
$query .= "JOIN studentGroups ";
$query .= "ON disciplines.StudentGroupId = studentGroups.StudentGroupId ";
$query .= "JOIN teachers ";
$query .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
$query .= "JOIN rings ";
$query .= "ON lessons.RingId = rings.RingId ";
$query .= "JOIN auditoriums ";
$query .= "ON lessons.AuditoriumId = auditoriums.AuditoriumId ";
$query .= "JOIN calendars ";
$query .= "ON lessons.CalendarId = calendars.CalendarId ";
$query .= "WHERE calendars.Date = " . $dateString . " ";
$query .= "AND lessons.isActive = 1 ";

$result = $database->query($query);
$dailyLessons = array();
while ($lesson = $result->fetch_row())
{
    $dailyLessons[] = $lesson;
}

foreach ($dailyLessons as $index => $lesson)
{
    if (Utilities::AuditoriumBuilding($lesson[1]) != $building)
    {
        unset($dailyLessons[$index]);
    }
}

$result = array();
foreach ($dailyLessons as $index => $lesson)
{
    if (!array_key_exists($lesson[0], $result))
    {
        $result[$lesson[0]] = array();
    }

    $result[$lesson[0]][$lesson[1]] = array("text" => $lesson[2], "hint" => $lesson[3] . "@" . $lesson[4]);
}

$eventsQuery  = "SELECT `AuditoriumEventId`, auditoriumEvents.Name as EventName, ";
$eventsQuery .= "calendars.Date as eventDate, rings.Time as eventTime, auditoriums.Name as eventAuditorium ";
$eventsQuery .= "FROM `auditoriumEvents` ";
$eventsQuery .= "JOIN calendars ";
$eventsQuery .= "ON auditoriumEvents.CalendarId = calendars.CalendarId ";
$eventsQuery .= "JOIN rings ";
$eventsQuery .= "ON auditoriumEvents.RingId = rings.RingId ";
$eventsQuery .= "JOIN auditoriums ";
$eventsQuery .= "ON auditoriumEvents.AuditoriumId = auditoriums.AuditoriumId ";
$eventsQuery .= "WHERE calendars.Date = " . $dateString . " ";

$evtsResult = $database->query($eventsQuery);
while ($event = $evtsResult->fetch_assoc())
{
    if (Utilities::AuditoriumBuilding($event["eventAuditorium"]) == $building)
    {
        $pos = mb_strpos($event["EventName"], "@");
        if ($pos != 0)
        {
            $eventText = mb_substr($event["EventName"], 0, $pos);
            $eventHint = mb_substr($event["EventName"], $pos+1, mb_strlen($event["EventName"]));
        }
        else
        {
            $eventText = $event["EventName"];
            $eventHint = "";
        }

        if (!array_key_exists($event["eventTime"], $result))
        {
            $result[$event["eventTime"]] = array();
        }

        if (!array_key_exists($event["eventAuditorium"],  $result[$event["eventTime"]]))
        {
            $result[$event["eventTime"]][$event["eventAuditorium"]] =
                array("text" => $eventText, "hint" => $eventHint);
        }
        else
        {
            $result[$event["eventTime"]][$event["eventAuditorium"]]["text"] .= "<br />" . $eventText;
            $result[$event["eventTime"]][$event["eventAuditorium"]]["hint"] .= $eventHint;
        }
    }
}


// Сортировка результата по времени
ksort($result);

// Формируется список аудиторий
$auditoriumsList = array();
foreach ($result as $time => $timeLessons) {
    foreach (array_keys($timeLessons) as $auditorium) {
        if (!in_array($auditorium, $auditoriumsList))
        {
            $auditoriumsList[] = $auditorium;
        }
    }
}
asort($auditoriumsList);

if (empty($result))
{
    echo Utilities::TagMessage("Занятий нет.");
}
else
{

    echo "<table class=\"redHeadWhiteBodyTable\">";
    // Названия аудиторий
    echo "<tr>";
    echo "<td>Время</td>";
    foreach ($auditoriumsList as $auditorium) {
        echo "<td>";
        echo $auditorium;
        echo "</td>";
    }
    echo "</tr>";

    foreach ($result as $time => $timeLessons) {
        $lesHour = mb_substr($time, 0, 2);
        $lesMin = mb_substr($time, 3, 2);
        $timeDiff = Utilities::DiffTimeWithNowInMinutes($lesHour, $lesMin);


        $scheduleDate = DateTime::createFromFormat('Y-m-d', mb_substr($dateString, 1, mb_strlen($dateString)-2));
        $scheduleDOW = Utilities::$DOWEnToRu[date( "w", $scheduleDate->getTimestamp())];
        $todaysDOW = Utilities::$DOWEnToRu[date( "w", time())];

        if (($timeDiff < 0) && ($timeDiff > -80) && ($todaysDOW == $scheduleDOW))
        {
            $onGoing = 1;
        }
        else
        {
            $onGoing = 0;
        }
        // Время
        echo "<tr>";
        echo "<td";
        if ($onGoing == 1)
        {
            echo " style=\"background:#FFFFAA\"";
        }
        echo ">";
        echo mb_substr($time, 0, 5);
        echo "</td>";
        // Занятия
        foreach ($auditoriumsList as $auditorium) {
            echo "<td title=\"";
            if (array_key_exists($auditorium, $timeLessons))
            {
                echo $timeLessons[$auditorium]["hint"];
            }
            echo "\" ";
            if ($onGoing == 1)
            {
                echo " style=\"background:#FFFFAA\"";
            }
            echo ">";
            if (array_key_exists($auditorium, $timeLessons))
            {
                echo $timeLessons[$auditorium]["text"];
            }
            echo "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}
?>