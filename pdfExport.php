<?php

$dbPrefix = $_GET["dbPrefix"];
$faculty_id = $_GET["facultyId"];
$scheduleDOW = $_GET["dow"];

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

$facultyGroupsQuery  = "SELECT " . $dbPrefix . "GroupsInFaculties.StudentGroupId, Name ";
$facultyGroupsQuery .= "FROM " . $dbPrefix . "GroupsInFaculties ";
$facultyGroupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
$facultyGroupsQuery .= "ON " . $dbPrefix . "GroupsInFaculties.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
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

    $Gname = iconv("UTF-8","cp1251",$key);
    if (strpos($Gname, ' (+Í)') !== FALSE)
    {

    }

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

$facultyNameQuery  = "SELECT Name ";
$facultyNameQuery .= "FROM " . $dbPrefix . "faculties ";
$facultyNameQuery .= "WHERE FacultyId = " . $faculty_id;

$facultyNameQueryResult = $database->query($facultyNameQuery);

$facultyNameObject = $facultyNameQueryResult->fetch_assoc();

$facultyName = iconv("UTF-8","cp1251",$facultyNameObject["Name"]);

$ScheduleFontSize = 12;
$lineHeight = 4;

$bu = 0;

do
{
    $pdf = new FPDF('L');

    $pdf->SetMargins(5, 2.5, 0.5);
    $pdf->AddPage();
    // ========================================================================================
    $pdf->AddFont('Calibri','','calibri.php');
    $pdf->SetFont('Calibri','',10);


    $pdf->Cell(289, 5, 'Ðàñïèñàíèå', 0, 0, 'C');

    $pdf->Ln(); // Line break


    $month = intval(mb_substr($options["Semester Starts"], 5, 2));
    $year =  intval(mb_substr($options["Semester Starts"], 0, 4));

    if ($month > 6)
    {
        $pdf->Cell(289, 5, 'ïåðâîãî ñåìåñòðà ' . $year . ' - ' . ($year+1) . ' ó÷åáíîãî ãîäà', 0, 0, 'C');
    }
    else
    {
        $pdf->Cell(289, 5, 'âòîðîãî ñåìåñòðà ' . ($year-1) . ' - ' . $year . ' ó÷åáíîãî ãîäà', 0, 0, 'C');
    }

    $pdf->Ln();


    $pdf->Cell(289, 5, $facultyName, 0, 0, 'C');

    $pdf->Ln();

    $pdf->SetFont('Calibri','',14);
    $DOW = array(
        "1" => "ÏÎÍÅÄÅËÜÍÈÊ", "2" => "ÂÒÎÐÍÈÊ", "3" => "ÑÐÅÄÀ",
        "4" => "×ÅÒÂÅÐÃ", "5" => "ÏßÒÍÈÖÀ", "6" => "ÑÓÁÁÎÒÀ", "7" => "ÂÎÑÊÐÅÑÅÍÜÅ"
    );

    $pdf->Cell(289, 5, $DOW[$scheduleDOW], 0, 0, 'C');
    // ========================================================================================

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
        $groupKey = str_replace(' (+Í)', "", $groupKey);

        $pdf->Cell($cellWidth, 7, $groupKey, 1, 0, 'C');
        $i++;
    }
    $pdf->Ln();

    //foreach ($timeArray as $time) {
    for($ind = 0; $ind < count($timeArray); $ind++) {

        $RowMaxHeight = 0;

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

        $row_x = $pdf->GetX();
        $row_y = $pdf->GetY();

        $current_x = $pdf->GetX();
        $current_y = $pdf->GetY();

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
                if (($tfdData["lessons"][0]["groupName"] != str_replace(' (+Í)', "", $groupKey)) &&
                    ($tfdData["lessons"][0]["groupName"] != (str_replace(' (+Í)', "", $groupKey) . "(Í)")) &&
                    ($tfdData["lessons"][0]["groupName"] != (str_replace(' (+Í)', "", $groupKey) . " (+Í)")))
                {
                    $cellValues[$cellValueIndex] .= $tfdData["lessons"][0]["groupName"] . "\n";
                }

                $cellValues[$cellValueIndex] .= $tfdData["lessons"][0]["discName"];
                if ($tfdData["lessons"][0]["groupName"] == (str_replace(' (+Í)', "", $groupKey) . "(Í)"))
                {
                    $cellValues[$cellValueIndex] .= " (Í)";
                }
                if ($tfdData["lessons"][0]["groupName"] == (str_replace(' (+Í)', "", $groupKey) . " (+Í)"))
                {
                    $cellValues[$cellValueIndex] .= " (+Í)";
                }
                $cellValues[$cellValueIndex] .= "\n";
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

            $lineCount = 0;
            $lineCounts = array();
            for ($i = 0; $i < count($cellValues); $i++) {
                $cnt = mb_substr_count($cellValues[$i],"\n");
                $lineCounts[] = $cnt;
                $lineCount += $cnt;
            }

            //$lineHeight = 2;
            $pdf->SetFont('Calibri','',$ScheduleFontSize);

            $cellValueCount = count($cellValues);

            $MultiHeight = $current_y;
            for ($i = 0; $i < $cellValueCount; $i++) {
                $pdf->SetXY($current_x, $MultiHeight);

                $pdf->MultiCell($cellWidth, $lineHeight, $cellValues[$i], 0);

                $MultiHeight = $pdf->GetY();

                if ($i != $cellValueCount-1)
                {
                    $pdf->Line($current_x, $MultiHeight, $current_x + $cellWidth, $MultiHeight);
                }
            }

            if (($MultiHeight - $row_y) > $RowMaxHeight)
            {
                $RowMaxHeight = $MultiHeight - $row_y;
            }

            $pdf->SetXY($current_x + $cellWidth, $current_y);
        }

        $pdf->Rect($row_x,$row_y,30,$RowMaxHeight);
        $pdf->SetFont('Calibri','',12);
        $pdf->SetXY($row_x, $row_y);
        $pdf->MultiCell(30, $RowMaxHeight, $timeString, 0, 'C');

        for($ii = 0; $ii < count($groups); $ii++) {
            $pdf->Rect($row_x + 30 + $ii*$cellWidth, $row_y, $cellWidth, $RowMaxHeight);
        }
    }

    $pn = $pdf->PageNo();

    $ScheduleFontSize--;
    $lineHeight -= 1/3;

}while($pn > 1);

$pdf->Output();