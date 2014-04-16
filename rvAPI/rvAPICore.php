<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/ConfigOptions.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Utilities.php");

global $database;
global $options;

class RVAPI {

    public $database;
    public $options;

    public function __construct($db, $opt){
        $this->database = $db;
        $this->options = $opt;
    }

    public function get_faculties() {
        $semesterStarts = $this->options["Semester Starts"];
        $semesterStartsCorrectFormat = $this->ReformatDate($semesterStarts);

        $semesterEnds = $this->options["Semester Ends"];
        $semesterEndsCorrectFormat = $this->ReformatDate($semesterEnds);

        $faculties["faculties"] = array();

        $facultiesQuery  = "SELECT Name, SortingOrder ";
        $facultiesQuery .= "FROM faculties ";
        $facultiesQuery .= "ORDER BY SortingOrder";

        $facultiesList = $this->database->query($facultiesQuery);
        while($faculty = $facultiesList->fetch_assoc())
        {
            $faculties["faculties"][] = array (
                "faculty_name" => $faculty["Name"],
                "faculty_id" => $faculty["SortingOrder"],
                "date_start" => $semesterStartsCorrectFormat,
                "date_end" => $semesterEndsCorrectFormat
            );
        }

        $result = json_encode($faculties);

        return $result;
    }

    public function get_groups($faculty_id) {
        /*
        $gifQuery  = "SELECT Name, studentGroups.studentGroupId ";
        $gifQuery .= "FROM GroupsInFaculties ";
        $gifQuery .= "JOIN studentGroups ";
        $gifQuery .= "ON GroupsInFaculties.StudentGroupId = studentGroups.StudentGroupId ";
        $gifQuery .= "WHERE facultyId = " . $faculty_id;

        $groups["groups"] = array();

        $studentGroupsQueryList = $this->database->query($gifQuery);
        while($studentGroup = $studentGroupsQueryList->fetch_assoc())
        {
            $groups["groups"][] = array (
                "group_name" => $studentGroup["Name"],
                "group_id" => $studentGroup["studentGroupId"]
            );
        }*/

        $FacultyGroupsList = array(
            "1" => array("12 А", "13 А"),
            "2" => array("12 Б", "13 Б", "14 Б"),
            "3" => array("12 В0", "12 В", "13 В", "14 В"),
            "4" => array("12 Г", "12 Г(Н)", "13 Г", "13 Г(Н)", "14 Г"),
            "5" => array("12 Д", "12 Д(Н)", "13 Д", "13 Д(Н)", "14 Д"),
            "6" => array("12 Е", "13 Е", "14 Е"),
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
        
        $groupSchedule = $this->get_schedule_raw($group_id);

        $result = json_encode($groupSchedule);

        return $result;
    }

    /**
     * @param $semesterStarts
     * @return string
     */
    public function ReformatDate($semesterStarts)
    {
        $semesterStartsCorrectFormat =
            mb_substr($semesterStarts, 8, 2) . "." .
            mb_substr($semesterStarts, 5, 2) . "." .
            mb_substr($semesterStarts, 0, 4);
        return $semesterStartsCorrectFormat;
    }

    /**
     * @param $result
     */
    public function EchoResultPlusExit($result)
    {
        echo "<pre>";
        echo print_r($result);
        echo "</pre>";
        exit;
    }
/**
     * @param $result
     */
    public function EchoResult($result)
    {
        echo "<pre>";
        echo print_r($result);
        echo "</pre>";
    }

    public function get_schedule_raw($group_id)
    {
        $BuildingAddress = array(
            "Mol" => "ул. Молодогвардейская, д.196, корп. 2",
            "Jar" => "ул. Ярмарочная, д.17, корп. 3"
        );

        $nameQuery = "SELECT studentGroups.Name ";
        $nameQuery .= "FROM studentGroups ";
        $nameQuery .= "WHERE studentGroups.StudentGroupId = " . $group_id;

        $qResult = $this->database->query($nameQuery);
        $result = $qResult->fetch_assoc();
        $groupName = $result["Name"];

        $groupSchedule["group_name"] = $groupName;

        $groupsQuery = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT studentsInGroups.StudentId ";
        $groupsQuery .= "FROM studentsInGroups ";
        $groupsQuery .= "JOIN studentGroups ";
        $groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
        $groupsQuery .= "WHERE studentGroups.StudentGroupId = " . $group_id . " ";
        $groupsQuery .= ")";

        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc()) {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = "WHERE disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $allLessonsQuery = "SELECT disciplines.Name as discName, rings.Time as startTime, ";
        $allLessonsQuery .= "calendars.Date as date, teachers.FIO as teacherFIO, ";
        $allLessonsQuery .= "auditoriums.Name as auditoriumName, ";
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

        $tfdAudArray = array();
        for ($i = 1; $i <= 7; $i++) {
            $tfdAudArray[$i] = array();
        }

        $lessonsList = $this->database->query($allLessonsQuery);
        while ($lesson = $lessonsList->fetch_assoc()) {

            $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["date"]);

            $lesson["correctDate"] = $this->ReformatDate($lesson["date"]);
            $lesson["dow"] = Utilities::$DOWEnToRu[date("w", $lessonDate->getTimestamp())];

            $startHour = intval(mb_substr($lesson["startTime"], 0, 2));
            $startMinute = intval(mb_substr($lesson["startTime"], 3, 2));
            $endHour = $startHour + 1;
            $endMinute = $startMinute + 20;
            if ($endMinute >= 60) {
                $endHour = $endHour + 1;
                $endMinute = $endMinute - 60;
            }
            if ($startHour < 10) {
                $startHour = "0" . $startHour;
            }
            if ($startMinute < 10) {
                $startMinute = "0" . $startMinute;
            }
            if ($endHour < 10) {
                $endHour = "0" . $endHour;
            }
            if ($endMinute < 10) {
                $endMinute = "0" . $endMinute;
            }

            $building = null;
            if (Utilities::AuditoriumBuilding($lesson["auditoriumName"]) == "Other") {
                if ($lesson["auditoriumName"] == "Ауд. ШКОЛА") {
                    $building = "ул. Молодогвардейская, д.196, корп. 2";
                }
                if (strpos($lesson["auditoriumName"], 'СГУ') !== FALSE) {
                    $building = "ул. Академика Павлова, дом 1";
                }

                $OtherBuildings = array(
                    "Ауд. СамГТУ (ул. Куйбышева, 153; 4 этаж)",
                    "Ауд. СамГМУ (ул. Арцыбушевская, 171)",
                    "Ауд. СамГМУ (Гагарина, 18)",
                    "Ауд. СГАУ",
                    "Ауд. ул. Маяковского, 20"
                );

                if (in_array($lesson["auditoriumName"], $OtherBuildings)) {
                    $building = $lesson["auditoriumName"];
                }

            } else {
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


            $tfdAudKey = $lesson["tfdId"] . "+" . $lesson["auditoriumName"];
            if (!array_key_exists($newLesson["time_start"], $tfdAudArray[$lesson["dow"]])) {
                $tfdAudArray[$lesson["dow"]][$newLesson["time_start"]] = array();
            }
            if (!array_key_exists($tfdAudKey, $tfdAudArray[$lesson["dow"]][$newLesson["time_start"]])) {
                $tfdAudArray[$lesson["dow"]][$newLesson["time_start"]][$tfdAudKey] =
                    count($groupSchedule["days"][$lesson["dow"] - 1]["lessons"]);
                $groupSchedule["days"][$lesson["dow"] - 1]["lessons"][] = $newLesson;
            } else {
                $index = $tfdAudArray[$lesson["dow"]][$newLesson["time_start"]][$tfdAudKey];
                $groupSchedule["days"][$lesson["dow"] - 1]["lessons"][$index]["dates"][] = $lesson["correctDate"];
            }
        }

        return $groupSchedule;
    }
}

$NewAPI = new RVAPI($database, $options);

?>