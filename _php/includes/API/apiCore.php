<?php
include $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";

global $database;

class api {

    public $database;

    public function __construct($db){
        $this->database = $db;
    }

    public function ExecuteAction($POST){
        $action = $POST["action"];

        if (!(($action == "list") ||
              ($action == "groupsBundle") ||
              ($action == "bundle") ||
              ($action == "update")))
        {
            echo $this->APIError("Неизвестное действие (action)");
            exit;
        }

        $expelledIncluded = array();
        $expelledIncluded['ExpelledIncluded'] = 1;

        switch ($action) {
            case "update":
            case "bundle":
                $bundle = array();
                $bundle["auditoriums"] = $this->GetAuditoriumsList();
                $bundle["calendars"] = $this->GetCalendarsList();
                $bundle["disciplines"] = $this->GetDisciplinesList(null);
                $bundle["lessons"] = $this->GetLessonsList($POST);
                $bundle["rings"] = $this->GetRingsList();
                $bundle["students"] = $this->GetStudentsList($expelledIncluded);
                $bundle["studentGroups"] = $this->GetStudentGroupsList();
                $bundle["studentsInGroups"] = $this->GetStudentInGroupsList();
                $bundle["teachers"] = $this->GetTechersList();
                $bundle["teacherForDisciplines"] = $this->GetTFDList();

                $bundle["configOptions"] = $this->GetConfigOptionsList();
                $bundle["lessonLogEvents"] = $this->GetLogEvents(null);

                $result = json_encode($bundle);

                return ($result);
                break;
            case "groupsBundle":
                $bundle = array();
                $bundle["studentGroups"] = $this->GetStudentGroupsList();
                $bundle["faculties"] = $this->GetFacultiesList();
                $bundle["groupsInFaculties"] = $this->GetGroupsInFacultiesList();

                $result = json_encode($bundle);

                return ($result);
                break;
            case "list":
                if(!isset($POST['listtype']))
                {
                    echo $this->APIError("listtype - обязательный параметр при list запросе.");
                    exit;
                }
                else
                {
                    $listtype = $POST["listtype"];
                }

                switch ($listtype) {
                    case "auditoriums":
                        $auditoriums = $this->GetAuditoriumsList();
                        return (json_encode($auditoriums));
                        break;
                    case "calendars":
                        $calendars = $this->GetCalendarsList();
                        return (json_encode($calendars));
                        break;
                    case "configOptions":
                        $options = $this->GetConfigOptionsList();
                        return (json_encode($options));
                        break;
                    case "disciplines":
                        $disciplines = $this->GetDisciplinesList($POST);
                        return (json_encode($disciplines));
                        break;
                    case "lessons":
                        $lessons = $this->GetLessonsList($POST);
                        return (json_encode($lessons));
                        break;
                    case "rings":
                        $rings = $this->GetRingsList();
                        return (json_encode($rings));
                        break;
                    case "students":
                        $students = $this->GetStudentsList($POST);
                        return (json_encode($students));
                        break;
                    case "studentGroups":
                        $groupList = $this->GetStudentGroupsList();
                        return (json_encode($groupList));
                        break;
                    case "studentsInGroups":
                        $studentsInGroups = $this->GetStudentInGroupsList();
                        return (json_encode($studentsInGroups));
                        break;
                    case "teachers":
                        $teachers = $this->GetTechersList();
                        return (json_encode($teachers));
                        break;
                    case "teacherForDisciplines":
                        $tfds = $this->GetTFDList();
                        return (json_encode($tfds));
                        break;
                    case "lessonLogEvents":
                        $lessonLogEvents = $this->GetLogEvents($POST);
                        return (json_encode($lessonLogEvents));
                        break;
                    case "faculties":
                        $faculties = $this->GetFacultiesList();
                        return (json_encode($faculties));
                        break;
                }
                break;
        }
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetLogEvents($POST)
    {
        if (isset($POST['studentId'])) {
            $studentId = $POST['studentId'];
            $groupsQuery = "SELECT studentsInGroups.StudentGroupId ";
            $groupsQuery .= "FROM studentsInGroups ";
            $groupsQuery .= "WHERE StudentId = " . $studentId;

            $groupIdsResult = $this->database->query($groupsQuery);

            $groupIdsArray = array();
            while ($id = $groupIdsResult->fetch_assoc()) {
                $groupIdsArray[] = $id["StudentGroupId"];
            }
            $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

            $query = "SELECT lessonLogEvents.LessonLogEventId, ";
            $query .= "lessonLogEvents.OldLessonId,lessonLogEvents.NewLessonId, ";
            $query .= "lessonLogEvents.DateTime,lessonLogEvents.PublicComment ";
            $query .= "FROM lessonLogEvents ";

            $query .= "LEFT JOIN lessons AS newLesson ";
            $query .= "ON lessonLogEvents.NewLessonId = newLesson.LessonId ";
            $query .= "LEFT JOIN teacherForDisciplines AS newTFD ";
            $query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
            $query .= "LEFT JOIN disciplines AS newDics ";
            $query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";

            $query .= "LEFT JOIN lessons AS oldLesson ";
            $query .= "ON lessonLogEvents.OldLessonId = oldLesson.LessonId ";
            $query .= "LEFT JOIN teacherForDisciplines AS oldTFD ";
            $query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
            $query .= "LEFT JOIN disciplines AS oldDics ";
            $query .= "ON oldTFD.DisciplineId = oldDics.DisciplineId ";

            $query .= "WHERE  ";
            $query .= "newDics.StudentGroupId " . $groupCondition . " OR ";
            $query .= "oldDics.StudentGroupId " . $groupCondition;
        } else {
            if (isset($POST['groupId'])) {
                $groupId = $POST['groupId'];
                $groupsQuery = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT studentsInGroups.StudentId ";
                $groupsQuery .= "FROM studentsInGroups ";
                $groupsQuery .= "WHERE studentsInGroups.StudentGroupId = '" . $groupId . "' ";
                $groupsQuery .= ")";

                $groupIdsResult = $this->database->query($groupsQuery);

                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc()) {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }
                $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

                $query = "SELECT lessonLogEvents.LessonLogEventId, ";
                $query .= "lessonLogEvents.OldLessonId,lessonLogEvents.NewLessonId, ";
                $query .= "lessonLogEvents.DateTime,lessonLogEvents.PublicComment ";
                $query .= "FROM lessonLogEvents ";

                $query .= "LEFT JOIN lessons AS newLesson ";
                $query .= "ON lessonLogEvents.NewLessonId = newLesson.LessonId ";
                $query .= "LEFT JOIN teacherForDisciplines AS newTFD ";
                $query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
                $query .= "LEFT JOIN disciplines AS newDics ";
                $query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";

                $query .= "LEFT JOIN lessons AS oldLesson ";
                $query .= "ON lessonLogEvents.OldLessonId = oldLesson.LessonId ";
                $query .= "LEFT JOIN teacherForDisciplines AS oldTFD ";
                $query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
                $query .= "LEFT JOIN disciplines AS oldDics ";
                $query .= "ON oldTFD.DisciplineId = oldDics.DisciplineId ";

                $query .= "WHERE  ";
                $query .= "newDics.StudentGroupId " . $groupCondition . " OR ";
                $query .= "oldDics.StudentGroupId " . $groupCondition;

            } else {
                $lessonLogEvents = array();

                $query = "SELECT lessonLogEvents.LessonLogEventId, ";
                $query .= "lessonLogEvents.OldLessonId,lessonLogEvents.NewLessonId, ";
                $query .= "lessonLogEvents.DateTime,lessonLogEvents.PublicComment ";
                $query .= "FROM lessonLogEvents";
            }
        }

        $eventList = $this->database->query($query);

        while ($dbEvent = $eventList->fetch_assoc()) {
            $event = array();
            $event["LessonLogEventId"] = $dbEvent["LessonLogEventId"];
            $event["OldLessonId"] = $dbEvent["OldLessonId"];
            $event["NewLessonId"] = $dbEvent["NewLessonId"];
            $event["DateTime"] = $dbEvent["DateTime"];
            $event["Comment"] = $dbEvent["PublicComment"];

            $lessonLogEvents[] = $event;
        }
        return $lessonLogEvents;
    }

    /**
     * @return array
     */
    private function GetTFDList()
    {
        $tfds = array();

        $query = "SELECT teacherForDisciplines.TeacherForDisciplineId, ";
        $query .= "teacherForDisciplines.TeacherId, teacherForDisciplines.DisciplineId ";
        $query .= "FROM teacherForDisciplines";

        $tfdList = $this->database->query($query);

        while ($dbtfd = $tfdList->fetch_assoc()) {
            $tfd = array();
            $tfd["TeacherForDisciplineId"] = $dbtfd["TeacherForDisciplineId"];
            $tfd["TeacherId"] = $dbtfd["TeacherId"];
            $tfd["DisciplineId"] = $dbtfd["DisciplineId"];

            $tfds[] = $tfd;
        }
        return $tfds;
    }

    /**
     * @return array
     */
    private function GetTechersList()
    {
        $teachers = array();

        $query = "SELECT teachers.TeacherId, teachers.FIO ";
        $query .= "FROM teachers";

        $teacherList = $this->database->query($query);

        while ($dbTeacher = $teacherList->fetch_assoc()) {
            $teacher = array();
            $teacher["TeacherId"] = $dbTeacher["TeacherId"];
            $teacher["FIO"] = $dbTeacher["FIO"];

            $teachers[] = $teacher;
        }
        return $teachers;
    }

    /**
     * @return array
     */
    private function GetStudentInGroupsList()
    {
        $studentsInGroups = array();

        $query = "SELECT studentsInGroups.StudentsInGroupsId, ";
        $query .= "studentsInGroups.StudentId, studentsInGroups.StudentGroupId ";
        $query .= "FROM studentsInGroups";

        $sigList = $this->database->query($query);

        while ($dbSIG = $sigList->fetch_assoc()) {
            $sig = array();
            $sig["StudentsInGroupsId"] = $dbSIG["StudentsInGroupsId"];
            $sig["StudentId"] = $dbSIG["StudentId"];
            $sig["StudentGroupId"] = $dbSIG["StudentGroupId"];

            $studentsInGroups[] = $sig;
        }
        return $studentsInGroups;
    }

    /**
     * @return array
     */
    private function GetStudentGroupsList()
    {
        $studentGroups = array();

        $query = "SELECT studentGroups.StudentGroupId, studentGroups.Name ";
        $query .= "FROM studentGroups";

        $groupList = $this->database->query($query);

        while ($dbGroup = $groupList->fetch_assoc()) {
            $group = array();
            $group["StudentGroupId"] = $dbGroup["StudentGroupId"];
            $group["Name"] = $dbGroup["Name"];

            $studentGroups[] = $group;
        }

        return $studentGroups;
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetStudentsList($POST)
    {
        $students = array();

        $query = "SELECT students.StudentId, ";
        $query .= "students.F, students.I, students.O, ";
        $query .= "students.Starosta, students.NFactor, students.Expelled ";
        $query .= "FROM students ";
        if (isset($POST['groupId'])) {
            $query = "SELECT students.StudentId, ";
            $query .= "students.F, students.I, students.O, ";
            $query .= "students.Starosta, students.NFactor, students.Expelled ";
            $query .= "FROM studentsInGroups ";
            $query .= "JOIN students ";
            $query .= "ON studentsInGroups.StudentId = students.StudentId ";
            $query .= "WHERE studentsInGroups.StudentGroupId = " . $POST['groupId'] . " ";
        }
        if (!((isset($POST['ExpelledIncluded'])) && ($POST['ExpelledIncluded'] == 1))) {
            if (isset($POST['groupId'])) {
                $query .= "AND ";
            } else {
                $query .= "WHERE  ";
            }
            $query .= " students.Expelled = 0";
        }

        $studentsList = $this->database->query($query);

        while ($dbStudent = $studentsList->fetch_assoc()) {
            $student = array();
            $student["StudentId"] = $dbStudent["StudentId"];
            $student["F"] = $dbStudent["F"];
            $student["I"] = $dbStudent["I"];
            $student["O"] = $dbStudent["O"];
            $student["Starosta"] = $dbStudent["Starosta"];
            $student["NFactor"] = $dbStudent["NFactor"];
            $student["Expelled"] = $dbStudent["Expelled"];

            $students[] = $student;
        }
        return $students;
    }

    /**
     * @return array
     */
    private function GetRingsList()
    {
        $rings = array();

        $query = "SELECT rings.RingId, rings.Time ";
        $query .= "FROM rings";

        $ringList = $this->database->query($query);

        while ($dbRing = $ringList->fetch_assoc()) {
            $ring = array();
            $ring["RingId"] = $dbRing["RingId"];
            $ring["Time"] = $dbRing["Time"];

            $rings[] = $ring;
        }
        return $rings;
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetLessonsList($POST)
    {
        $lessons = array();

        if (isset($POST['studentId'])) {
            $studentId = $POST['studentId'];
            $groupsQuery = "SELECT studentsInGroups.StudentGroupId ";
            $groupsQuery .= "FROM studentsInGroups ";
            $groupsQuery .= "WHERE StudentId = " . $studentId;

            $groupIdsResult = $this->database->query($groupsQuery);

            $groupIdsArray = array();
            while ($id = $groupIdsResult->fetch_assoc()) {
                $groupIdsArray[] = $id["StudentGroupId"];
            }
            $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

            $query = "SELECT lessons.LessonId, lessons.IsActive, ";
            $query .= "lessons.TeacherForDisciplineId, lessons.CalendarId, ";
            $query .= "lessons.RingId, lessons.AuditoriumId ";
            $query .= "FROM lessons ";
            $query .= "JOIN teacherForDisciplines ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
            $query .= "JOIN disciplines ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
            $query .= "WHERE disciplines.StudentGroupId " . $groupCondition;
        } else {
            if (isset($POST['groupId'])) {
                $query = "SELECT lessons.LessonId, lessons.IsActive, ";
                $query .= "lessons.TeacherForDisciplineId, lessons.CalendarId, ";
                $query .= "lessons.RingId, lessons.AuditoriumId ";
                $query .= "FROM lessons ";
                $query .= "JOIN teacherForDisciplines ON lessons.TeacherForDisciplineId = teacherForDisciplines.TeacherForDisciplineId ";
                $query .= "JOIN disciplines ON teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
                if (isset($POST['LimitToExactGroup']) && ($POST['LimitToExactGroup'] == 1)) {
                    $query .= "WHERE disciplines.StudentGroupId = " . $POST['groupId'];
                } else {
                    $groupsQuery = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
                    $groupsQuery .= "FROM studentsInGroups ";
                    $groupsQuery .= "WHERE StudentId ";
                    $groupsQuery .= "IN ( ";
                    $groupsQuery .= "SELECT studentsInGroups.StudentId ";
                    $groupsQuery .= "FROM studentsInGroups ";
                    $groupsQuery .= "JOIN studentGroups ";
                    $groupsQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
                    $groupsQuery .= "WHERE studentGroups.StudentGroupId = '" . $POST['groupId'] . "' ";
                    $groupsQuery .= ")";

                    $groupIdsResult = $this->database->query($groupsQuery);

                    $groupIdsArray = array();
                    while ($id = $groupIdsResult->fetch_assoc()) {
                        $groupIdsArray[] = $id["StudentGroupId"];
                    }
                    $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

                    $query .= "WHERE disciplines.StudentGroupId " . $groupCondition;
                }

            } else {
                $query = "SELECT lessons.LessonId, lessons.IsActive, ";
                $query .= "lessons.TeacherForDisciplineId, lessons.CalendarId, ";
                $query .= "lessons.RingId, lessons.AuditoriumId ";
                $query .= "FROM lessons";
            }
        }

        if (isset($POST['updateLessonsAfterId'])) {

        }


        $lessonsList = $this->database->query($query);

        while ($les = $lessonsList->fetch_assoc()) {
            $l = array();
            $l["LessonId"] = $les["LessonId"];
            $l["IsActive"] = $les["IsActive"];
            $l["TeacherForDisciplineId"] = $les["TeacherForDisciplineId"];
            $l["CalendarId"] = $les["CalendarId"];
            $l["RingId"] = $les["RingId"];
            $l["AuditoriumId"] = $les["AuditoriumId"];

            $lessons[] = $l;
        }
        return $lessons;
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetDisciplinesList($POST)
    {
        $disciplines = array();

        $query = "SELECT disciplines.DisciplineId, disciplines.Name, disciplines.Attestation,  ";
        $query .= "disciplines.AuditoriumHours, disciplines.LectureHours, ";
        $query .= "disciplines.PracticalHours, disciplines.StudentGroupId ";
        $query .= "FROM disciplines ";

        if (isset($POST['groupId'])) {
            if (isset($POST['LimitToExactGroup']) && ($POST['LimitToExactGroup'] == 1)) {
                $query .= "WHERE disciplines.StudentGroupId = " . $POST['groupId'];
            } else {
                $groupsQuery = "SELECT DISTINCT studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT studentsInGroups.StudentId ";
                $groupsQuery .= "FROM studentsInGroups ";
                $groupsQuery .= "WHERE studentsInGroups.StudentGroupId = " . $POST['groupId'] . " ";
                $groupsQuery .= ")";

                $groupIdsResult = $this->database->query($groupsQuery);
                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc()) {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }

                $query .= "WHERE disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";
            }
        }

        $disciplineList = $this->database->query($query);

        while ($discipline = $disciplineList->fetch_assoc()) {
            $d = array();
            $d["DisciplineId"] = $discipline["DisciplineId"];
            $d["Name"] = $discipline["Name"];
            $d["Attestation"] = $discipline["Attestation"];
            $d["AuditoriumHours"] = $discipline["AuditoriumHours"];
            $d["LectureHours"] = $discipline["LectureHours"];
            $d["PracticalHours"] = $discipline["PracticalHours"];
            $d["StudentGroupId"] = $discipline["StudentGroupId"];

            $disciplines[] = $d;
        }
        return $disciplines;
    }

    /**
     * @return array
     */
    private function GetConfigOptionsList()
    {
        $options = array();

        $query = "SELECT configs.ConfigOptionId , configs.Key , configs.Value ";
        $query .= "FROM configs";

        $optionsList = $this->database->query($query);

        while ($option = $optionsList->fetch_assoc()) {
            $o = array();
            $o["ConfigOptionId"] = $option["ConfigOptionId"];
            $o["Key"] = $option["Key"];
            $o["Value"] = $option["Value"];

            $options[] = $o;
        }
        return $options;
    }

    /**
     * @return array
     */
    private function GetCalendarsList()
    {
        $calendars = array();

        $query = "SELECT calendars.CalendarId, calendars.Date ";
        $query .= "FROM calendars";

        $calendarList = $this->database->query($query);

        while ($calendar = $calendarList->fetch_assoc()) {
            $c = array();
            $c["CalendarId"] = $calendar["CalendarId"];
            $c["Date"] = $calendar["Date"];

            $calendars[] = $c;
        }
        return $calendars;
    }

    /**
     * @return array
     */
    private function GetAuditoriumsList()
    {
        $auditoriums = array();

        $query = "SELECT auditoriums.AuditoriumId, auditoriums.Name ";
        $query .= "FROM auditoriums";

        $audList = $this->database->query($query);

        while ($auditorium = $audList->fetch_assoc()) {
            $aud = array();
            $aud["AuditoriumId"] = $auditorium["AuditoriumId"];
            $aud["Name"] = $auditorium["Name"];

            $auditoriums[] = $aud;
        }
        return $auditoriums;
    }

    public function APIError($errorMessage)
    {
        $result = array();
        $result["error"] = $errorMessage;
        return (json_encode($result));
    }

    public function PlainError($errorText){
        $output  = $errorText . "<br/>";
        $output .= "Документация ";
        $output .= "<a href=\"developers\">здесь</a>.";
        return $output;
    }

    public function WelcomeMessage(){
        $output  = "Добро пожаловать на страницу API<br/>";
        $output .= "Документация находится  ";
        $output .= "<a href=\"developers\">здесь</a>.";
        return $output;
    }

    private function GetFacultiesList()
    {
        $faculties = array();

        $query = "SELECT FacultyId, Name, Letter, SortingOrder ";
        $query .= "FROM faculties ";

        $facultyList = $this->database->query($query);

        while ($faculty = $facultyList->fetch_assoc()) {

            $faculties[] = $faculty;
        }
        return $faculties;
    }

    private function GetGroupsInFacultiesList()
    {
        $GroupsInFaculty = array();

        $query = "SELECT GroupsInFacultyId, StudentGroupId, FacultyId ";
        $query .= "FROM GroupsInFaculties ";

        $gifList = $this->database->query($query);

        while ($gif = $gifList->fetch_assoc()) {

            $GroupsInFaculty[] = $gif;
        }
        return $GroupsInFaculty;
    }
}

$api = new api($database);

?>