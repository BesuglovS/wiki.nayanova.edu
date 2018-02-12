<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["groupId"];
$schedulePrefix = "old_";
//$schedulePrefix = "";

require_once("Database.php");
require_once("Utilities.php");

global $database;

$todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = gmdate("y.m.d H:i:s", $todayStamp);
$statisticQuery  = "INSERT INTO " . $dbPrefix . "sessionStats( DateTime, GroupId ) ";
$statisticQuery .= "VALUES ( \"";
$statisticQuery .= $today;
$statisticQuery .= "\", \"";
$statisticQuery .= $groupId;
$statisticQuery .= "\")";

$database->query($statisticQuery);

$groupsQuery  = "SELECT DISTINCT " . $schedulePrefix . $dbPrefix . "studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM " . $schedulePrefix . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT " . $schedulePrefix . $dbPrefix . "studentsInGroups.StudentId ";
$groupsQuery .= "FROM " . $schedulePrefix . $dbPrefix . "studentsInGroups ";
$groupsQuery .= "JOIN " . $schedulePrefix . $dbPrefix . "studentGroups ";
$groupsQuery .= "ON " . $schedulePrefix . $dbPrefix . "studentsInGroups.StudentGroupId = " . $schedulePrefix . $dbPrefix . "studentGroups.StudentGroupId ";
$groupsQuery .= "JOIN " . $schedulePrefix . $dbPrefix . "students ";
$groupsQuery .= "ON " . $schedulePrefix . $dbPrefix . "studentsInGroups.StudentId = " . $schedulePrefix . $dbPrefix . "students.StudentId ";
$groupsQuery .= "WHERE " . $schedulePrefix . $dbPrefix . "studentGroups.StudentGroupId = ". $groupId ." ";
$groupsQuery .= "AND " . $schedulePrefix . $dbPrefix . "students.Expelled = 0 ";
$groupsQuery .= ")";

$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = $schedulePrefix . $dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$disciplinesQuery  = "SELECT " . $schedulePrefix . $dbPrefix . "disciplines.DisciplineId, Name, FIO ";
$disciplinesQuery .= "FROM " . $schedulePrefix . $dbPrefix . "disciplines ";
$disciplinesQuery .= "JOIN " . $schedulePrefix . $dbPrefix . "teacherForDisciplines ";
$disciplinesQuery .= "ON " . $schedulePrefix . $dbPrefix . "disciplines.DisciplineId = " . $schedulePrefix . $dbPrefix . "teacherForDisciplines.DisciplineId ";
$disciplinesQuery .= "JOIN " . $schedulePrefix . $dbPrefix . "teachers ";
$disciplinesQuery .= "ON " . $schedulePrefix . $dbPrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . $dbPrefix . "teachers.TeacherId ";
$disciplinesQuery .= "WHERE " . $groupCondition . " ";
$disciplinesQuery .= "AND ((Attestation = 2) OR (Attestation = 3)) ";

$discIdsResult = $database->query($disciplinesQuery);

$discIdsArray = array();
$discNames = array();
while ($id = $discIdsResult->fetch_assoc())
{
    $discIdsArray[] = $id["DisciplineId"];
    $discNames[$id["DisciplineId"]]["DiscName"] = $id["Name"];
    $discNames[$id["DisciplineId"]]["FIO"] = $id["FIO"];
}

if (count($discIdsArray) == 0)
{
    echo Utilities::TagMessage("Нет экзаменов.");
    exit;
}

$discCondition = " DisciplineId IN ( " . implode(" , ", $discIdsArray) . " )";

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . $dbPrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}


$examsQuery  = "SELECT DisciplineId, ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId ";
$examsQuery .= "FROM " . $dbPrefix . "exams ";
$examsQuery .= "WHERE " . $discCondition . " ";
$examsQuery .= "AND IsActive = 1 ";

$examsQueryResult = $database->query($examsQuery);
$exams = array();
while ($exam = $examsQueryResult->fetch_assoc())
{
    $exams[] = $exam;
}

function exams_sort($a, $b) {
    $aDate = DateTime::createFromFormat('d.m.Y H:i',$a["ConsultationDateTime"]);
    $bDate = DateTime::createFromFormat('d.m.Y H:i',$b["ConsultationDateTime"]);

    if ($aDate == $bDate) {
        return 0;
    }

    return ($aDate < $bDate) ? -1 : 1;
}

uasort($exams, 'exams_sort');

for ($i = 0; $i < count($exams); $i++) {
    if ($exams[$i]["ConsultationAuditoriumId"] != 0)
    {
        $exams[$i]["ConsultationAuditoriumName"] = $auditoriums[$exams[$i]["ConsultationAuditoriumId"]];
    }
    else
    {
        $exams[$i]["ConsultationAuditoriumName"] = "";
        $exams[$i]["ConsultationAuditoriumId"] = "";
    }

    if ($exams[$i]["ExamAuditoriumId"] != 0)
    {
        $exams[$i]["ExamAuditoriumName"] = $auditoriums[$exams[$i]["ExamAuditoriumId"]];
    }
    else
    {
        $exams[$i]["ExamAuditoriumName"] = "";
        $exams[$i]["ExamAuditoriumId"] = "";
    }

    $exams[$i]["DisciplineName"] = $discNames[$exams[$i]["DisciplineId"]]["DiscName"];
    $exams[$i]["TeacherFIO"] = $discNames[$exams[$i]["DisciplineId"]]["FIO"];

    if ($exams[$i]["ConsultationDateTime"] == "01.01.2020 0:00")
    {
        $exams[$i]["ConsultationDateTime"] = "";
    }

    if ($exams[$i]["ExamDateTime"] == "01.01.2020 0:00")
    {
        $exams[$i]["ExamDateTime"] = "";
    }
}

//echo "<pre>";
//echo print_r($exams);
//echo "</pre>";
//exit;

if (count($exams) != 0)
{
    echo "<table class=\"DailySchedule\">";
    //for ($i = 0; $i < count($exams); $i++) {
    foreach ($exams as $key => $curExam) {
        $sameAuditorium = false;
        if ($curExam["ConsultationAuditoriumName"] == $curExam["ExamAuditoriumName"])
        {
            $sameAuditorium = true;
        }

        $consultationDateTime = explode(" ", $curExam["ConsultationDateTime"]);
        $examDateTime = explode(" ", $curExam["ExamDateTime"]);

        $sameTime = false;
        if ($consultationDateTime[1] == $examDateTime[1])
        {
            $sameTime = true;
        }

        echo "<tr>";
            echo "<td>";
            echo $consultationDateTime[0];
            echo "</td>";

            echo "<td>";
            echo "К";
            echo "</td>";

            if ($sameTime)
            {
                echo "<td rowspan='2'>";
            }
            else
            {
                echo "<td>";
            }

            echo $consultationDateTime[1];
            echo "</td>";

            echo "<td rowspan=\"2\" title='" . $curExam["TeacherFIO"] . "'>";
            echo $curExam["DisciplineName"];
            echo "</td>";

            if ($sameAuditorium)
            {
                echo "<td rowspan=\"2\">";
            }
            else
            {
                echo "<td>";
            }
            echo $curExam["ConsultationAuditoriumName"];
            echo "</td>";
        echo "</tr>";



        echo "<tr>";
            echo "<td>";
            echo $examDateTime[0];
            echo "</td>";

            echo "<td>";
            echo "Э";
            echo "</td>";

            if (!$sameTime)
            {
                echo "<td>";
                echo $examDateTime[1];
                echo "</td>";
            }

            if (!$sameAuditorium)
            {
                echo "<td>";
                echo $curExam["ExamAuditoriumName"];
                echo "</td>";
            }
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo Utilities::TagMessage("Экзаменов нет.");
}