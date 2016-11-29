<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$building = $_GET["building"];
$dateString = $_GET["date"];

require_once("Database.php");
require_once("Utilities.php");

global $database;

$query  = "SELECT ";
$query .= $dbPrefix . "rings.Time, ";
$query .= $dbPrefix . "auditoriums.Name AS AudName, ";
$query .= $dbPrefix . "studentGroups.Name AS studentGroupName, ";
$query .= $dbPrefix . "disciplines.Name AS discName, ";
$query .= $dbPrefix . "teachers.FIO, ";
$query .= $dbPrefix . "auditoriums.AuditoriumId, ";
$query .= $dbPrefix . "buildings.BuildingId ";
$query .= "FROM " . $dbPrefix . "lessons ";
$query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
$query .= "ON " . $dbPrefix . "lessons.`TeacherForDisciplineId` = " . $dbPrefix . "teacherForDisciplines.`TeacherForDisciplineId` ";
$query .= "JOIN " . $dbPrefix . "disciplines ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
$query .= "JOIN " . $dbPrefix . "studentGroups ";
$query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
$query .= "JOIN " . $dbPrefix . "teachers ";
$query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
$query .= "JOIN " . $dbPrefix . "rings ";
$query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
$query .= "JOIN " . $dbPrefix . "auditoriums ";
$query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
$query .= "JOIN " . $dbPrefix . "calendars ";
$query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
$query .= "JOIN " . $dbPrefix . "buildings ";
$query .= "ON " . $dbPrefix . "auditoriums.BuildingId = " . $dbPrefix . "buildings.BuildingId ";
$query .= "WHERE " . $dbPrefix . "calendars.Date = " . $dateString . " ";
$query .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

$result = $database->query($query);
$dailyLessons = array();
while ($lesson = $result->fetch_assoc())
{
    $dailyLessons[] = $lesson;
}

foreach ($dailyLessons as $index => $lesson)
{
    if ($lesson["BuildingId"] != $building)
    {
        unset($dailyLessons[$index]);
    }
}


$result = array();
foreach ($dailyLessons as $index => $lesson)
{
    if (!array_key_exists($lesson["Time"], $result))
    {
        $result[$lesson["Time"]] = array();
    }

    $result[$lesson["Time"]][$lesson["AudName"]] =
        array("text" => $lesson["studentGroupName"], "hint" => $lesson["discName"] . "@" . $lesson["FIO"]);
}

$eventsQuery  = "SELECT `AuditoriumEventId`, " . $dbPrefix . "auditoriumEvents.Name as EventName, ";
$eventsQuery .= $dbPrefix . "calendars.Date as eventDate, " . $dbPrefix . "rings.Time as eventTime, ";
$eventsQuery .= $dbPrefix . "auditoriums.Name as eventAuditorium, ";
$eventsQuery .= $dbPrefix . "auditoriums.AuditoriumId as eventAuditoriumId,  ";
$eventsQuery .= $dbPrefix . "buildings.BuildingId ";
$eventsQuery .= "FROM " . $dbPrefix . "auditoriumEvents ";
$eventsQuery .= "JOIN " . $dbPrefix . "calendars ";
$eventsQuery .= "ON " . $dbPrefix . "auditoriumEvents.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
$eventsQuery .= "JOIN " . $dbPrefix . "rings ";
$eventsQuery .= "ON " . $dbPrefix . "auditoriumEvents.RingId = " . $dbPrefix . "rings.RingId ";
$eventsQuery .= "JOIN " . $dbPrefix . "auditoriums ";
$eventsQuery .= "ON " . $dbPrefix . "auditoriumEvents.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
$eventsQuery .= "JOIN " . $dbPrefix . "buildings ";
$eventsQuery .= "ON " . $dbPrefix . "auditoriums.BuildingId = " . $dbPrefix . "buildings.BuildingId ";
$eventsQuery .= "WHERE " . $dbPrefix . "calendars.Date = " . $dateString . " ";

$evtsResult = $database->query($eventsQuery);
while ($event = $evtsResult->fetch_assoc())
{
    if ($event["BuildingId"] == $building)
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

    echo "<div id=\"schoolAuds\"></div>";
}
?>