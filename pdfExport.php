<?php

require('fpdf17/fpdf.php');
require_once("_php/includes/Database.php");
require_once("_php/includes/ConfigOptions.php");
require_once("_php/includes/Utilities.php");
header("Content-type: text/html; charset=windows-1251");

global $database;
global $options;

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
$scheduleDOW = $_GET["dow"];

$facultyGroupsQuery  = "SELECT GroupsInFaculties.StudentGroupId, Name ";
$facultyGroupsQuery .= "FROM `GroupsInFaculties` ";
$facultyGroupsQuery .= "JOIN studentGroups ";
$facultyGroupsQuery .= "ON GroupsInFaculties.StudentGroupId = studentGroups.StudentGroupId ";
$facultyGroupsQuery .= "WHERE FacultyId = " . $faculty_id;

$facultyGroupsResult = $database->query($facultyGroupsQuery);

$groups = array();
while ($group = $facultyGroupsResult->fetch_assoc())
{
    $groups[iconv("UTF-8","cp1251", $group["Name"])] = array (
        "group_id" => $group["StudentGroupId"]
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
        $lesson["discName"] = iconv("UTF-8","cp1251",$lesson["discName"]);
        $lesson["teacherFIO"] = iconv("UTF-8","cp1251",$lesson["teacherFIO"]);
        $lesson["auditoriumName"] = iconv("UTF-8","cp1251",$lesson["auditoriumName"]);
        $lesson["groupName"] = iconv("UTF-8","cp1251",$lesson["groupName"]);

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

class PDF extends FPDF
{
    public $options;
    public $facultyName;
    public $dow;

    // Page header
    function __construct($opt, $fName, $dayOfWeek)
    {
        $this->options = $opt;
        $this->facultyName = $fName;
        $this->dow = $dayOfWeek;

        parent::__construct('L');
    }

    function Header()
    {
        $this->AddFont('Calibri','','calibri.php');
        $this->SetFont('Calibri','',10);

        $this->Cell(289, 5, 'Ðàñïèñàíèå', 0, 0, 'C');

        $this->Ln(); // Line break

        $month = intval(mb_substr($this->options["Semester Starts"], 5, 2));
        $year =  intval(mb_substr($this->options["Semester Starts"], 0, 4));

        if ($month > 6)
        {
            $this->Cell(289, 5, 'ïåðâîãî ñåìåñòðà ' . $year . ' - ' . ($year+1) . ' ó÷åáíîãî ãîäà', 0, 0, 'C');
        }
        else
        {
            $this->Cell(289, 5, 'âòîðîãî ñåìåñòðà ' . ($year-1) . ' - ' . $year . ' ó÷åáíîãî ãîäà', 0, 0, 'C');
        }

        $this->Ln();

        $this->Cell(289, 5, $this->facultyName, 0, 0, 'C');

        $this->Ln();

        $DOW = array(
            "1" => "ÏÎÍÅÄÅËÜÍÈÊ", "2" => "ÂÒÎÐÍÈÊ", "3" => "ÑÐÅÄÀ",
            "4" => "×ÅÒÂÅÐÃ", "5" => "ÏßÒÍÈÖÀ", "6" => "ÑÓÁÁÎÒÀ", "7" => "ÂÎÑÊÐÅÑÅÍÜÅ"
        );

        $this->Cell(289, 5, $DOW[$this->dow], 0, 0, 'C');
    }
}

$facultyNameQuery  = "SELECT Name ";
$facultyNameQuery .= "FROM faculties ";
$facultyNameQuery .= "WHERE FacultyId = " . $faculty_id;

$facultyNameQueryResult = $database->query($facultyNameQuery);

$facultyNameObject = $facultyNameQueryResult->fetch_assoc();

$facultyName = iconv("UTF-8","cp1251",$facultyNameObject["Name"]);


$pdf = new PDF($options, $facultyName, $scheduleDOW);
$pdf->SetMargins(5, 2.5, 0.5);
$pdf->AddPage();

if ($dayOff == 1)
{
    $pdf->Ln(80);
    $pdf->SetFont('Calibri','',50);
    $pdf->Cell(0,0,'ÂÛÕÎÄÍÎÉ!', 0, 0, 'C');

    $pdf->Output();
    exit;
}

$pdf->Ln();
$pdf->Cell(30,7,'Âðåìÿ',1,0,'C');

$cellWidth = 256 / count($groups);

$i = 1;
foreach ($groups as $groupKey => $groupData)
{
    $pdf->Cell($cellWidth, 7, $groupKey, 1, 0, 'C');
    $i++;
}
$pdf->Ln();

$rowHeight = 158 / count($timeArray);

//foreach ($timeArray as $time) {
for($ind = 0; $ind < count($timeArray); $ind++) {

    $values = array_values($timeArray);
    $time = $values[$ind];

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


    if ($onGoing == 1)
    {
        //echo " style=\"background:#FFFFAA\"";
    }

    $current_x = $pdf->GetX();
    $current_y = $pdf->GetY();

    $pdf->Rect($current_x,$current_y,30,$rowHeight);

    $pdf->SetFont('Calibri','',10);

    $hour = intval(mb_substr($time,0,2));
    $minute = intval(mb_substr($time,3,2));
    $minute += 80;

    while ($minute >= 60) {
        $hour++;
        $minute -= 60;
    }
    if ($minute < 10)
    {
        $minute = '0' . $minute;
    }

    $timeString = $time . " - " . $hour . ":" . $minute;

    $pdf->MultiCell(30, $rowHeight, $timeString, 0, 'C');

    $pdf->SetXY($current_x + 30, $current_y);


    foreach ($groups as $groupKey => $groupData) {
        //$groupData["Schedule"][$time]

        if ($onGoing == 1)
        {
            //echo " style=\"background:#FFFFAA\"";
        }

        $cellValues = array();
        $cellValues[] = "";
        $cellValueIndex = 0;

        $splitCounter = 0;

        usort($groupData["Schedule"][$time], "tfdSort");

        foreach ($groupData["Schedule"][$time] as $tfdId => $tfdData)
        {
            if ($tfdData["lessons"][0]["groupName"] != $groupKey)
            {
                $cellValues[$cellValueIndex] .= $tfdData["lessons"][0]["groupName"] . "\n";
            }
            $cellValues[$cellValueIndex] .= $tfdData["lessons"][0]["discName"] . "\n";
            $cellValues[$cellValueIndex] .= $tfdData["lessons"][0]["teacherFIO"] . "\n";

            $commonWeeks = array();
            foreach ($tfdData["weeksAndAuds"] as $curAud => $weekArray)
            {
                foreach ($weekArray as $weekNum) {
                    $commonWeeks[] = $weekNum;
                }
            }
            $cellValues[$cellValueIndex] .=  "(" . iconv("UTF-8","cp1251",Utilities::GatherWeeksToString($commonWeeks)) . ")\n";

            // TODO ñîðòèðîâàòü íåäåëè àóäèòîðèé ïî ïîðÿäêó
            if (count($tfdData["weeksAndAuds"]) > 1)
            {
                foreach ($tfdData["weeksAndAuds"] as $audName => $currentWeekList)
                {
                    $cellValues[$cellValueIndex] .=  iconv("UTF-8","cp1251",Utilities::GatherWeeksToString($currentWeekList)) . " - ";
                    $cellValues[$cellValueIndex] .=  $audName . "\n";
                }
            }
            else
            {
                foreach ($tfdData["weeksAndAuds"] as $audName => $weekList)
                {
                    $cellValues[$cellValueIndex] .=  $audName . "\n";
                }
            }

            $cnt = count($groupData["Schedule"][$time]);
            if (($cnt != 1) && ($splitCounter != $cnt-1))
            {
                //echo "<hr />";
                $cellValues[] = "";
                $cellValueIndex++;
            }

            $splitCounter++;
        }

        $current_y = $pdf->GetY();
        $current_x = $pdf->GetX();

        $pdf->Rect($current_x,$current_y,$cellWidth,$rowHeight);


        $lineCount = 0;
        $lineCounts = array();
        for ($i = 0; $i < count($cellValues); $i++) {
            $cnt = mb_substr_count($cellValues[$i],"\n");
            $lineCounts[] = $cnt;
            $lineCount += $cnt;
        }

        $lineHeight = 10;
        if ($lineCount != 0)
        {
            $lineHeight = $rowHeight / $lineCount;
        }
        if ($lineHeight > 5)
        {
            $lineHeight = 5;
        }
        $pdf->SetFont('Calibri','',10);
        switch ($lineHeight) {
            case 4:
                $pdf->SetFont('Calibri','',8);
                break;
            case 3:
                $pdf->SetFont('Calibri','',6);
                break;
            case 2:
                $pdf->SetFont('Calibri','',4);
                break;
            case 1:
                $pdf->SetFont('Calibri','',2);
                break;
        }

        $cellValueCount = count($cellValues);

        for ($i = 0; $i <= $cellValueCount; $i++) {
            $curHeight = 0;
            for ($j = 0; $j < $i; $j++) {
                $curHeight += $lineHeight*$lineCounts[$j];
            }
            $pdf->SetXY($current_x, $current_y + $curHeight);

            $pdf->MultiCell($cellWidth, $lineHeight, $cellValues[$i], 0);

            if ($i != $cellValueCount-1)
            {
                $pdf->Line(
                    $current_x, $current_y + ($i+1)*$lineHeight*$lineCounts[$i],
                    $current_x + $cellWidth, $current_y + ($i+1)*$lineHeight*$lineCounts[$i]);
            }
        }

        $pdf->SetXY($current_x + $cellWidth, $current_y);
    }

    if ($ind != count($timeArray)-1)
    {
        $pdf->Ln($rowHeight);
    }
}

$pdf->Output();
