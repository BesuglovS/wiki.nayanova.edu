<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");
require_once("Utilities.php");

global $database;

$groupId = $_GET["groupId"];

$schedulePrefix = "old_";

$todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = gmdate("y.m.d H:i:s", $todayStamp);
$statisticQuery  = "INSERT INTO sessionChangesStats( DateTime, GroupId ) ";
$statisticQuery .= "VALUES ( \"";
$statisticQuery .= $today;
$statisticQuery .= "\", \"";
$statisticQuery .= $groupId;
$statisticQuery .= "\")";

$database->query($statisticQuery);

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}

$groupsQuery  = "SELECT DISTINCT " . $schedulePrefix . "studentsInGroups.StudentGroupId ";
$groupsQuery .= "FROM " . $schedulePrefix . "studentsInGroups ";
$groupsQuery .= "WHERE StudentId ";
$groupsQuery .= "IN ( ";
$groupsQuery .= "SELECT " . $schedulePrefix . "studentsInGroups.StudentId ";
$groupsQuery .= "FROM " . $schedulePrefix . "studentsInGroups ";
$groupsQuery .= "JOIN " . $schedulePrefix . "studentGroups ";
$groupsQuery .= "ON " . $schedulePrefix . "studentsInGroups.StudentGroupId = " . $schedulePrefix . "studentGroups.StudentGroupId ";
$groupsQuery .= "JOIN " . $schedulePrefix . "students ";
$groupsQuery .= "ON " . $schedulePrefix . "studentsInGroups.StudentId = " . $schedulePrefix . "students.StudentId ";
$groupsQuery .= "WHERE " . $schedulePrefix . "studentGroups.StudentGroupId = ". $groupId ." ";
$groupsQuery .= "AND " . $schedulePrefix . "students.Expelled = 0 ";
$groupsQuery .= ")";
$groupIdsResult = $database->query($groupsQuery);

$groupIdsArray = array();
while ($id = $groupIdsResult->fetch_assoc())
{
    $groupIdsArray[] = $id["StudentGroupId"];
}
$groupCondition = $schedulePrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

$disciplinesQuery  = "SELECT " . $schedulePrefix . "disciplines.DisciplineId, Name, FIO ";
$disciplinesQuery .= "FROM " . $schedulePrefix . "disciplines ";
$disciplinesQuery .= "JOIN " . $schedulePrefix . "teacherForDisciplines ";
$disciplinesQuery .= "ON " . $schedulePrefix . "disciplines.DisciplineId = " . $schedulePrefix . "teacherForDisciplines.DisciplineId ";
$disciplinesQuery .= "JOIN " . $schedulePrefix . "teachers ";
$disciplinesQuery .= "ON " . $schedulePrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . "teachers.TeacherId ";
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

$discCondition = " exams.DisciplineId IN ( " . implode(" , ", $discIdsArray) . " )";

$auditoriumQuery  = "SELECT AuditoriumId, Name ";
$auditoriumQuery .= "FROM " . $schedulePrefix . "auditoriums";
$auditoriumsResult = $database->query($auditoriumQuery);

$auditoriums = array();
while ($auditorium = $auditoriumsResult->fetch_assoc())
{
    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
}

$examsQuery  = "SELECT ExamId, exams.DisciplineId, ";
$examsQuery .= "ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId, ";
$examsQuery .= $schedulePrefix . "disciplines.Name, " . $schedulePrefix . "teachers.FIO ";
$examsQuery .= "FROM exams ";
$examsQuery .= "JOIN " . $schedulePrefix . "teacherForDisciplines ";
$examsQuery .= "ON exams.DisciplineId = " . $schedulePrefix . "teacherForDisciplines.DisciplineId ";
$examsQuery .= "JOIN " . $schedulePrefix . "teachers ";
$examsQuery .= "ON " . $schedulePrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . "teachers.TeacherId ";
$examsQuery .= "JOIN " . $schedulePrefix . "disciplines ";
$examsQuery .= "ON exams.DisciplineId = " . $schedulePrefix . "disciplines.DisciplineId ";
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
$logEventQuery .= "FROM examsLogEvents ";
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