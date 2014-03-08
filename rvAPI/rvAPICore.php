<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Utilities.php");

global $database;

class RVAPI {

    public $database;

    public function __construct($db){
        $this->database = $db;
    }

    public function get_faculties() {
        $faculties["faculties"] = array();
        $faculties["faculties"][] = array (
            "faculty_name" => "факультет математики и компьютерных наук",
            "faculty_id" => "1",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "философский факультет",
            "faculty_id" => "2",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "химико-биологический факультет",
            "faculty_id" => "3",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "экономический факультет",
            "faculty_id" => "4",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "юридический факультет",
            "faculty_id" => "5",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "факультет международных отношений",
            "faculty_id" => "6",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "факультет управления",
            "faculty_id" => "7",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );
        $faculties["faculties"][] = array (
            "faculty_name" => "факультет туризма",
            "faculty_id" => "8",
            "date_start" => "03.02.2014",
            "date_end" => "08.06.2014"
        );

        $result = json_encode($faculties);

        return $result;
    }

    public function get_groups($faculty_id) {
        $FacultyGroupsList = array(
            "1" => array("12 А", "13 А"),
            "2" => array("12 Б", "13 Б", "14 Б", "15 Б"),
            "3" => array("12 В0", "12 В", "13 В", "14 В", "15 В"),
            "4" => array("12 Г", "12 Г(Н)", "13 Г", "13 Г(Н)", "14 Г", "14 Г(Н)"),
            "5" => array("12 Д", "12 Д(Н)", "13 Д", "13 Д(Н)", "14 Д", "14 Д(Н)", "15 Д"),
            "6" => array("12 Е", "13 Е", "14 Е", "15 Е"),
            "7" => array("12 У", "13 У", "14 У", "15 У"),
            "8" => array("12 Т", "13 Т", "14 Т")
        );
        $FacultyGroups = $FacultyGroupsList[$faculty_id];

        $groups["groups"] = array();
        foreach ($FacultyGroups as $GroupName) {
            $idQuery  = "SELECT studentGroups.StudentGroupId ";
            $idQuery .= "FROM studentGroups ";
            $idQuery .= "WHERE studentGroups.Name = \"" . $GroupName . "\"";

            $qResult = $this->database->query($idQuery);
            $result = $qResult->fetch_assoc();
            $groupId = $result["StudentGroupId"];

            $groups["groups"][] = array (
                "group_name" => $GroupName,
                "group_id" => $groupId
            );
        }

        $result = json_encode($groups);

        return $result;
    }

    public function get_schedule($group_id) {

        $BuildingAddress = array(
            "Mol" => "ул. Молодогвардейская, д.196, корп. 2",
            "Jar" => "ул. Ярмарочная, д.17, корп. 3"
        );

        $nameQuery  = "SELECT studentGroups.Name ";
        $nameQuery .= "FROM studentGroups ";
        $nameQuery .= "WHERE studentGroups.StudentGroupId = " . $group_id;

        $qResult = $this->database->query($nameQuery);
        $result = $qResult->fetch_assoc();
        $groupName = $result["Name"];

        $groupSchedule["group_name"] = $groupName;

        $groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT studentsInGroups.StudentId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "JOIN studentGroups ";
        $groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
        $groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $group_id ." ";
        $groupsQuery .= ")";

        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc())
        {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = "WHERE disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $allLessonsQuery  = "SELECT disciplines.Name as discName, rings.Time as startTime, ";
        $allLessonsQuery .= "calendars.Date as date, teachers.FIO as teacherFIO, auditoriums.Name as auditoriumName ";
        $allLessonsQuery .= "FROM lessons ";
        $allLessonsQuery .= "JOIN teacherForDisciplines ";
        $allLessonsQuery .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
        $allLessonsQuery .= "JOIN teachers ";
        $allLessonsQuery .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
        $allLessonsQuery .= "JOIN disciplines ";
        $allLessonsQuery .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
        $allLessonsQuery .= "JOIN calendars ";
        $allLessonsQuery .= "ON lessons.CalendarId = calendars.calendarId ";
        $allLessonsQuery .= "JOIN auditoriums ";
        $allLessonsQuery .= "ON lessons.auditoriumId = auditoriums.AuditoriumId ";
        $allLessonsQuery .= "JOIN rings ";
        $allLessonsQuery .= "ON lessons.ringId = rings.ringId ";
        $allLessonsQuery .= $groupCondition;
        $allLessonsQuery .= "AND lessons.isActive = 1 ";

        $groupSchedule["days"] = array();
        for ($i = 1; $i <= 7; $i++) {
            $groupSchedule["days"][] = array("weekday" => $i, "lessons" => array());
        }

        $lessonsList = $this->database->query($allLessonsQuery);
        while($lesson = $lessonsList->fetch_assoc())
        {
            $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["date"]);
            $lesson["correctDate"] = $lessonDate->format('d.m.Y');
            $lesson["dow"] = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];

            $startHour = intval(mb_substr($lesson["startTime"], 0, 2));
            $startMinute  = intval(mb_substr($lesson["startTime"], 3, 2));
            $endHour = $startHour + 1;
            $endMinute = $startMinute + 20;
            if ($endMinute >= 60)
            {
                $endHour = $endHour + 1;
                $endMinute = $endMinute - 60;
            }
            if ($startHour < 10) {$startHour = "0" . $startHour;}
            if ($startMinute < 10) {$startMinute = "0" . $startMinute;}
            if ($endHour < 10) {$endHour = "0" . $endHour;}
            if ($endMinute < 10) {$endMinute = "0" . $endMinute;}

            $building = null;
            if (Utilities::AuditoriumBuilding($lesson["auditoriumName"]) == "Other")
            {
                if ($lesson["auditoriumName"] == "Ауд. ШКОЛА")
                {
                    $building = "ул. Молодогвардейская, д.196, корп. 2";
                }
                if (strpos($lesson["auditoriumName"], 'СГУ') !== FALSE)
                {
                    $building = "ул. Академика Павлова, дом 1";
                }

                $OtherBuildings = array(
                    "Ауд. СамГТУ (ул. Куйбышева, 153; 4 этаж)",
                    "Ауд. СамГМУ (ул. Арцыбушевская, 171)",
                    "Ауд. СамГМУ (Гагарина, 18)",
                    "Ауд. СГАУ",
                    "Ауд. ул. Маяковского, 20"
                );

                if (in_array($lesson["auditoriumName"], $OtherBuildings)){
                    $building = $lesson["auditoriumName"];
                }

            }
            else
            {
                $building = $BuildingAddress[Utilities::AuditoriumBuilding($lesson["auditoriumName"])];
            }

            $newLesson = array(
                "subject" => $lesson["discName"],
                "type" => 2,
                "time_start" => $startHour . ":" . $startMinute,
                "time_end" => $endHour . ":" . $endMinute,
                "parity" => null,
                "date_start" => null,
                "date_end" => null,
                "dates" => array($lesson["correctDate"]),
                "teachers" => array(),
                "auditories" => array(
                    "auditory_name" => $lesson["auditoriumName"],
                    "auditory_address" => $building
                )
            );

            $newLesson["teachers"][] = array("teacher_name" => $lesson["teacherFIO"]);

            $groupSchedule["days"][$lesson["dow"]-1]["lessons"][] = $newLesson;

        }

        $result = json_encode($groupSchedule);

        return $result;
    }

    public function get_schedule2($group_id) {

        $BuildingAddress = array(
            "Mol" => "ул. Молодогвардейская, д.196, корп. 2",
            "Jar" => "ул. Ярмарочная, д.17, корп. 3"
        );

        $nameQuery  = "SELECT studentGroups.Name ";
        $nameQuery .= "FROM studentGroups ";
        $nameQuery .= "WHERE studentGroups.StudentGroupId = " . $group_id;

        $qResult = $this->database->query($nameQuery);
        $result = $qResult->fetch_assoc();
        $groupName = $result["Name"];

        $groupSchedule["group_name"] = $groupName;

        $groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT studentsInGroups.StudentId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "JOIN studentGroups ";
        $groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
        $groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $group_id ." ";
        $groupsQuery .= ")";

        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc())
        {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = "WHERE disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $allLessonsQuery  = "SELECT disciplines.Name as discName, rings.Time as startTime, ";
        $allLessonsQuery .= "calendars.Date as date, teachers.FIO as teacherFIO, auditoriums.Name as auditoriumName, ";
        $allLessonsQuery .= "teacherForDisciplines.TeacherForDisciplineId as tfdId ";
        $allLessonsQuery .= "FROM lessons ";
        $allLessonsQuery .= "JOIN teacherForDisciplines ";
        $allLessonsQuery .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
        $allLessonsQuery .= "JOIN teachers ";
        $allLessonsQuery .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
        $allLessonsQuery .= "JOIN disciplines ";
        $allLessonsQuery .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
        $allLessonsQuery .= "JOIN calendars ";
        $allLessonsQuery .= "ON lessons.CalendarId = calendars.calendarId ";
        $allLessonsQuery .= "JOIN auditoriums ";
        $allLessonsQuery .= "ON lessons.auditoriumId = auditoriums.AuditoriumId ";
        $allLessonsQuery .= "JOIN rings ";
        $allLessonsQuery .= "ON lessons.ringId = rings.ringId ";
        $allLessonsQuery .= $groupCondition;
        $allLessonsQuery .= "AND lessons.isActive = 1 ";

        $groupSchedule["days"] = array();
        for ($i = 1; $i <= 7; $i++) {
            $groupSchedule["days"][] = array("weekday" => $i, "lessons" => array());
        }

        $tfdArrayIndexes = array();
        for ($i = 1; $i <= 7; $i++) {
            $tfdArrayIndexes[$i] = array();
        }

        $lessonsList = $this->database->query($allLessonsQuery);
        while($lesson = $lessonsList->fetch_assoc())
        {
            $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["date"]);
            $lesson["correctDate"] = $lessonDate->format('d.m.Y');
            $lesson["dow"] = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];

            $startHour = intval(mb_substr($lesson["startTime"], 0, 2));
            $startMinute  = intval(mb_substr($lesson["startTime"], 3, 2));
            $endHour = $startHour + 1;
            $endMinute = $startMinute + 20;
            if ($endMinute >= 60)
            {
                $endHour = $endHour + 1;
                $endMinute = $endMinute - 60;
            }
            if ($startHour < 10) {$startHour = "0" . $startHour;}
            if ($startMinute < 10) {$startMinute = "0" . $startMinute;}
            if ($endHour < 10) {$endHour = "0" . $endHour;}
            if ($endMinute < 10) {$endMinute = "0" . $endMinute;}

            $building = null;
            if (Utilities::AuditoriumBuilding($lesson["auditoriumName"]) == "Other")
            {
                if ($lesson["auditoriumName"] == "Ауд. ШКОЛА")
                {
                    $building = "ул. Молодогвардейская, д.196, корп. 2";
                }
                if (strpos($lesson["auditoriumName"], 'СГУ') !== FALSE)
                {
                    $building = "ул. Академика Павлова, дом 1";
                }

                $OtherBuildings = array(
                    "Ауд. СамГТУ (ул. Куйбышева, 153; 4 этаж)",
                    "Ауд. СамГМУ (ул. Арцыбушевская, 171)",
                    "Ауд. СамГМУ (Гагарина, 18)",
                    "Ауд. СГАУ",
                    "Ауд. ул. Маяковского, 20"
                );

                if (in_array($lesson["auditoriumName"], $OtherBuildings)){
                    $building = $lesson["auditoriumName"];
                }

            }
            else
            {
                $building = $BuildingAddress[Utilities::AuditoriumBuilding($lesson["auditoriumName"])];
            }

            $newLesson = array(
                "subject" => $lesson["discName"],
                "type" => 2,
                "time_start" => $startHour . ":" . $startMinute,
                "time_end" => $endHour . ":" . $endMinute,
                "parity" => null,
                "date_start" => null,
                "date_end" => null,
                "dates" => array($lesson["correctDate"]),
                "teachers" => array(),
                "auditories" => array()
            );
            $newLesson["teachers"][] = array("teacher_name" => $lesson["teacherFIO"]);
            $newLesson["auditories"][] = array(
                "auditory_name" => $lesson["auditoriumName"],
                "auditory_address" => $building
            );

            if (!array_key_exists($newLesson["time_start"], $tfdArrayIndexes[$lesson["dow"]]))
            {
                $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]] = array();
            }
            if (!array_key_exists($lesson["tfdId"], $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]]))
            {
                $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]][$lesson["tfdId"]] =
                    count($groupSchedule["days"][$lesson["dow"]-1]["lessons"]);
                $groupSchedule["days"][$lesson["dow"]-1]["lessons"][] = $newLesson;
            }
            else
            {
                $index = $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]][$lesson["tfdId"]];
                $groupSchedule["days"][$lesson["dow"]-1]["lessons"][$index]["dates"][] = $lesson["correctDate"];
                $groupSchedule["days"][$lesson["dow"]-1]["lessons"][$index]["auditories"][] = array(
                    "auditory_name" => $lesson["auditoriumName"],
                    "auditory_address" => $building
                );
            }
        }

        /*
        echo "<pre>";
        echo print_r($tfdArrayIndexes);
        echo "</pre>";

        return $groupSchedule;
        */

        $result = json_encode($groupSchedule);

        return $result;
    }

    public function get_schedule22($group_id) {

        $BuildingAddress = array(
            "Mol" => "ул. Молодогвардейская, д.196, корп. 2",
            "Jar" => "ул. Ярмарочная, д.17, корп. 3"
        );

        $nameQuery  = "SELECT studentGroups.Name ";
        $nameQuery .= "FROM studentGroups ";
        $nameQuery .= "WHERE studentGroups.StudentGroupId = " . $group_id;

        $qResult = $this->database->query($nameQuery);
        $result = $qResult->fetch_assoc();
        $groupName = $result["Name"];

        $groupSchedule["group_name"] = $groupName;

        $groupsQuery  = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT studentsInGroups.StudentId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "JOIN studentGroups ";
        $groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
        $groupsQuery .= "WHERE studentGroups.StudentGroupId = ". $group_id ." ";
        $groupsQuery .= ")";

        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc())
        {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = "WHERE disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $allLessonsQuery  = "SELECT disciplines.Name as discName, rings.Time as startTime, ";
        $allLessonsQuery .= "calendars.Date as date, teachers.FIO as teacherFIO, auditoriums.Name as auditoriumName, ";
        $allLessonsQuery .= "teacherForDisciplines.TeacherForDisciplineId as tfdId ";
        $allLessonsQuery .= "FROM lessons ";
        $allLessonsQuery .= "JOIN teacherForDisciplines ";
        $allLessonsQuery .= "ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
        $allLessonsQuery .= "JOIN teachers ";
        $allLessonsQuery .= "ON teacherForDisciplines.TeacherId = teachers.TeacherId ";
        $allLessonsQuery .= "JOIN disciplines ";
        $allLessonsQuery .= "ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
        $allLessonsQuery .= "JOIN calendars ";
        $allLessonsQuery .= "ON lessons.CalendarId = calendars.calendarId ";
        $allLessonsQuery .= "JOIN auditoriums ";
        $allLessonsQuery .= "ON lessons.auditoriumId = auditoriums.AuditoriumId ";
        $allLessonsQuery .= "JOIN rings ";
        $allLessonsQuery .= "ON lessons.ringId = rings.ringId ";
        $allLessonsQuery .= $groupCondition;
        $allLessonsQuery .= "AND lessons.isActive = 1 ";

        $groupSchedule["days"] = array();
        for ($i = 1; $i <= 7; $i++) {
            $groupSchedule["days"][] = array("weekday" => $i, "lessons" => array());
        }

        $tfdArrayIndexes = array();
        for ($i = 1; $i <= 7; $i++) {
            $tfdArrayIndexes[$i] = array();
        }

        $lessonsList = $this->database->query($allLessonsQuery);
        while($lesson = $lessonsList->fetch_assoc())
        {
            $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["date"]);
            $lesson["correctDate"] = $lessonDate->format('d.m.Y');
            $lesson["dow"] = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];

            $startHour = intval(mb_substr($lesson["startTime"], 0, 2));
            $startMinute  = intval(mb_substr($lesson["startTime"], 3, 2));
            $endHour = $startHour + 1;
            $endMinute = $startMinute + 20;
            if ($endMinute >= 60)
            {
                $endHour = $endHour + 1;
                $endMinute = $endMinute - 60;
            }
            if ($startHour < 10) {$startHour = "0" . $startHour;}
            if ($startMinute < 10) {$startMinute = "0" . $startMinute;}
            if ($endHour < 10) {$endHour = "0" . $endHour;}
            if ($endMinute < 10) {$endMinute = "0" . $endMinute;}

            $building = null;
            if (Utilities::AuditoriumBuilding($lesson["auditoriumName"]) == "Other")
            {
                if ($lesson["auditoriumName"] == "Ауд. ШКОЛА")
                {
                    $building = "ул. Молодогвардейская, д.196, корп. 2";
                }
                if (strpos($lesson["auditoriumName"], 'СГУ') !== FALSE)
                {
                    $building = "ул. Академика Павлова, дом 1";
                }

                $OtherBuildings = array(
                    "Ауд. СамГТУ (ул. Куйбышева, 153; 4 этаж)",
                    "Ауд. СамГМУ (ул. Арцыбушевская, 171)",
                    "Ауд. СамГМУ (Гагарина, 18)",
                    "Ауд. СГАУ",
                    "Ауд. ул. Маяковского, 20"
                );

                if (in_array($lesson["auditoriumName"], $OtherBuildings)){
                    $building = $lesson["auditoriumName"];
                }

            }
            else
            {
                $building = $BuildingAddress[Utilities::AuditoriumBuilding($lesson["auditoriumName"])];
            }

            $newLesson = array(
                "subject" => $lesson["discName"],
                "type" => 2,
                "time_start" => $startHour . ":" . $startMinute,
                "time_end" => $endHour . ":" . $endMinute,
                "parity" => null,
                "date_start" => null,
                "date_end" => null,
                "dates" => array($lesson["correctDate"]),
                "teachers" => array(),
                "auditories" => array()
            );
            $newLesson["teachers"][] = array("teacher_name" => $lesson["teacherFIO"]);
            $newLesson["auditories"][] = array(
                "auditory_name" => $lesson["auditoriumName"],
                "auditory_address" => $building
            );

            if (!array_key_exists($newLesson["time_start"], $tfdArrayIndexes[$lesson["dow"]]))
            {
                $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]] = array();
            }
            if (!array_key_exists($lesson["tfdId"], $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]]))
            {
                $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]][$lesson["tfdId"]] =
                    count($groupSchedule["days"][$lesson["dow"]-1]["lessons"]);
                $groupSchedule["days"][$lesson["dow"]-1]["lessons"][] = $newLesson;
            }
            else
            {
                $index = $tfdArrayIndexes[$lesson["dow"]][$newLesson["time_start"]][$lesson["tfdId"]];
                $groupSchedule["days"][$lesson["dow"]-1]["lessons"][$index]["dates"][] = $lesson["correctDate"];
                $groupSchedule["days"][$lesson["dow"]-1]["lessons"][$index]["auditories"][] = array(
                    "auditory_name" => $lesson["auditoriumName"],
                    "auditory_address" => $building
                );
            }
        }


        echo "<pre>";
        echo print_r($groupSchedule);
        echo "</pre>";

        return "";
        /*

        $result = json_encode($groupSchedule);

        return $result;
        */
    }


}

$NewAPI = new RVAPI($database);

?>