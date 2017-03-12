<?php

require_once("Database.php");

class ZachDates {
    public function GetNearestZach($dbPrefix, $studentId){


        global $database;

        $groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
        $groupsQuery .= "WHERE StudentId = " . $studentId;

        $groupIdsResult = $database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc())
        {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = "WHERE " . $dbPrefix . "studentGroups.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $query  = "SELECT DISTINCT " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId, " . $dbPrefix . "disciplines.Name, " . $dbPrefix . "disciplines.Attestation, " . $dbPrefix . "disciplines.AuditoriumHours, ";
        $query .= $dbPrefix . "studentGroups.Name  AS GroupName, " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId as TFDID, ";
        $query .= $dbPrefix . "teachers.FIO ";
        $query .= "FROM " . $dbPrefix . "disciplines ";
        $query .= "LEFT JOIN " . $dbPrefix . "studentGroups ";
        $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
        $query .= "LEFT JOIN " . $dbPrefix . "teacherForDisciplines ";
        $query .= "ON " . $dbPrefix . "disciplines.DisciplineId = " . $dbPrefix . "teacherForDisciplines.DisciplineId ";
        $query .= "LEFT JOIN " . $dbPrefix . "teachers ";
        $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId =  " . $dbPrefix . "teachers.TeacherId ";


        $query .= $groupCondition;

        $discListQueryResult = $database->query($query);


        $result = "";

        $count = 0;

        $discList = array();
        while($disc = $discListQueryResult->fetch_assoc())
        {
            if (($disc["Attestation"] == 1) ||
                ($disc["Attestation"] == 3) ||
                ($disc["Attestation"] == 4)) {
                $lastLessonQuery = "";
                $lastLessonQuery .= "SELECT Date ";
                $lastLessonQuery .= "FROM ". $dbPrefix . "lessons  ";
                $lastLessonQuery .= "JOIN calendars ON lessons.CalendarId = calendars.CalendarId ";
                $lastLessonQuery .= "WHERE `TeacherForDisciplineId` = " . $disc["TeacherForDisciplineId"] . " ";
                $lastLessonQuery .= "AND `IsActive` = 1 ";
                $lastLessonQuery .= "ORDER BY Date DESC ";
                $lastLessonQuery .= "LIMIT 1 ";

//                echo "<script>";
//                echo "console.log('" . $disc["Name"] . "');";
//                echo "console.log('" . $lastLessonQuery . "');";
//                echo "</script>";

                $llqResult = $database->query($lastLessonQuery);

                if ($llqResult) {
                    $llqData = $llqResult->fetch_assoc();
                    $discipline = array();
                    $discipline["DisciplineId"] = $disc["DisciplineId"];
                    $discipline["Name"] = $disc["Name"];
                    $discipline["Attestation"] = $disc["Attestation"];
                    $discipline["GroupName"] = $disc["GroupName"];
                    $discipline["teacherFIO"] = $disc["FIO"];
                    $discipline["lastLessonDate"] = $llqData["Date"];
                    $discList[] = $discipline;
                    $count++;
                }
            }
        }

        function cmp($a, $b)
        {
            $aDate = DateTime::createFromFormat('Y-m-d', $a["lastLessonDate"]);
            $bDate = DateTime::createFromFormat('Y-m-d', $b["lastLessonDate"]);

            if ($aDate > $bDate) {
                $result = 1;
            } else {
                if ($aDate < $bDate) {
                    $result = -1;
                } else {
                    $result = 0;
                }
            }

            return $result;
        }

//        $result .= "<pre>";
//        $result .= print_r($discList);
//        $result .= "</pre>";

        usort($discList, "cmp");

        $result .= "<div id='ZachList'>";
        $result .= "<h4>Зачёты / Даты последних занятий</h4>";
        $result .= "<table style='width: 100%'>";

        foreach ($discList as $discData) {
            $style = $this->GetStyle($discData["lastLessonDate"]);

            $result .= "<tr class='" . $style . "'>";
            $result .= "<td style='text-align: left'>" .
                $discData["Name"] . " (" . $discData["GroupName"] . ")\n" .
                $discData["teacherFIO"] .
                "</td>";
            $result .= "<td style='text-align: center; vertical-align: middle'>" .
                $this->ReformatDate($discData["lastLessonDate"]) .
                "</td>";
            $result .= "</tr>";
        }

        $result .= "</table>";
        $result .= "</div>";

        return $result;
    }

    public function ReformatDate($semesterStarts)
    {
        $semesterStartsCorrectFormat =
            mb_substr($semesterStarts, 8, 2) . "." .
            mb_substr($semesterStarts, 5, 2) . "." .
            mb_substr($semesterStarts, 0, 4);
        return $semesterStartsCorrectFormat;
    }

    private function GetStyle($lastLessonDate)
    {
        $now = new DateTime();
        $Date = DateTime::createFromFormat('Y-m-d', $lastLessonDate);

        if ($now > $Date) {
            $result = "tooLate";
        } else {
            if ($now < $Date) {
                $interval = $now->diff($Date)->days;
                if ($interval == 1) {
                    return "tomorrow";
                }
                
                $result = "notYet";
            } else {
                $result = "todayGreatDay";
            }
        }

        return $result;
    }
}