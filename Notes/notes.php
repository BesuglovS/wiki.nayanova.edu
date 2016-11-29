<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
global $database;

if (!isset($filter)) {
    $filter = $_GET["filter"];
}

$query  = "SELECT Text, LateAmount, disciplines.Name AS DiscName, ";
$query .= "teachers.fio as FIO, lessons.IsActive as lessonStatus, ";
$query .= "studentGroups.Name AS GroupName, calendars.Date, rings.Time ";
$query .= "FROM `scheduleNotes` ";
$query .= "JOIN lessons ";
$query .= "ON scheduleNotes.LessonId = lessons.LessonId ";
$query .= "JOIN calendars ";
$query .= "ON lessons.CalendarId = calendars.CalendarId ";
$query .= "JOIN rings ";
$query .= "ON lessons.RingId = rings.RingId ";
$query .= "JOIN auditoriums ";
$query .= "ON lessons.AuditoriumId = auditoriums.AuditoriumId ";
$query .= "JOIN teacherForDisciplines ";
$query .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
$query .= "JOIN teachers ";
$query .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
$query .= "JOIN disciplines ";
$query .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
$query .= "JOIN studentGroups ";
$query .= "ON disciplines.StudentGroupId = studentGroups.StudentGroupId ";
if ($filter != "") {
    $query .= "WHERE Text LIKE '%" . $filter . "%' OR disciplines.Name LIKE '%" . $filter . "%' ";
    $query .= "OR teachers.FIO LIKE '%" . $filter . "%' OR studentGroups.Name LIKE '%" . $filter . "%' ";
}
$query .= "ORDER BY calendars.Date, rings.Time, teachers.FIO ";


$qResult = $database->query($query);

echo "<table id=\"notesTable\" class='table-bordered'>";

echo "<tr>";

echo "<th>Заметка</th>";
echo "<th>Время без преподавателя</th>";
echo "<th>Дата</th>";
echo "<th>Время</th>";
echo "<th>Ф.И.О. преподавателя</th>";
echo "<th>Наименование дисциплины</th>";
echo "<th>Группа</th>";
echo "<th>Стутус урока</th>";

echo "</tr>";

$TotalTimeAmount = 0;
$count = 0;

while($note = $qResult->fetch_assoc())
{
    if ($note["lessonStatus"] == "1") {
        $TotalTimeAmount += $note["LateAmount"];
    }
    $count++;

    $lessonDate = strtotime( $note["Date"] );
    $dateFormatted = date( 'd.m.Y', $lessonDate );

    echo "<tr>";

    echo "<td>" . $note["Text"] . "</td>";
    echo "<td>" . $note["LateAmount"] . "</td>";
    echo "<td>" . $dateFormatted . "</td>";
    echo "<td>" . $note["Time"] . "</td>";
    echo "<td>" . $note["FIO"] . "</td>";
    echo "<td>" . $note["DiscName"] . "</td>";
    echo "<td>" . $note["GroupName"] . "</td>";
    echo "<td>" . (($note["lessonStatus"] == "1") ? "+" : "-") . "</td>";

    echo "</tr>";
}

echo "</table>";

echo "<h2>Итого (" . $count . ") время без преподавателя = " . $TotalTimeAmount . "</h2>";