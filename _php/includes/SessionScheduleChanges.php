<?php
header("Content-type: text/html; charset=utf-8");

$dbPrefix = $_GET["dbPrefix"];
$groupId = $_GET["groupId"];
$schedulePrefix = "old_";

require_once("Database.php");
require_once("Utilities.php");

global $database;

$todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = gmdate("y.m.d H:i:s", $todayStamp);
$statisticQuery  = "INSERT INTO " . $dbPrefix . "sessionChangesStats( DateTime, GroupId ) ";
$statisticQuery .= "VALUES ( \"";
$statisticQuery .= $today;
$statisticQuery .= "\", \"";
$statisticQuery .= $groupId;
$statisticQuery .= "\")";

$database->query($statisticQuery);

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . $dbPrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}

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

$discCondition = " " . $dbPrefix . "exams.DisciplineId IN ( " . implode(" , ", $discIdsArray) . " )";

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . $dbPrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}

$examsQuery  = "SELECT ExamId, " . $dbPrefix . "exams.DisciplineId, ";
$examsQuery .= "ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId, ";
$examsQuery .= $schedulePrefix . $dbPrefix . "disciplines.Name, " . $schedulePrefix . $dbPrefix . "teachers.FIO ";
$examsQuery .= "FROM " . $dbPrefix . "exams ";
$examsQuery .= "JOIN " . $schedulePrefix . $dbPrefix . "teacherForDisciplines ";
$examsQuery .= "ON " . $dbPrefix . "exams.DisciplineId = " . $schedulePrefix . $dbPrefix . "teacherForDisciplines.DisciplineId ";
$examsQuery .= "JOIN " . $schedulePrefix . $dbPrefix . "teachers ";
$examsQuery .= "ON " . $schedulePrefix . $dbPrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . $dbPrefix . "teachers.TeacherId ";
$examsQuery .= "JOIN " . $schedulePrefix . "disciplines ";
$examsQuery .= "ON " . $dbPrefix . "exams.DisciplineId = " . $schedulePrefix . $dbPrefix . "disciplines.DisciplineId ";
$examsQuery .= "WHERE " . $discCondition . " ";

$examsQueryResult = $database->query($examsQuery);
$exams = array();
$examIds = array();
while ($exam = $examsQueryResult->fetch_assoc())
{
    $exams[$exam["ExamId"]] = $exam;
    $examIds[] = $exam["ExamId"];
}

$logEventQuery  = "SELECT OldExamId, NewExamId, DateTime ";
$logEventQuery .= "FROM " . $dbPrefix . "examsLogEvents ";
$logEventQuery .= "WHERE ";
$logEventQuery .= "OldExamId IN ( " . implode(" , ", $examIds) . " ) ";
$logEventQuery .= "OR ";
$logEventQuery .= "NewExamId IN ( " . implode(" , ", $examIds) . " ) ";
$logEventQuery .= "ORDER BY DateTime DESC ";

$logEventsQueryResult = $database->query($logEventQuery);

$result = array();
while ($logEvent = $logEventsQueryResult->fetch_assoc())
{
    $logItem = array();
    $logItem["DateTime"] = $logEvent["DateTime"];

    $oldExam = $exams[$logEvent["OldExamId"]];
    $newExam = $exams[$logEvent["NewExamId"]];

    $logItem["NameFIO"] = $oldExam["Name"] . "<br />" . $oldExam["FIO"];

    $logItem["OldExamString"]  = "Консультация " . "<br />";
    if ($oldExam["ConsultationDateTime"] != "01.01.2020 0:00")
    {
        $logItem["OldExamString"] .= $oldExam["ConsultationDateTime"] . " ";
    }
    if ($oldExam["ConsultationAuditoriumId"] != 0)
    {
        $logItem["OldExamString"] .= $auditoriums[$oldExam["ConsultationAuditoriumId"]];
    }
    $logItem["OldExamString"] .= "<br />";
    $logItem["OldExamString"] .= "Экзамен " . "<br />";
    if ($oldExam["ExamDateTime"] != "01.01.2020 0:00")
    {
        $logItem["OldExamString"] .= $oldExam["ExamDateTime"] . " ";
    }
    if ($oldExam["ExamAuditoriumId"] != 0)
    {
        $logItem["OldExamString"] .= $auditoriums[$oldExam["ExamAuditoriumId"]];
    }

    $logItem["NewExamString"]  = "Консультация " . "<br />";
    if ($newExam["ConsultationDateTime"] != "01.01.2020 0:00")
    {
        $logItem["NewExamString"] .= $newExam["ConsultationDateTime"] . " ";
    }
    if ($newExam["ConsultationAuditoriumId"] != 0)
    {
        $logItem["NewExamString"] .= $auditoriums[$newExam["ConsultationAuditoriumId"]];
    }
    $logItem["NewExamString"] .= "<br />";
    $logItem["NewExamString"] .= "Экзамен " . "<br />";
    if ($newExam["ExamDateTime"] != "01.01.2020 0:00")
    {
        $logItem["NewExamString"] .= $newExam["ExamDateTime"] . " ";
    }
    if ($newExam["ExamAuditoriumId"] != 0)
    {
        $logItem["NewExamString"] .= $auditoriums[$newExam["ExamAuditoriumId"]];
    }

    $result[] = $logItem;
}

function event_sort($a, $b) {
    $aDateTimeString = $a["DateTime"];
    $bDateTimeString = $b["DateTime"];

    // 16.12.2013 10:10
    $aDateTimeStamp = DateTime::createFromFormat('d.m.Y H:i', $aDateTimeString)->getTimestamp();
    $bDateTimeStamp = DateTime::createFromFormat('d.m.Y H:i', $bDateTimeString)->getTimestamp();

    $diff = $aDateTimeStamp - $bDateTimeStamp;

    return ($diff > 0) ? -1 : 1;
}

usort($result, "event_sort");

if (count($result) != 0)
{
    echo "<table class=\"DOWSchedule\">";
    echo "<tr>";
    echo "  <td>Дата и время изменения</td>";
    echo "  <td>Дисциплина</td>";
    echo "  <td>Старый вариант</td>";
    echo "  <td>Новый вариант</td>";
    echo "</tr>";
    for ($i = 0; $i < count($result); $i++) {
        echo "<tr>";
        echo "  <td>" . $result[$i]["DateTime"] . "</td>";
        echo "  <td>" . $result[$i]["NameFIO"] . "</td>";
        echo "  <td>" . $result[$i]["OldExamString"] . "</td>";
        echo "  <td>" . $result[$i]["NewExamString"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
else
{
    echo Utilities::TagMessage("Нет записей.");
}