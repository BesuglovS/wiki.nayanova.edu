<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/ConfigOptions.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Utilities.php");

global $database;
global $options;

class RVAPI {

    public $schoolDBPrefix = "s_";

    public $database;
    public $options, $schoolOptions;

    public function __construct($db, $opt){
        $this->database = $db;
        $this->options = $opt;

        $this->schoolOptions = $this->getSchoolOptions();
    }

    public function getSchoolOptions() {
        $configsTableName = $this->schoolDBPrefix . "configs";

        $query  = "SELECT ". $configsTableName . ".Key, " . $configsTableName . ".Value FROM " . $configsTableName;

        $result = $this->database->query($query);

        $s_options = array();
        while ($option = $result->fetch_assoc())
        {
            $s_options[$option["Key"]] = $option["Value"];
        }

        return $s_options;
    }

    /**
     * @param $faculties
     * @return mixed
     */
    public function GetFacultiesFromDB()
    {
        $semesterStarts = $this->options["Semester Starts"];
        $semesterStartsCorrectFormat = $this->ReformatDate($semesterStarts);

        $semesterEnds = $this->options["Semester Ends"];
        $semesterEndsCorrectFormat = $this->ReformatDate($semesterEnds);

        $faculties["faculties"] = array();

        $facultiesQuery = "SELECT Name, SortingOrder ";
        $facultiesQuery .= "FROM faculties ";
        $facultiesQuery .= "ORDER BY SortingOrder";

        $facultiesList = $this->database->query($facultiesQuery);
        while ($faculty = $facultiesList->fetch_assoc()) {
            if ((strpos($faculty["Name"], "Аспирантура") === FALSE) &&
                (strpos($faculty["Name"], "Магистратура") === FALSE) &&
                (strpos($faculty["Name"], "магистратура") === FALSE))
            {
                $faculties["faculties"][] = array(
                    "faculty_name" => $faculty["Name"],
                    "faculty_id" => $faculty["SortingOrder"],
                    "date_start" => $semesterStartsCorrectFormat,
                    "date_end" => $semesterEndsCorrectFormat
                );
            }
        }
        return $faculties;
    }

    /**
     * @param $faculties
     * @return mixed
     */
    public function GetSchoolFacultiesFromDB()
    {
        $semesterStarts = $this->schoolOptions["Semester Starts"];
        $semesterStartsCorrectFormat = $this->ReformatDate($semesterStarts);

        $semesterEnds = $this->schoolOptions["Semester Ends"];
        $semesterEndsCorrectFormat = $this->ReformatDate($semesterEnds);

        $faculties = array();

        $facultiesQuery = "SELECT Name, FacultyId ";
        $facultiesQuery .= "FROM " . $this->schoolDBPrefix . "faculties ";
        $facultiesQuery .= "ORDER BY SortingOrder";

        $facultiesList = $this->database->query($facultiesQuery);
        while ($faculty = $facultiesList->fetch_assoc()) {
            $id = $this->schoolDBPrefix . $faculty["FacultyId"];
            $faculties[] = array(
                "faculty_name" => $faculty["Name"],
                "faculty_id" => $id,
                "date_start" => $semesterStartsCorrectFormat,
                "date_end" => $semesterEndsCorrectFormat
            );
        }
        return $faculties;
    }

    public function get_faculties() {
        $faculties = $this->GetFacultiesFromDB();

        $schoolFaculties = $this->GetSchoolFacultiesFromDB();
        foreach($schoolFaculties as $faculty)
        {
            $faculties["faculties"][] = $faculty;
        }

        $result = json_encode($faculties);

        return $result;
    }

    function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
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

        $prefix = "";
        if ($this->startsWith($faculty_id, 's_'))
        {
            $prefix = $this->schoolDBPrefix;
        }

        $FacultyGroupsList = array(
            "1" => array("12 А", "13 А", "14 А", "2 АА", "3 АА"),
            "2" => array("12 Б", "13 Б", "14 Б", "15 Б", "16 Б", "2 АБ"),
            "3" => array("12 В", "13 В", "14 В", "15 В", "2 АВ"),
            "4" => array("12 Г", "12 Г(Н)", "13 Г", "13 Г(Н)", "14 Г", "14 Г(Н)", "15 Г", "16 Г", "1 АГ", "2 АГ"),
            "5" => array("12 Д", "13 Д", "13 Д(Н)", "14 Д", "15 Д", "16 Д", "1 АД", "2 АД", "3 АД"),
            "6" => array("12 Е", "13 Е", "14 Е", "15 Е"),
            "7" => array("12 У", "13 У", "14 У", "15 У"),
            "8" => array("12 Т", "13 Т", "14 Т", "15 Т"),
            "s_15" => array("8Б", "8В", "8Г"),
            "s_16" => array("9А1", "9А2", "9Б", "9В", "9Г"),
            "s_17" => array("10А", "10Б", "10В", "10Г"),
            "s_18" => array("11А", "11Б", "11В", "11Г", "8А"),

            "s_19" => array("1А", "1Б", "1В", "1Г"),
            "s_20" => array("2А", "2Б", "2В", "2Г"),
            "s_21" => array("3А", "3Б", "3В", "3Г"),
            "s_22" => array("4А", "4Б", "4В", "4Г", "4Д"),
            "s_23" => array("5А", "5Б", "5В", "5Г", "5Д"),
            "s_24" => array("6А", "6Б", "6В", "6Г"),
            "s_25" => array("7А", "7Б", "7В")
        );
        $FacultyGroups = $FacultyGroupsList[$faculty_id];

        $groups["groups"] = array();
        foreach ($FacultyGroups as $GroupName) {
            $idQuery  = "SELECT " . $prefix . "studentGroups.StudentGroupId ";
            $idQuery .= "FROM " . $prefix . "studentGroups ";
            $idQuery .= "WHERE " . $prefix . "studentGroups.Name = \"" . $GroupName . "\"";

            $qResult = $this->database->query($idQuery);
            $result = $qResult->fetch_assoc();
            $groupId = $result["StudentGroupId"];

            $groups["groups"][] = array (
                "group_name" => $GroupName,
                "group_id" => $prefix . $groupId
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
        $prefix = "";
        if ($this->startsWith($group_id, 's_'))
        {
            $prefix = $this->schoolDBPrefix;
            $group_id = substr($group_id, 2);
        }

        /*
        $BuildingAddress = array(
            "Mol" => "ул. Молодогвардейская, д.196, корп. 2",
            "Jar" => "ул. Ярмарочная, д.17, корп. 3"
        );
        */

        $nameQuery = "SELECT " . $prefix . "studentGroups.Name ";
        $nameQuery .= "FROM " . $prefix . "studentGroups ";
        $nameQuery .= "WHERE " . $prefix . "studentGroups.StudentGroupId = " . $group_id . " ";

        $qResult = $this->database->query($nameQuery);
        $result = $qResult->fetch_assoc();
        $groupName = $result["Name"];

        $groupSchedule["group_name"] = $groupName;

        $groupsQuery = "SELECT DISTINCT " . $prefix . "studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM " . $prefix . "studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT " . $prefix . "studentsInGroups.StudentId ";
        $groupsQuery .= "FROM " . $prefix . "studentsInGroups ";
        $groupsQuery .= "JOIN " . $prefix . "studentGroups ";
        $groupsQuery .= "ON " . $prefix . "studentsInGroups.StudentGroupId = " . $prefix . "studentGroups.StudentGroupId ";
        $groupsQuery .= "WHERE " . $prefix . "studentGroups.StudentGroupId = " . $group_id . " ";
        $groupsQuery .= ")";

        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc()) {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = "WHERE " . $prefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $allLessonsQuery = "SELECT " . $prefix . "disciplines.Name as discName, " . $prefix . "rings.Time as startTime, ";
        $allLessonsQuery .= $prefix . "calendars.Date as date, " . $prefix . "teachers.FIO as teacherFIO, ";
        $allLessonsQuery .= $prefix . "auditoriums.Name as auditoriumName, ";
        $allLessonsQuery .= $prefix . "teacherForDisciplines.TeacherForDisciplineId as tfdId, ";
        $allLessonsQuery .= $prefix . "buildings.Name AS buildingName ";
        $allLessonsQuery .= "FROM " . $prefix . "lessons ";
        $allLessonsQuery .= "JOIN " . $prefix . "teacherForDisciplines ";
        $allLessonsQuery .= "ON " . $prefix . "lessons.TeacherForDisciplineId = " . $prefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $allLessonsQuery .= "JOIN " . $prefix . "teachers ";
        $allLessonsQuery .= "ON " . $prefix . "teacherForDisciplines.TeacherId = " . $prefix . "teachers.TeacherId ";
        $allLessonsQuery .= "JOIN " . $prefix . "disciplines ";
        $allLessonsQuery .= "ON " . $prefix . "teacherForDisciplines.DisciplineId = " . $prefix . "disciplines.DisciplineId ";
        $allLessonsQuery .= "JOIN " . $prefix . "calendars ";
        $allLessonsQuery .= "ON " . $prefix . "lessons.CalendarId = " . $prefix . "calendars.calendarId ";
        $allLessonsQuery .= "JOIN " . $prefix . "auditoriums ";
        $allLessonsQuery .= "ON " . $prefix . "lessons.auditoriumId = " . $prefix . "auditoriums.AuditoriumId ";
        $allLessonsQuery .= "JOIN " . $prefix . "buildings ";
        $allLessonsQuery .= "ON " . $prefix . "auditoriums.BuildingId = " . $prefix . "buildings.BuildingId ";
        $allLessonsQuery .= "JOIN " . $prefix . "rings ";
        $allLessonsQuery .= "ON " . $prefix . "lessons.ringId = " . $prefix . "rings.ringId ";
        $allLessonsQuery .= $groupCondition;
        $allLessonsQuery .= "AND " . $prefix . "lessons.isActive = 1 ";

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
            $building = $lesson["buildingName"];
            /*
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
            */

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