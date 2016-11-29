<?php
include $_SERVER["DOCUMENT_ROOT"] . "/php/Database.php";
include $_SERVER["DOCUMENT_ROOT"] . "/php/Utilities.php";
include $_SERVER["DOCUMENT_ROOT"] . "/php/ConfigOptions.php";

global $database;

class api {

    public $database;
    public $dbPrefix;

    public function __construct($db, $dbPrefix){
        $this->database = $db;
        $this->dbPrefix = $dbPrefix;
    }

    public function ExecuteAction($POST){


        $action = $POST["action"];


        if (!(($action == "list") ||
              ($action == "groupsBundle") ||
              ($action == "bundle") ||
              ($action == "update") ||
              ($action == "dailySchedule") ||
              ($action == "groupExams") ||
              ($action == "weekSchedule") ||
              ($action == "groupSchedule") ||
              ($action == "TeacherWeekSchedule") ||
              ($action == "TeacherSchedule")
        ))
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

                $bundle["faculties"] = $this->GetFacultiesList();
                $bundle["groupsInFaculties"] = $this->GetGroupsInFacultiesList();

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
                    case "mainStudentGroups":
                        $groupList = $this->GetMainStudentGroupsList();
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

                    case "groupDisciplines":
                        $disciplines = $this->GetGroupDisciplinesList($POST);
                        return (json_encode($disciplines));
                        break;
                    case "teacherDisciplines":
                        $disciplines = $this->GetTeacherDisciplinesList($POST);
                        return (json_encode($disciplines));
                        break;
                        
					case "buildings":
                        $buildings = $this->GetBuildingsList();
                        return (json_encode($buildings));                        
                        break;
                }
                break;
            case "dailySchedule":
                $dailySchedule = $this->GetDailySchedule($POST);
                return (json_encode($dailySchedule));
                break;
            case "weekSchedule":
                $weekSchedule = $this->GetWeekSchedule($POST);
                return (json_encode($weekSchedule));
                break;
            case "groupSchedule":
                $groupSchedule = $this->GetGroupSchedule($POST);
                return (json_encode($groupSchedule));
                break;
            case "groupExams":
                $exams = $this->GetGroupExams($POST);
                return (json_encode($exams));
                break;
            case "TeacherWeekSchedule":
                $teacherWeekSchedule = $this->GetTeacherWeekSchedule($POST);
                return (json_encode($teacherWeekSchedule));
                break;
            case "TeacherSchedule":
                $teacherSchedule = $this->GetTeacherSchedule($POST);
                return (json_encode($teacherSchedule));
                break;
        }
    }

    private function GetGroupExams($POST)
    {
        $dbPrefix = "";
        if (isset($POST['dbPrefix'])) {
            $dbPrefix = $POST["dbPrefix"];
        }
        $schedulePrefix = "";
        if (isset($POST['schedulePrefix'])) {
            $schedulePrefix = $POST["schedulePrefix"];
        }

        $groupIds = "";
        $facultyId = -1;
        if (isset($POST['facultyId']))
        {
            $facultyId = $POST['facultyId'];
            $fquery  = "SELECT " .  $schedulePrefix . "studentGroups.StudentGroupId AS studentGroupId ";
            $fquery .= "FROM " . $schedulePrefix . "GroupsInFaculties ";
            $fquery .= "JOIN " . $schedulePrefix . "faculties ";
            $fquery .= "ON " . $schedulePrefix . "GroupsInFaculties.FacultyId = " . $schedulePrefix . "faculties.FacultyId ";
            $fquery .= "JOIN " . $schedulePrefix . "studentGroups ";
            $fquery .= "ON " . $schedulePrefix . "GroupsInFaculties.StudentGroupId = " . $schedulePrefix . "studentGroups.StudentGroupId ";
            $fquery .= "WHERE " . $schedulePrefix . "faculties.FacultyId = " . $facultyId;

            $queryResult = $this->database->query($fquery);

            $groups = array();
            while ($group = $queryResult->fetch_assoc()) {
                $groups[] = $group["studentGroupId"];
            }
        }
        else {
            if (isset($POST['groupId'])) {
                $groupIds = $POST["groupId"];
                $groups = explode("@", $groupIds);
            } else {
                return "";
            }
        }

        $groupNamesQuery  = "SELECT " .  $schedulePrefix . "studentGroups.StudentGroupId, " .  $schedulePrefix . "studentGroups.Name ";
        $groupNamesQuery .= "FROM " .  $schedulePrefix . "studentGroups ";
        $groupNamesQuery .= "WHERE " .  $schedulePrefix . "studentGroups.StudentGroupId ";
        $groupNamesQuery .= "IN ( " . implode(" , ", $groups) . " )";

        $groupNamesQueryResult = $this->database->query($groupNamesQuery);
        $groupNames = array();
        while ($gr = $groupNamesQueryResult->fetch_assoc())
        {
            $groupNames[$gr['StudentGroupId']] = $gr['Name'];
        }


        $result = array();

        for ($j = 0; $j < count($groups); $j++) {

            $groupId = $groups[$j];

            $groupsQuery = "SELECT DISTINCT " . $schedulePrefix . "studentsInGroups.StudentGroupId ";
            $groupsQuery .= "FROM " . $schedulePrefix . "studentsInGroups ";
            $groupsQuery .= "WHERE StudentId ";
            $groupsQuery .= "IN ( ";
            $groupsQuery .= "SELECT " . $schedulePrefix . "studentsInGroups.StudentId ";
            $groupsQuery .= "FROM " . $schedulePrefix . "studentsInGroups ";
            $groupsQuery .= "JOIN " . $schedulePrefix .  "studentGroups ";
            $groupsQuery .= "ON " . $schedulePrefix . "studentsInGroups.StudentGroupId = " . $schedulePrefix . "studentGroups.StudentGroupId ";
            $groupsQuery .= "JOIN " . $schedulePrefix . "students ";
            $groupsQuery .= "ON " . $schedulePrefix . "studentsInGroups.StudentId = " . $schedulePrefix . "students.StudentId ";
            $groupsQuery .= "WHERE " . $schedulePrefix . "studentGroups.StudentGroupId = " . $groupId . " ";
            $groupsQuery .= "AND " . $schedulePrefix . "students.Expelled = 0 ";
            $groupsQuery .= ")";
            $groupIdsResult = $this->database->query($groupsQuery);

            $groupIdsArray = array();
            while ($id = $groupIdsResult->fetch_assoc()) {
                $groupIdsArray[] = $id["StudentGroupId"];
            }
            $groupCondition = $schedulePrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

            $disciplinesQuery = "SELECT " . $schedulePrefix . "disciplines.DisciplineId, ";
			$disciplinesQuery .= $schedulePrefix . "disciplines.Name as DisciplineName, FIO, ";
			$disciplinesQuery .= $schedulePrefix . "studentGroups.Name as StudentGroupName ";
            $disciplinesQuery .= "FROM " . $schedulePrefix . "disciplines ";			
			$disciplinesQuery .= "JOIN " . $schedulePrefix .  "studentGroups ";
            $disciplinesQuery .= "ON " . $schedulePrefix . "disciplines.StudentGroupId = " . $schedulePrefix . "studentGroups.StudentGroupId ";			
            $disciplinesQuery .= "JOIN " . $schedulePrefix . "teacherForDisciplines ";
            $disciplinesQuery .= "ON " . $schedulePrefix . "disciplines.DisciplineId = " . $schedulePrefix . "teacherForDisciplines.DisciplineId ";
            $disciplinesQuery .= "JOIN " . $schedulePrefix . "teachers ";
            $disciplinesQuery .= "ON " . $schedulePrefix . "teacherForDisciplines.TeacherId = " . $schedulePrefix . "teachers.TeacherId ";
            $disciplinesQuery .= "WHERE " . $groupCondition . " ";
            $disciplinesQuery .= "AND ((Attestation = 2) OR (Attestation = 3)) ";
						

            $discIdsResult = $this->database->query($disciplinesQuery);

            $discIdsArray = array();
            $discNames = array();
            while ($id = $discIdsResult->fetch_assoc()) {
                $discIdsArray[] = $id["DisciplineId"];
                $discNames[$id["DisciplineId"]]["DiscName"] = $id["DisciplineName"];
                $discNames[$id["DisciplineId"]]["FIO"] = $id["FIO"];
				$discNames[$id["DisciplineId"]]["StudentGroupName"] = $id["StudentGroupName"];
            }

            if (count($discIdsArray) != 0) {

                $discCondition = " DisciplineId IN ( " . implode(" , ", $discIdsArray) . " )";

                $auditoriumQuery = "SELECT AuditoriumId, Name ";
                $auditoriumQuery .= "FROM " . $schedulePrefix . "auditoriums";
                $auditoriumsResult = $this->database->query($auditoriumQuery);

                $auditoriums = array();
                while ($auditorium = $auditoriumsResult->fetch_assoc()) {
                    $auditoriums[$auditorium["AuditoriumId"]] = $auditorium["Name"];
                }


                $examsQuery = "SELECT DisciplineId, ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId ";
                $examsQuery .= "FROM " . $dbPrefix . "exams ";
                $examsQuery .= "WHERE " . $discCondition . " ";
                $examsQuery .= "AND IsActive = 1 ";

                $examsQueryResult = $this->database->query($examsQuery);
                $exams = array();
                $exams["Exams"] = array();
                $exams["groupId"] = $groupId;
                $exams["groupName"] = $groupNames[$groupId];
                while ($exam = $examsQueryResult->fetch_assoc()) {
                    $exams["Exams"][] = $exam;
                }
				
                usort($exams["Exams"], "exams_sort");

                for ($i = 0; $i < count($exams["Exams"]); $i++) {
                    if ($exams["Exams"][$i]["ConsultationAuditoriumId"] != 0) {
                        $exams["Exams"][$i]["ConsultationAuditoriumName"] = $auditoriums[$exams["Exams"][$i]["ConsultationAuditoriumId"]];
                    } else {
                        $exams["Exams"][$i]["ConsultationAuditoriumName"] = "";
                        $exams["Exams"][$i]["ConsultationAuditoriumId"] = "";
                    }

                    if ($exams["Exams"][$i]["ExamAuditoriumId"] != 0) {
                        $exams["Exams"][$i]["ExamAuditoriumName"] = $auditoriums[$exams["Exams"][$i]["ExamAuditoriumId"]];
                    } else {
                        $exams["Exams"][$i]["ExamAuditoriumName"] = "";
                        $exams["Exams"][$i]["ExamAuditoriumId"] = "";
                    }

                    $exams["Exams"][$i]["DisciplineName"] = $discNames[$exams["Exams"][$i]["DisciplineId"]]["DiscName"];
                    $exams["Exams"][$i]["TeacherFIO"] = $discNames[$exams["Exams"][$i]["DisciplineId"]]["FIO"];
					$exams["Exams"][$i]["StudentGroupName"] = $discNames[$exams["Exams"][$i]["DisciplineId"]]["StudentGroupName"];

                    if ($exams["Exams"][$i]["ConsultationDateTime"] == "01.01.2020 0:00") {
                        $exams["Exams"][$i]["ConsultationDateTime"] = "";
                    }

                    if ($exams["Exams"][$i]["ExamDateTime"] == "01.01.2020 0:00") {
                        $exams["Exams"][$i]["ExamDateTime"] = "";
                    }
                }
            }
			
            $result[$exams["groupId"]] = $exams;
        }

        return $result;
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetLogEvents($POST)
    {
        if (isset($POST['studentId'])) {
            $studentId = $POST['studentId'];
            $groupsQuery = "SELECT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
            $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
            $groupsQuery .= "WHERE StudentId = " . $studentId;

            $groupIdsResult = $this->database->query($groupsQuery);

            $groupIdsArray = array();
            while ($id = $groupIdsResult->fetch_assoc()) {
                $groupIdsArray[] = $id["StudentGroupId"];
            }
            $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

            $query = "SELECT " . $this->dbPrefix . "lessonLogEvents.LessonLogEventId, ";
            $query .= $this->dbPrefix . "lessonLogEvents.OldLessonId, " . $this->dbPrefix . "lessonLogEvents.NewLessonId, ";
            $query .= $this->dbPrefix . "lessonLogEvents.DateTime, " . $this->dbPrefix . "lessonLogEvents.PublicComment ";
            $query .= "FROM " . $this->dbPrefix . "lessonLogEvents ";

            $query .= "LEFT JOIN " . $this->dbPrefix . "lessons AS newLesson ";
            $query .= "ON " . $this->dbPrefix . "lessonLogEvents.NewLessonId = newLesson.LessonId ";
            $query .= "LEFT JOIN " . $this->dbPrefix . "teacherForDisciplines AS newTFD ";
            $query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
            $query .= "LEFT JOIN " . $this->dbPrefix . "disciplines AS newDics ";
            $query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";

            $query .= "LEFT JOIN " . $this->dbPrefix . "lessons AS oldLesson ";
            $query .= "ON " . $this->dbPrefix . "lessonLogEvents.OldLessonId = oldLesson.LessonId ";
            $query .= "LEFT JOIN " . $this->dbPrefix . "teacherForDisciplines AS oldTFD ";
            $query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
            $query .= "LEFT JOIN " . $this->dbPrefix . "disciplines AS oldDics ";
            $query .= "ON oldTFD.DisciplineId = oldDics.DisciplineId ";

            $query .= "WHERE  ";
            $query .= "newDics.StudentGroupId " . $groupCondition . " OR ";
            $query .= "oldDics.StudentGroupId " . $groupCondition;
        } else {
            if (isset($POST['groupId'])) {
                $groupId = $POST['groupId'];
                $groupsQuery = "SELECT DISTINCT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT " . $this->dbPrefix . "studentsInGroups.StudentId ";
                $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE " . $this->dbPrefix . "studentsInGroups.StudentGroupId = '" . $groupId . "' ";
                $groupsQuery .= ")";

                $groupIdsResult = $this->database->query($groupsQuery);

                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc()) {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }
                $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

                $query = "SELECT " . $this->dbPrefix . "lessonLogEvents.LessonLogEventId, ";
                $query .= $this->dbPrefix . "lessonLogEvents.OldLessonId, " . $this->dbPrefix . "lessonLogEvents.NewLessonId, ";
                $query .= $this->dbPrefix . "lessonLogEvents.DateTime, " . $this->dbPrefix . "lessonLogEvents.PublicComment ";
                $query .= "FROM " . $this->dbPrefix . "lessonLogEvents ";

                $query .= "LEFT JOIN " . $this->dbPrefix . "lessons AS newLesson ";
                $query .= "ON " . $this->dbPrefix . "lessonLogEvents.NewLessonId = newLesson.LessonId ";
                $query .= "LEFT JOIN " . $this->dbPrefix . "teacherForDisciplines AS newTFD ";
                $query .= "ON newLesson.TeacherForDisciplineId = newTFD.TeacherForDisciplineId ";
                $query .= "LEFT JOIN " . $this->dbPrefix . "disciplines AS newDics ";
                $query .= "ON newTFD.DisciplineId = newDics.DisciplineId ";

                $query .= "LEFT JOIN " . $this->dbPrefix . "lessons AS oldLesson ";
                $query .= "ON " . $this->dbPrefix . "lessonLogEvents.OldLessonId = oldLesson.LessonId ";
                $query .= "LEFT JOIN " . $this->dbPrefix . "teacherForDisciplines AS oldTFD ";
                $query .= "ON oldLesson.TeacherForDisciplineId = oldTFD.TeacherForDisciplineId ";
                $query .= "LEFT JOIN " . $this->dbPrefix . "disciplines AS oldDics ";
                $query .= "ON oldTFD.DisciplineId = oldDics.DisciplineId ";

                $query .= "WHERE  ";
                $query .= "newDics.StudentGroupId " . $groupCondition . " OR ";
                $query .= "oldDics.StudentGroupId " . $groupCondition;

            } else {
                $query = "SELECT " . $this->dbPrefix . "lessonLogEvents.LessonLogEventId, ";
                $query .= $this->dbPrefix . "lessonLogEvents.OldLessonId, " . $this->dbPrefix . "lessonLogEvents.NewLessonId, ";
                $query .= $this->dbPrefix . "lessonLogEvents.DateTime, " . $this->dbPrefix . "lessonLogEvents.PublicComment ";
                $query .= "FROM " . $this->dbPrefix . "lessonLogEvents";
            }
        }

        $eventList = $this->database->query($query);

        $lessonLogEvents = array();

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

        $query = "SELECT " . $this->dbPrefix . "teacherForDisciplines.TeacherForDisciplineId, ";
        $query .= $this->dbPrefix . "teacherForDisciplines.TeacherId, ";
        $query .= $this->dbPrefix . "teacherForDisciplines.DisciplineId ";
        $query .= "FROM " . $this->dbPrefix . "teacherForDisciplines";

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

    function teacherFioCmp($a, $b)
    {
        return strcmp($a["FIO"], $b["FIO"]);
    }

    private function GetTechersList()
    {
        $teachers = array();

        $query = "SELECT " . $this->dbPrefix . "teachers.TeacherId, " . $this->dbPrefix . "teachers.FIO ";
        $query .= "FROM " . $this->dbPrefix . "teachers";

        $teacherList = $this->database->query($query);

        while ($dbTeacher = $teacherList->fetch_assoc()) {
            $teacher = array();
            $teacher["TeacherId"] = $dbTeacher["TeacherId"];
            $teacher["FIO"] = $dbTeacher["FIO"];

            $teachers[] = $teacher;
        }

        usort($teachers, array($this, "teacherFioCmp"));

        return $teachers;
    }

    /**
     * @return array
     */
    private function GetStudentInGroupsList()
    {
        $studentsInGroups = array();

        $query = "SELECT " . $this->dbPrefix . "studentsInGroups.StudentsInGroupsId, ";
        $query .= $this->dbPrefix . "studentsInGroups.StudentId, " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
        $query .= "FROM " . $this->dbPrefix . "studentsInGroups";

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

        $query = "SELECT " . $this->dbPrefix . "studentGroups.StudentGroupId, " . $this->dbPrefix . "studentGroups.Name ";
        $query .= "FROM " . $this->dbPrefix . "studentGroups";

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
     * @return array
     */

    function groupNameCmp($a, $b)
    {
        return strcmp($a["Name"], $b["Name"]);
    }

    private function GetMainStudentGroupsList()
    {
        $studentGroups = array();

        $query = "SELECT " . $this->dbPrefix . "studentGroups.StudentGroupId, " . $this->dbPrefix . "studentGroups.Name ";
        $query .= "FROM " . $this->dbPrefix . "studentGroups";

        $groupList = $this->database->query($query);

        $banList = array("1 А", "2 А", "3 А");

        while ($dbGroup = $groupList->fetch_assoc()) {
            if ((strpos($dbGroup["Name"],'+Н') == false) &&
                (strpos($dbGroup["Name"],' + ') == false) &&
                (strpos($dbGroup["Name"],'-А-') == false) &&
                (strpos($dbGroup["Name"],'-Н-') == false) &&
                (strpos($dbGroup["Name"],'-Ф-') == false) &&
                (strpos($dbGroup["Name"],'I') == false) &&
				(0 !== strpos($dbGroup["Name"], '1 А')) && // not strting with
				(0 !== strpos($dbGroup["Name"], '2 А')) && // not strting with
				(0 !== strpos($dbGroup["Name"], '3 А')) && // not strting with
                (!in_array($dbGroup["Name"], $banList)))
            {
                $group = array();
                $group["StudentGroupId"] = $dbGroup["StudentGroupId"];
                $group["Name"] = $dbGroup["Name"];

                $studentGroups[] = $group;
            }
        }

        usort($studentGroups, array($this, "groupNameCmp"));

        return $studentGroups;
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetStudentsList($POST)
    {
        $students = array();

        $query = "SELECT " . $this->dbPrefix . "students.StudentId, ";
        $query .= $this->dbPrefix . "students.F, " . $this->dbPrefix . "students.I, " . $this->dbPrefix . "students.O, ";
        $query .= $this->dbPrefix . "students.Starosta, " . $this->dbPrefix . "students.NFactor, " . $this->dbPrefix . "students.Expelled ";
        $query .= "FROM " . $this->dbPrefix . "students ";
        if (isset($POST['groupId'])) {
            $query = "SELECT " . $this->dbPrefix . "students.StudentId, ";
            $query .= $this->dbPrefix . "students.F, " . $this->dbPrefix . "students.I, " . $this->dbPrefix . "students.O, ";
            $query .= $this->dbPrefix . "students.Starosta, " . $this->dbPrefix . "students.NFactor, " . $this->dbPrefix . ".Expelled ";
            $query .= "FROM " . $this->dbPrefix . "studentsInGroups ";
            $query .= "JOIN " . $this->dbPrefix . "students ";
            $query .= "ON " . $this->dbPrefix . "studentsInGroups.StudentId = " . $this->dbPrefix . "students.StudentId ";
            $query .= "WHERE " . $this->dbPrefix . "studentsInGroups.StudentGroupId = " . $POST['groupId'] . " ";
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

        $query = "SELECT " . $this->dbPrefix . "rings.RingId, " . $this->dbPrefix . "rings.Time ";
        $query .= "FROM " . $this->dbPrefix . "rings";

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
            $groupsQuery = "SELECT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
            $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
            $groupsQuery .= "WHERE StudentId = " . $studentId;

            $groupIdsResult = $this->database->query($groupsQuery);

            $groupIdsArray = array();
            while ($id = $groupIdsResult->fetch_assoc()) {
                $groupIdsArray[] = $id["StudentGroupId"];
            }
            $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

            $query = "SELECT " . $this->dbPrefix . "lessons.LessonId, " . $this->dbPrefix . "lessons.IsActive, ";
            $query .= $this->dbPrefix . "lessons.TeacherForDisciplineId, " . $this->dbPrefix . "lessons.CalendarId, ";
            $query .= $this->dbPrefix . "lessons.RingId, " . $this->dbPrefix . "lessons.AuditoriumId ";
            $query .= "FROM " . $this->dbPrefix . "lessons ";
            $query .= "JOIN " . $this->dbPrefix . "teacherForDisciplines ";
            $query .= "ON " . $this->dbPrefix . "lessons.TeacherForDisciplineId = " . $this->dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
            $query .= "JOIN " . $this->dbPrefix . "disciplines ";
            $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.DisciplineId = " . $this->dbPrefix . "disciplines.DisciplineId ";
            $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId " . $groupCondition;
        } else {
            if (isset($POST['groupId'])) {
                $query = "SELECT " . $this->dbPrefix . "lessons.LessonId, " . $this->dbPrefix . "lessons.IsActive, ";
                $query .= $this->dbPrefix . "lessons.TeacherForDisciplineId, " . $this->dbPrefix . "lessons.CalendarId, ";
                $query .= $this->dbPrefix . "lessons.RingId, " . $this->dbPrefix . "lessons.AuditoriumId ";
                $query .= "FROM " . $this->dbPrefix . "lessons ";
                $query .= "JOIN " . $this->dbPrefix . "teacherForDisciplines ";
                $query .= "ON " . $this->dbPrefix . "lessons.TeacherForDisciplineId = " . $this->dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
                $query .= "JOIN " . $this->dbPrefix . "disciplines ";
                $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.DisciplineId = " . $this->dbPrefix . "disciplines.DisciplineId ";
                if (isset($POST['LimitToExactGroup']) && ($POST['LimitToExactGroup'] == 1)) {
                    $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId = " . $POST['groupId'];
                } else {
                    $groupsQuery = "SELECT DISTINCT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
                    $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                    $groupsQuery .= "WHERE StudentId ";
                    $groupsQuery .= "IN ( ";
                    $groupsQuery .= "SELECT " . $this->dbPrefix . "studentsInGroups.StudentId ";
                    $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                    $groupsQuery .= "JOIN " . $this->dbPrefix . "studentGroups ";
                    $groupsQuery .= "ON " . $this->dbPrefix . "studentsInGroups.StudentGroupId = " . $this->dbPrefix . "studentGroups.StudentGroupId ";
                    $groupsQuery .= "WHERE " . $this->dbPrefix . "studentGroups.StudentGroupId = '" . $POST['groupId'] . "' ";
                    $groupsQuery .= ")";

                    $groupIdsResult = $this->database->query($groupsQuery);

                    $groupIdsArray = array();
                    while ($id = $groupIdsResult->fetch_assoc()) {
                        $groupIdsArray[] = $id["StudentGroupId"];
                    }
                    $groupCondition = " IN ( " . implode(" , ", $groupIdsArray) . " )";

                    $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId " . $groupCondition;
                }

            } else {
                $query = "SELECT " . $this->dbPrefix . "lessons.LessonId, " . $this->dbPrefix . "lessons.IsActive, ";
                $query .= $this->dbPrefix . "lessons.TeacherForDisciplineId, " . $this->dbPrefix . "lessons.CalendarId, ";
                $query .= $this->dbPrefix . "lessons.RingId, " . $this->dbPrefix . "lessons.AuditoriumId ";
                $query .= "FROM " . $this->dbPrefix . "lessons";
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

        $query = "SELECT " . $this->dbPrefix . "disciplines.DisciplineId, " . $this->dbPrefix . "disciplines.Name, ";
        $query .= $this->dbPrefix . "disciplines.Attestation,  ";
        $query .= $this->dbPrefix . "disciplines.AuditoriumHours, " . $this->dbPrefix . "disciplines.LectureHours, ";
        $query .= $this->dbPrefix . "disciplines.PracticalHours, " . $this->dbPrefix . "disciplines.StudentGroupId ";
        $query .= "FROM " . $this->dbPrefix . "disciplines ";

        if (isset($POST['groupId'])) {
            if (isset($POST['LimitToExactGroup']) && ($POST['LimitToExactGroup'] == 1)) {
                $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId = " . $POST['groupId'];
            } else {
                $groupsQuery = "SELECT DISTINCT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT " . $this->dbPrefix . "studentsInGroups.StudentId ";
                $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE " . $this->dbPrefix . "studentsInGroups.StudentGroupId = " . $POST['groupId'] . " ";
                $groupsQuery .= ")";

                $groupIdsResult = $this->database->query($groupsQuery);
                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc()) {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }

                $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";
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
     * @param $POST
     * @return array
     */
    private function GetGroupDisciplinesList($POST)
    {
        $disciplines = array();

        $query = "SELECT " . $this->dbPrefix . "disciplines.DisciplineId, " . $this->dbPrefix . "disciplines.Name, ";
        $query .= $this->dbPrefix . "disciplines.Attestation,  ";
        $query .= $this->dbPrefix . "disciplines.AuditoriumHours, " . $this->dbPrefix . "disciplines.LectureHours, ";
        $query .= $this->dbPrefix . "disciplines.PracticalHours, " . $this->dbPrefix . "disciplines.StudentGroupId, ";
        $query .= $this->dbPrefix . "studentGroups.Name AS studentGroupName " ;
        $query .= "FROM " . $this->dbPrefix . "disciplines ";
        $query .= "JOIN ". $this->dbPrefix . "studentGroups ";
        $query .= "ON ". $this->dbPrefix . "disciplines.StudentGroupId = ". $this->dbPrefix . "studentGroups.StudentGroupId ";

        if (isset($POST['groupId'])) {
            if (isset($POST['LimitToExactGroup']) && ($POST['LimitToExactGroup'] == 1)) {
                $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId = " . $POST['groupId'];
            } else {
                $groupsQuery = "SELECT DISTINCT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT " . $this->dbPrefix . "studentsInGroups.StudentId ";
                $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE " . $this->dbPrefix . "studentsInGroups.StudentGroupId = " . $POST['groupId'] . " ";
                $groupsQuery .= ")";

                $groupIdsResult = $this->database->query($groupsQuery);
                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc()) {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }

                $query .= "WHERE " . $this->dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";
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
            $d["StudentGroupName"] = $discipline["studentGroupName"];

            $disciplines[] = $d;
        }
        return $disciplines;
    }

    /**
     * @param $POST
     * @return array
     */
    private function GetTeacherDisciplinesList($POST)
    {
        $disciplines = array();

        $query = "SELECT " . $this->dbPrefix . "disciplines.DisciplineId, " . $this->dbPrefix . "disciplines.Name, ";
        $query .= $this->dbPrefix . "disciplines.Attestation,  ";
        $query .= $this->dbPrefix . "disciplines.AuditoriumHours, " . $this->dbPrefix . "disciplines.LectureHours, ";
        $query .= $this->dbPrefix . "disciplines.PracticalHours, " . $this->dbPrefix . "disciplines.StudentGroupId, ";
        $query .= $this->dbPrefix . "studentGroups.Name AS studentGroupName " ;
        $query .= "FROM " . $this->dbPrefix . "teacherForDisciplines ";
        $query .= "JOIN " . $this->dbPrefix . "teachers ";
        $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.TeacherId = teachers.TeacherId ";
        $query .= "JOIN " . $this->dbPrefix . "disciplines ";
        $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.DisciplineId = disciplines.DisciplineId ";
        $query .= "JOIN ". $this->dbPrefix . "studentGroups ";
        $query .= "ON ". $this->dbPrefix . "disciplines.StudentGroupId = ". $this->dbPrefix . "studentGroups.StudentGroupId ";

        if (isset($POST['teacherId'])) {
                $query .= "WHERE " . $this->dbPrefix . "teachers.TeacherId = " . $POST['teacherId'];
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
            $d["StudentGroupName"] = $discipline["studentGroupName"];

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

        $query = "SELECT " . $this->dbPrefix . "configs.ConfigOptionId , " . $this->dbPrefix . "configs.Key , " . $this->dbPrefix . "configs.Value ";
        $query .= "FROM " . $this->dbPrefix . "configs";

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

        $query = "SELECT " . $this->dbPrefix . "calendars.CalendarId, " . $this->dbPrefix . "calendars.Date ";
        $query .= "FROM " . $this->dbPrefix . "calendars";

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

        $query = "SELECT " . $this->dbPrefix . "auditoriums.AuditoriumId, " . $this->dbPrefix . "auditoriums.Name ";
        $query .= "FROM " . $this->dbPrefix . "auditoriums";

        $audList = $this->database->query($query);

        while ($auditorium = $audList->fetch_assoc()) {
            $aud = array();
            $aud["AuditoriumId"] = $auditorium["AuditoriumId"];
            $aud["Name"] = $auditorium["Name"];

            $auditoriums[] = $aud;
        }
        return $auditoriums;
    }
	
	/**
     * @return array
     */
    private function GetBuildingsList()
    {
        $buildings = array();

        $query = "SELECT " . $this->dbPrefix . "buildings.BuildingId, " . $this->dbPrefix . "buildings.Name ";
        $query .= "FROM " . $this->dbPrefix . "buildings";

        $buildingList = $this->database->query($query);

        while ($dbBuilding = $buildingList->fetch_assoc()) {
            $building = array();
            $building["BuildingId"] = $dbBuilding["BuildingId"];
            $building["Name"] = $dbBuilding["Name"];

            $buildings[] = $building;
        }
        return $buildings;
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
        $query .= "FROM " . $this->dbPrefix . "faculties ";

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
        $query .= "FROM " . $this->dbPrefix . "GroupsInFaculties ";

        $gifList = $this->database->query($query);

        while ($gif = $gifList->fetch_assoc()) {

            $GroupsInFaculty[] = $gif;
        }
        return $GroupsInFaculty;
    }

    private function ReformatDateToMySQL($date)
    {
        // 0123456789
        // 25.10.1995 => 1995-10-25
        $semesterStartsCorrectFormat =
            mb_substr($date, 6, 4) . "-" .
            mb_substr($date, 3, 2) . "-" .
            mb_substr($date, 0, 2);
        return $semesterStartsCorrectFormat;
    }

    private function GetDailySchedule($POST)
    {
        if ((!isset($POST['groupId'])) && (!isset($POST['date'])))
        {
            return "";
        }

        $dbPrefix = "";
        if (isset($POST['dbPrefix']))
        {
            $dbPrefix = $POST["dbPrefix"];
        }

        if ((isset($POST['groupIds'])) && (isset($POST['date'])))
        {
            $dateString = $POST["date"];
            $groupIds = explode('@', $POST["groupIds"]);

            $result = array();

            // Оптимизировать !!!
            $groups = array();
            $counter = 1;
            for ($i = 0; $i < count($groupIds); $i++) {
                $fquery  = "SELECT ";
                $fquery .= $dbPrefix . "studentGroups.StudentGroupId AS studentGroupId, ";
                $fquery .= $dbPrefix . "studentGroups.Name AS studentGroupName ";
                $fquery .= "FROM " . $dbPrefix . "studentGroups ";
                $fquery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = " . $groupIds[$i] ;

                $queryResult = $this->database->query($fquery);
                $group = $queryResult->fetch_assoc();

                $groupItem = array();
                $groupItem["studentGroupName"] = $group["studentGroupName"];
                $groupItem["studentGroupId"] = $group["studentGroupId"];
                $groups[$counter] = $groupItem;
                $counter++;
            }
            $counter--;

            for ($i = 1; $i <= $counter; $i++) {
                $groupSchedule = array();
                $groupSchedule["studentGroupName"] = $groups[$i]["studentGroupName"];
                $groupSchedule["studentGroupId"] = $groups[$i]["studentGroupId"];

                $groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
                $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
                $groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
                $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
                $groupsQuery .= "JOIN " . $dbPrefix . "students ";
                $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentId = " . $dbPrefix . "students.StudentId ";
                $groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $groupSchedule["studentGroupId"] ." ";
                $groupsQuery .= "AND " . $dbPrefix . "students.Expelled = 0 ";
                $groupsQuery .= ")";
                $groupIdsResult = $this->database->query($groupsQuery);

                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc())
                {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }
                $groupCondition = $dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

                $query  = "SELECT " . $dbPrefix . "rings.Time, " . $dbPrefix . "disciplines.Name AS discName, ";
                $query .= $dbPrefix . "teachers.FIO, " . $dbPrefix . "auditoriums.Name AS audName, ";
                $query .= $dbPrefix . "studentGroups.Name AS groupName ";
                $query .= "FROM " . $dbPrefix . "lessons ";
                $query .= "JOIN " . $dbPrefix . "calendars ";
                $query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
                $query .= "JOIN " . $dbPrefix . "rings ";
                $query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
                $query .= "JOIN " . $dbPrefix . "auditoriums ";
                $query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumID ";
                $query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
                $query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
                $query .= "JOIN " . $dbPrefix . "teachers ";
                $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
                $query .= "JOIN " . $dbPrefix . "disciplines ";
                $query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
                $query .= "JOIN " . $dbPrefix . "studentGroups ";
                $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
                $query .= "WHERE " . $dbPrefix . "lessons.IsActive=1 ";
                $query .= "AND (" . $groupCondition . ") ";
                $query .= "AND " . $dbPrefix . "calendars.Date = \"" . $dateString . "\" ";
                $query .= "ORDER BY " . $dbPrefix . "rings.Time ASC, groupName";

                $lessonsList = $this->database->query($query);
                $groupSchedule["Lessons"] = array();

                while($lesson = $lessonsList->fetch_assoc())
                {
                    $groupSchedule["Lessons"][] = $lesson;
                }

                $result[] = $groupSchedule;
            }

            return $result;
        }

        if ((!isset($POST['groupId'])) && (isset($POST['date'])) && (isset($POST['facultyId'])))
        {
            $dateString = $POST["date"];
            $facultyId = $POST["facultyId"];

            $result = array();

            $fquery  = "SELECT " . $dbPrefix . "faculties.FacultyId AS FacultyId, ";
            $fquery .= $dbPrefix . "faculties.Name AS FacultyName, ";
            $fquery .= $dbPrefix . "studentGroups.StudentGroupId AS studentGroupId, ";
            $fquery .= $dbPrefix . "studentGroups.Name AS studentGroupName ";
            $fquery .= "FROM " . $dbPrefix . "GroupsInFaculties ";
            $fquery .= "JOIN " . $dbPrefix . "faculties ";
            $fquery .= "ON " . $dbPrefix . "GroupsInFaculties.FacultyId = " . $dbPrefix . "faculties.FacultyId ";
            $fquery .= "JOIN " . $dbPrefix . "studentGroups ";
            $fquery .= "ON " . $dbPrefix . "GroupsInFaculties.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
            $fquery .= "WHERE " . $dbPrefix . "faculties.FacultyId = " . $facultyId;


            $queryResult = $this->database->query($fquery);
            while ($group = $queryResult->fetch_assoc())
            {
                $groupSchedule = array();
                $groupSchedule["studentGroupName"] = $group["studentGroupName"];
                $groupSchedule["studentGroupId"] = $group["studentGroupId"];

                $groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
                $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
                $groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
                $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
                $groupsQuery .= "JOIN " . $dbPrefix . "students ";
                $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentId = " . $dbPrefix . "students.StudentId ";
                $groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $group["studentGroupId"] ." ";
                $groupsQuery .= "AND " . $dbPrefix . "students.Expelled = 0 ";
                $groupsQuery .= ")";
                $groupIdsResult = $this->database->query($groupsQuery);

                $groupIdsArray = array();
                while ($id = $groupIdsResult->fetch_assoc())
                {
                    $groupIdsArray[] = $id["StudentGroupId"];
                }
                $groupCondition = $dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

                $query  = "SELECT " . $dbPrefix . "rings.Time, " . $dbPrefix . "disciplines.Name AS discName, ";
                $query .= $dbPrefix . "teachers.FIO, " . $dbPrefix . "auditoriums.Name AS audName, ";
                $query .= $dbPrefix . "studentGroups.Name AS groupName ";
                $query .= "FROM " . $dbPrefix . "lessons ";
                $query .= "JOIN " . $dbPrefix . "calendars ";
                $query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
                $query .= "JOIN " . $dbPrefix . "rings ";
                $query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
                $query .= "JOIN " . $dbPrefix . "auditoriums ";
                $query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumID ";
                $query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
                $query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
                $query .= "JOIN " . $dbPrefix . "teachers ";
                $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
                $query .= "JOIN " . $dbPrefix . "disciplines ";
                $query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
                $query .= "JOIN " . $dbPrefix . "studentGroups ";
                $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
                $query .= "WHERE " . $dbPrefix . "lessons.IsActive=1 ";
                $query .= "AND (" . $groupCondition . ") ";
                $query .= "AND " . $dbPrefix . "calendars.Date = \"" . $dateString . "\" ";
                $query .= "ORDER BY " . $dbPrefix . "rings.Time ASC, groupName";

                $lessonsList = $this->database->query($query);
                $groupSchedule["Lessons"] = array();

                while($lesson = $lessonsList->fetch_assoc())
                {
                    $groupSchedule["Lessons"][] = $lesson;
                }

                $result[] = $groupSchedule;
            }

            return $result;
        }

        if ((!isset($POST['groupId'])) && (isset($POST['date'])))
        {
            $dateString = $POST["date"];

            $result = array();

            $fquery  = "SELECT faculties.FacultyId AS FacultyId, ";
            $fquery .= "faculties.Name AS FacultyName, ";
            $fquery .= "studentGroups.StudentGroupId AS studentGroupId, ";
            $fquery .= "studentGroups.Name AS studentGroupName ";
            $fquery .= "FROM `GroupsInFaculties` ";
            $fquery .= "JOIN faculties ";
            $fquery .= "ON GroupsInFaculties.FacultyId = faculties.FacultyId ";
            $fquery .= "JOIN studentGroups ";
            $fquery .= "ON GroupsInFaculties.StudentGroupId = studentGroups.StudentGroupId ";

            $queryResult = $this->database->query($fquery);
            while ($group = $queryResult->fetch_assoc())
            {
                if (!array_key_exists($group["FacultyId"], $result))
                {
                    $result[$group["FacultyId"]] = array();
                    $result[$group["FacultyId"]]["FacultyName"] = $group["FacultyName"];
                    $result[$group["FacultyId"]]["Schedule"] = array();
                }

                $result[$group["FacultyId"]]["Schedule"][$group["studentGroupId"]] = array();
                $result[$group["FacultyId"]]["Schedule"][$group["studentGroupId"]]["studentGroupName"] = $group["studentGroupName"];

                $groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
                $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
                $groupsQuery .= "WHERE StudentId ";
                $groupsQuery .= "IN ( ";
                $groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
                $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
                $groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
                $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
                $groupsQuery .= "JOIN " . $dbPrefix . "students ";
                $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentId = " . $dbPrefix . "students.StudentId ";
                $groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $group["studentGroupId"] ." ";
                $groupsQuery .= "AND " . $dbPrefix . "students.Expelled = 0 ";
                $groupsQuery .= ")";
                $groupIdsResult = $this->database->query($groupsQuery);

                $result[$group["FacultyId"]]["Schedule"][$group["studentGroupId"]]["groupIdsArray"] = array();
                while ($id = $groupIdsResult->fetch_assoc())
                {
                    $result[$group["FacultyId"]]["Schedule"][$group["studentGroupId"]]["groupIdsArray"][] = $id["StudentGroupId"];
                }
            }

            $query  = "SELECT ";
            $query .= $dbPrefix . "rings.Time, ";
            $query .= $dbPrefix . "auditoriums.Name AS AudName, ";
            $query .= $dbPrefix . "studentGroups.StudentGroupId AS studentGroupId, ";
            $query .= $dbPrefix . "studentGroups.Name AS studentGroupName, ";
            $query .= $dbPrefix . "disciplines.Name AS discName, ";
            $query .= $dbPrefix . "teachers.FIO, ";
            $query .= $dbPrefix . "auditoriums.AuditoriumId, ";
            $query .= $dbPrefix . "buildings.BuildingId ";
            $query .= "FROM " . $dbPrefix . "lessons ";
            $query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
            $query .= "ON " . $dbPrefix . "lessons.`TeacherForDisciplineId` = " . $dbPrefix . "teacherForDisciplines.`TeacherForDisciplineId` ";
            $query .= "JOIN " . $dbPrefix . "disciplines ";
            $query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
            $query .= "JOIN " . $dbPrefix . "studentGroups ";
            $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
            $query .= "JOIN " . $dbPrefix . "teachers ";
            $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
            $query .= "JOIN " . $dbPrefix . "rings ";
            $query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
            $query .= "JOIN " . $dbPrefix . "auditoriums ";
            $query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
            $query .= "JOIN " . $dbPrefix . "calendars ";
            $query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
            $query .= "JOIN " . $dbPrefix . "buildings ";
            $query .= "ON " . $dbPrefix . "auditoriums.BuildingId = " . $dbPrefix . "buildings.BuildingId ";
            $query .= "WHERE " . $dbPrefix . "calendars.Date = \"" . $dateString . "\" ";
            $query .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

            $queryResult = $this->database->query($query);

            while ($lesson = $queryResult->fetch_assoc())
            {
                foreach ($result as $facultyId => $faculty)
                {
                    foreach ($result[$facultyId]["Schedule"] as $groupId => $group)
                    {
                        if (in_array($lesson["studentGroupId"], $result[$facultyId]["Schedule"][$groupId]["groupIdsArray"]))
                        {
                            if (!array_key_exists("Lessons", $result[$facultyId]["Schedule"][$groupId]))
                            {
                                $result[$facultyId]["Schedule"][$groupId]["Lessons"] = array();
                            }

                            $result[$facultyId]["Schedule"][$groupId]["Lessons"][] = $lesson;
                        }
                    }
                }
            }

            return $result;
        }

        if ((isset($POST['groupId'])) && (isset($POST['date'])))
        {
            $groupId = $POST["groupId"];
            $dateString = $POST["date"];

            $groupNameQuery = "SELECT " . $dbPrefix . "studentGroups.Name FROM " . $dbPrefix . "studentGroups WHERE StudentGroupId = " . $groupId;


            $sGroup = $this->database->query($groupNameQuery);
            $groupNameArr = $sGroup->fetch_assoc();
            $groupName = $this->database->real_escape_string($groupNameArr["Name"]);

            $studentId = -1;
            if ((isset($_SESSION['NUlogin'])) && (!isset($_SESSION['AltUserId'])))
            {
                $FIO = explode(' ',trim($_SESSION['NUlogin']));
                $F = $FIO[0];

                $MySQLDate = ReformatDateToMySQL($_SESSION['NUpassword']);
                $studentIdQuery  = "SELECT StudentId ";
                $studentIdQuery .= "FROM " . $dbPrefix . "students ";
                $studentIdQuery .= "WHERE F = '" . $F . "' ";
                $studentIdQuery .= "AND BirthDate = '" . $MySQLDate . "' ";
                $studentResult = $this->database->query($studentIdQuery);
                $studentIdArray = $studentResult->fetch_assoc();
                $studentId = $studentIdArray["StudentId"];
            }

            $altUserId = "";
            if (isset($_SESSION['AltUserId']))
            {
                $altUserId = $_SESSION['AltUserId'];
            }
            /*

            $todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
            $today = gmdate("y.m.d H:i:s", $todayStamp);
            $statisticQuery  = "INSERT INTO " . $dbPrefix . "DailyScheduleStats( groupId, date, statDate";
            $statisticQuery .= ", StudentId ";
            $statisticQuery .= ", AltUserId ";
            $statisticQuery .= ") ";
            $statisticQuery .= "VALUES ( \"";
            $statisticQuery .= $groupName;
            $statisticQuery .= "\", ";
            $statisticQuery .= $dateString;
            $statisticQuery .= ", \"";
            $statisticQuery .= $today . "\"";
            $statisticQuery .= ", ";
            $statisticQuery .= $studentId;
            $statisticQuery .= ", \"";
            $statisticQuery .= $altUserId;
            $statisticQuery .= "\" ";
            $statisticQuery .= ")";

            /*
            if (isset($_SESSION['NUlogin']))
            {
                echo $studentId . "<br />";
                echo $statisticQuery . "<br />";
            }*/
            /*
            $this->database->query($statisticQuery);
            */

            $groupsQuery  = "SELECT DISTINCT " . $dbPrefix . "studentsInGroups.StudentGroupId ";
            $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
            $groupsQuery .= "WHERE StudentId ";
            $groupsQuery .= "IN ( ";
            $groupsQuery .= "SELECT " . $dbPrefix . "studentsInGroups.StudentId ";
            $groupsQuery .= "FROM " . $dbPrefix . "studentsInGroups ";
            $groupsQuery .= "JOIN " . $dbPrefix . "studentGroups ";
            $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
            $groupsQuery .= "JOIN " . $dbPrefix . "students ";
            $groupsQuery .= "ON " . $dbPrefix . "studentsInGroups.StudentId = " . $dbPrefix . "students.StudentId ";
            $groupsQuery .= "WHERE " . $dbPrefix . "studentGroups.StudentGroupId = ". $groupId ." ";
            $groupsQuery .= "AND " . $dbPrefix . "students.Expelled = 0 ";
            $groupsQuery .= ")";
            $groupIdsResult = $this->database->query($groupsQuery);

            $groupIdsArray = array();
            while ($id = $groupIdsResult->fetch_assoc())
            {
                $groupIdsArray[] = $id["StudentGroupId"];
            }
            $groupCondition = $dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

            $query  = "SELECT " . $dbPrefix . "rings.Time, " . $dbPrefix . "disciplines.Name AS discName, ";
            $query .= $dbPrefix . "teachers.FIO, " . $dbPrefix . "auditoriums.Name AS audName, ";
            $query .= $dbPrefix . "studentGroups.Name AS groupName ";
            $query .= "FROM " . $dbPrefix . "lessons ";
            $query .= "JOIN " . $dbPrefix . "calendars ";
            $query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
            $query .= "JOIN " . $dbPrefix . "rings ";
            $query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
            $query .= "JOIN " . $dbPrefix . "auditoriums ";
            $query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumID ";
            $query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
            $query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
            $query .= "JOIN " . $dbPrefix . "teachers ";
            $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
            $query .= "JOIN " . $dbPrefix . "disciplines ";
            $query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
            $query .= "JOIN " . $dbPrefix . "studentGroups ";
            $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
            $query .= "WHERE " . $dbPrefix . "lessons.IsActive=1 ";
            $query .= "AND (" . $groupCondition . ") ";
            $query .= "AND " . $dbPrefix . "calendars.Date = \"" . $dateString . "\"";
            $query .= "ORDER BY " . $dbPrefix . "rings.Time ASC, groupName";

            $lessonsList = $this->database->query($query);

            if ($lessonsList->num_rows != 0)
            {
                $resultLessons = array();

                while($lesson = $lessonsList->fetch_assoc())
                {
                    $resultLesson = array();

                    $lesHour = mb_substr($lesson["Time"], 0, 2);
                    $lesMin = mb_substr($lesson["Time"], 3, 2);
                    $timeDiff = Utilities::DiffTimeWithNowInMinutes($lesHour, $lesMin);

                    $today = date("Y-m-d");

                    if (($timeDiff < 0) && ($timeDiff > -80) && ($today == $dateString))
                    {
                        $resultLesson["onGoing"] = 1;
                    }
                    else
                    {
                        $resultLesson["onGoing"] = 0;
                    }


                    $resultLesson["Time"] = substr($lesson["Time"], 0, strlen($lesson["Time"])-3);
                    $resultLesson["discName"] = $lesson["discName"];
                    $resultLesson["FIO"] = $lesson["FIO"];
                    $resultLesson["audName"] = $lesson["audName"];
                    $resultLesson["groupName"] = $lesson["groupName"];

                    $resultLessons[] = $resultLesson;
                }

                return $resultLessons;
            }
            else
            {
                return "Занятий нет";
            }
        }
    }

    private function GetWeekSchedule($POST)
    {
        $week = 0;

        if ((!isset($POST['groupId'])) || (!isset($POST['week'])))
        {
            echo $this->APIError("Необходимые параметры запроса week и groupId");
            exit;
        }
        else {
            $week = $POST["week"];
            $groupId = $POST["groupId"];
        }

        $semesterStartsQuery  = "SELECT `Value` ";
        $semesterStartsQuery .= "FROM " . $this->dbPrefix . "configs ";
        $semesterStartsQuery .= "WHERE `Key` = 'Semester Starts' ";

        $semesterStartsResult = $this->database->query($semesterStartsQuery);

        $dateObject = $semesterStartsResult->fetch_assoc();
        $dateString = $dateObject["Value"];

        $weekMinusOne = $week - 1;
        $weekFormat = "+ " . $weekMinusOne . " week";

        $weekStarts = strtotime($weekFormat, strtotime("Monday this week",strtotime($dateString)));

        $weekEnds= strtotime("+ 6 days", $weekStarts);

        $groupsQuery = "SELECT DISTINCT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT " . $this->dbPrefix . "studentsInGroups.StudentId ";
        $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
        $groupsQuery .= "JOIN " . $this->dbPrefix . "studentGroups ";
        $groupsQuery .= "ON " . $this->dbPrefix . "studentsInGroups.StudentGroupId = " . $this->dbPrefix . "studentGroups.StudentGroupId ";
        $groupsQuery .= "JOIN " . $this->dbPrefix . "students ";
        $groupsQuery .= "ON " . $this->dbPrefix . "studentsInGroups.StudentId = " . $this->dbPrefix . "students.StudentId ";
        $groupsQuery .= "WHERE " . $this->dbPrefix . "studentGroups.StudentGroupId = " . $groupId . " ";
        $groupsQuery .= "AND " . $this->dbPrefix . "students.Expelled = 0 ";
        $groupsQuery .= ")";
        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc()) {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = $this->dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $query  = "SELECT ";
        $query .= $this->dbPrefix . "calendars.Date AS date, ";
        $query .= $this->dbPrefix . "rings.Time, " . $this->dbPrefix . "disciplines.Name AS discName, ";
        $query .= $this->dbPrefix . "teachers.FIO, " . $this->dbPrefix . "auditoriums.Name AS audName, ";
        $query .= $this->dbPrefix . "studentGroups.Name AS groupName ";

        $query .= "FROM " . $this->dbPrefix . "lessons ";
        $query .= "JOIN " . $this->dbPrefix . "calendars ";
        $query .= "ON " . $this->dbPrefix . "lessons.CalendarId = " . $this->dbPrefix . "calendars.CalendarId ";
        $query .= "JOIN " . $this->dbPrefix . "rings ";
        $query .= "ON " . $this->dbPrefix . "lessons.RingId = " . $this->dbPrefix . "rings.RingId ";
        $query .= "JOIN " . $this->dbPrefix . "auditoriums ";
        $query .= "ON " . $this->dbPrefix . "lessons.AuditoriumId = " . $this->dbPrefix . "auditoriums.AuditoriumID ";
        $query .= "JOIN " . $this->dbPrefix . "teacherForDisciplines ";
        $query .= "ON " . $this->dbPrefix . "lessons.TeacherForDisciplineId = " . $this->dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $query .= "JOIN " . $this->dbPrefix . "teachers ";
        $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.TeacherId = " . $this->dbPrefix . "teachers.TeacherId ";
        $query .= "JOIN " . $this->dbPrefix . "disciplines ";
        $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.DisciplineId = " . $this->dbPrefix . "disciplines.DisciplineId ";
        $query .= "JOIN " . $this->dbPrefix . "studentGroups ";
        $query .= "ON " . $this->dbPrefix . "disciplines.StudentGroupId = " . $this->dbPrefix . "studentGroups.StudentGroupId ";
        $query .= "WHERE " . $this->dbPrefix . "lessons.IsActive=1 ";
        $query .= "AND (" . $groupCondition . ") ";
        $query .= "AND " . $this->dbPrefix . "calendars.Date BETWEEN \"" . date("Y-m-d", $weekStarts) . "\" AND \"" . date("Y-m-d", $weekEnds) . "\" ";
        $query .= "ORDER BY " . $this->dbPrefix . "calendars.Date, " . $this->dbPrefix . "rings.Time ASC, groupName";

        $lessonsList = $this->database->query($query);

        $lessons = array();

        while($lesson = $lessonsList->fetch_assoc())
        {
            $lesson["dow"] = date("N", strtotime($lesson["date"]));
            $lessons[] = $lesson;
        }

        return $lessons;
    }

    private function GetGroupSchedule($POST)
    {
        if (!isset($POST['groupId']))
        {
            echo $this->APIError("Необходимые параметры запроса groupId");
            exit;
        }
        else {
            $groupId = $POST["groupId"];
        }

        $semesterStartsQuery  = "SELECT `Value` ";
        $semesterStartsQuery .= "FROM " . $this->dbPrefix . "configs ";
        $semesterStartsQuery .= "WHERE `Key` = 'Semester Starts' ";

        $semesterStartsResult = $this->database->query($semesterStartsQuery);

        $dateObject = $semesterStartsResult->fetch_assoc();
        $semesterStartsDateString = $dateObject["Value"];

        $groupsQuery = "SELECT DISTINCT " . $this->dbPrefix . "studentsInGroups.StudentGroupId ";
        $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
        $groupsQuery .= "WHERE StudentId ";
        $groupsQuery .= "IN ( ";
        $groupsQuery .= "SELECT " . $this->dbPrefix . "studentsInGroups.StudentId ";
        $groupsQuery .= "FROM " . $this->dbPrefix . "studentsInGroups ";
        $groupsQuery .= "JOIN " . $this->dbPrefix . "studentGroups ";
        $groupsQuery .= "ON " . $this->dbPrefix . "studentsInGroups.StudentGroupId = " . $this->dbPrefix . "studentGroups.StudentGroupId ";
        $groupsQuery .= "JOIN " . $this->dbPrefix . "students ";
        $groupsQuery .= "ON " . $this->dbPrefix . "studentsInGroups.StudentId = " . $this->dbPrefix . "students.StudentId ";
        $groupsQuery .= "WHERE " . $this->dbPrefix . "studentGroups.StudentGroupId = " . $groupId . " ";
        $groupsQuery .= "AND " . $this->dbPrefix . "students.Expelled = 0 ";
        $groupsQuery .= ")";
        $groupIdsResult = $this->database->query($groupsQuery);

        $groupIdsArray = array();
        while ($id = $groupIdsResult->fetch_assoc()) {
            $groupIdsArray[] = $id["StudentGroupId"];
        }
        $groupCondition = $this->dbPrefix . "disciplines.StudentGroupId IN ( " . implode(" , ", $groupIdsArray) . " )";

        $query  = "SELECT ";
        $query .= $this->dbPrefix . "calendars.Date AS date, ";
        $query .= $this->dbPrefix . "rings.Time, " . $this->dbPrefix . "disciplines.Name AS discName, ";
        $query .= $this->dbPrefix . "teachers.FIO, " . $this->dbPrefix . "auditoriums.Name AS audName, ";
        $query .= $this->dbPrefix . "studentGroups.Name AS groupName ";

        $query .= "FROM " . $this->dbPrefix . "lessons ";
        $query .= "JOIN " . $this->dbPrefix . "calendars ";
        $query .= "ON " . $this->dbPrefix . "lessons.CalendarId = " . $this->dbPrefix . "calendars.CalendarId ";
        $query .= "JOIN " . $this->dbPrefix . "rings ";
        $query .= "ON " . $this->dbPrefix . "lessons.RingId = " . $this->dbPrefix . "rings.RingId ";
        $query .= "JOIN " . $this->dbPrefix . "auditoriums ";
        $query .= "ON " . $this->dbPrefix . "lessons.AuditoriumId = " . $this->dbPrefix . "auditoriums.AuditoriumID ";
        $query .= "JOIN " . $this->dbPrefix . "teacherForDisciplines ";
        $query .= "ON " . $this->dbPrefix . "lessons.TeacherForDisciplineId = " . $this->dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $query .= "JOIN " . $this->dbPrefix . "teachers ";
        $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.TeacherId = " . $this->dbPrefix . "teachers.TeacherId ";
        $query .= "JOIN " . $this->dbPrefix . "disciplines ";
        $query .= "ON " . $this->dbPrefix . "teacherForDisciplines.DisciplineId = " . $this->dbPrefix . "disciplines.DisciplineId ";
        $query .= "JOIN " . $this->dbPrefix . "studentGroups ";
        $query .= "ON " . $this->dbPrefix . "disciplines.StudentGroupId = " . $this->dbPrefix . "studentGroups.StudentGroupId ";
        $query .= "WHERE " . $this->dbPrefix . "lessons.IsActive=1 ";
        $query .= "AND (" . $groupCondition . ") ";
        $query .= "ORDER BY " . $this->dbPrefix . "calendars.Date, rings.Time ASC, groupName";

        $lessonsList = $this->database->query($query);

        $lessons = array();

        while($lesson = $lessonsList->fetch_assoc())
        {
            $lesson["week"] = Utilities::WeekFromDate($lesson["date"], $semesterStartsDateString);
            $lesson["dow"] = date("N", strtotime($lesson["date"]));

            if (!array_key_exists($lesson["week"], $lessons))
            {
                $lessons[$lesson["week"]] = array();
            }

            $lessons[$lesson["week"]][] = $lesson;
        }

        return $lessons;
    }

    private function GetTeacherWeekSchedule($POST)
    {
        $dbPrefix = "";
        if (isset($POST['dbPrefix'])) {
            $dbPrefix = $POST["dbPrefix"];
        }
        $teacherId = $POST["teacherId"];
        $week = $POST["week"];

        global $database;
        global $options;

        $query  = "SELECT " . $dbPrefix . "calendars.Date, " . $dbPrefix . "rings.Time, " . $dbPrefix . "studentGroups.Name as groupName, ";
        $query .= $dbPrefix . "disciplines.Name as disciplineName, " . $dbPrefix . "auditoriums.Name as auditoriumName, ";
        $query .= $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $query .= "FROM " . $dbPrefix . "lessons ";
        $query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
        $query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $query .= "JOIN " . $dbPrefix . "teachers ";
        $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
        $query .= "JOIN " . $dbPrefix . "calendars ";
        $query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
        $query .= "JOIN " . $dbPrefix . "rings ";
        $query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
        $query .= "JOIN " . $dbPrefix . "disciplines ";
        $query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
        $query .= "JOIN " . $dbPrefix . "studentGroups ";
        $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
        $query .= "JOIN " . $dbPrefix . "auditoriums ";
        $query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
        $query .= "WHERE " . $dbPrefix . "teachers.TeacherId=" . $teacherId . " ";
        $query .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

        $queryResult = $database->query($query);

        $lessonsArray = array();
        while ($lesson = $queryResult->fetch_assoc())
        {
            $lessonsArray[] = $lesson;
        }

        $semesterStarts = $options["Semester Starts"];

        $primaryResult = array();

        foreach ($lessonsArray as $lesson) {

            $lessonWeek = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);


            if ($lessonWeek != $week)
            {
                continue;
            }

            $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["Date"]);
            $dow = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];

            $lesson["dow"] = $dow;
            $lesson["Time"] = $lesson["Time"];

            $primaryResult[] = $lesson;
        }

        usort($primaryResult, function($a, $b)
        {
            if ($a["dow"] < $b["dow"])
            {
                return -1;
            }

            if ($a["dow"] > $b["dow"])
            {
                return 1;
            }

            if ($a["Time"] < $b["Time"])
            {
                return -1;
            }

            if ($a["Time"] > $b["Time"])
            {
                return 1;
            }

            return 0;
        });



        return $primaryResult;
    }

    private function GetTeacherSchedule($POST)
    {
        $dbPrefix = "";
        if (isset($POST['dbPrefix'])) {
            $dbPrefix = $POST["dbPrefix"];
        }
        $teacherId = $POST["teacherId"];

        global $database;
        global $options;

        $query  = "SELECT " . $dbPrefix . "calendars.Date, " . $dbPrefix . "rings.Time, " . $dbPrefix . "studentGroups.Name as groupName, ";
        $query .= $dbPrefix . "disciplines.Name as disciplineName, " . $dbPrefix . "auditoriums.Name as auditoriumName, ";
        $query .= $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $query .= "FROM " . $dbPrefix . "lessons ";
        $query .= "JOIN " . $dbPrefix . "teacherForDisciplines ";
        $query .= "ON " . $dbPrefix . "lessons.TeacherForDisciplineId = " . $dbPrefix . "teacherForDisciplines.TeacherForDisciplineId ";
        $query .= "JOIN " . $dbPrefix . "teachers ";
        $query .= "ON " . $dbPrefix . "teacherForDisciplines.TeacherId = " . $dbPrefix . "teachers.TeacherId ";
        $query .= "JOIN " . $dbPrefix . "calendars ";
        $query .= "ON " . $dbPrefix . "lessons.CalendarId = " . $dbPrefix . "calendars.CalendarId ";
        $query .= "JOIN " . $dbPrefix . "rings ";
        $query .= "ON " . $dbPrefix . "lessons.RingId = " . $dbPrefix . "rings.RingId ";
        $query .= "JOIN " . $dbPrefix . "disciplines ";
        $query .= "ON " . $dbPrefix . "teacherForDisciplines.DisciplineId = " . $dbPrefix . "disciplines.DisciplineId ";
        $query .= "JOIN " . $dbPrefix . "studentGroups ";
        $query .= "ON " . $dbPrefix . "disciplines.StudentGroupId = " . $dbPrefix . "studentGroups.StudentGroupId ";
        $query .= "JOIN " . $dbPrefix . "auditoriums ";
        $query .= "ON " . $dbPrefix . "lessons.AuditoriumId = " . $dbPrefix . "auditoriums.AuditoriumId ";
        $query .= "WHERE " . $dbPrefix . "teachers.TeacherId=" . $teacherId . " ";
        $query .= "AND " . $dbPrefix . "lessons.isActive = 1 ";

        $queryResult = $database->query($query);

        $lessonsArray = array();
        while ($lesson = $queryResult->fetch_assoc())
        {
            $lessonsArray[] = $lesson;
        }

        $semesterStarts = $options["Semester Starts"];

        $result = array();
        foreach ($lessonsArray as $lesson) {
            $lessonWeek = Utilities::WeekFromDate($lesson["Date"], $semesterStarts);
            $lessonDate = DateTime::createFromFormat('Y-m-d', $lesson["Date"]);
            $dow = Utilities::$DOWEnToRu[date( "w", $lessonDate->getTimestamp())];
            $time = mb_substr($lesson["Time"], 0, 5);
            $dowAndTime = $dow . " " . $time;

            $lesson["dow"] = $dow;
            $lesson["lessonWeek"] = $lessonWeek;

            if(!array_key_exists($dowAndTime, $result))
            {
                $result[$dowAndTime] = array();
            }

            $tfd = $lesson["TeacherForDisciplineId"];
            if (!array_key_exists($tfd, $result[$dowAndTime]))
            {
                $result[$dowAndTime][$tfd] = array();
                // Нет смысла
                unset($lesson["Date"]);
                unset($lesson["lessonWeek"]);

                // Избыточные
                //unset($lesson["Time"]);
                //unset($lesson["dow"]);
                //unset($lesson["TeacherForDisciplineId"]);

                $result[$dowAndTime][$tfd]["Lesson"] = $lesson;
                $result[$dowAndTime][$tfd]["Weeks"] = array();
                $result[$dowAndTime][$tfd]["Weeks"]["Array"] = array();
                $result[$dowAndTime][$tfd]["AuditoriumWeeks"] = array();
            }

            $result[$dowAndTime][$tfd]["Weeks"]["Array"][] = $lessonWeek;
            $result[$dowAndTime][$tfd]["Weeks"]["String"] =
                Utilities::GatherWeeksToString($result[$dowAndTime][$tfd]["Weeks"]["Array"]);

            if (!array_key_exists($lesson["auditoriumName"], $result[$dowAndTime][$tfd]["AuditoriumWeeks"]))
            {
                $result[$dowAndTime][$tfd]["AuditoriumWeeks"][$lesson["auditoriumName"]] = array();
                $result[$dowAndTime][$tfd]["AuditoriumWeeks"][$lesson["auditoriumName"]]["Array"] = array();
            }

            $result[$dowAndTime][$tfd]["AuditoriumWeeks"][$lesson["auditoriumName"]]["Array"][] = $lessonWeek;
            $result[$dowAndTime][$tfd]["AuditoriumWeeks"][$lesson["auditoriumName"]]["String"] =
                Utilities::GatherWeeksToString($result[$dowAndTime][$tfd]["AuditoriumWeeks"][$lesson["auditoriumName"]]["Array"]);

            unset($result[$dowAndTime][$tfd]["Lesson"]["auditoriumName"]);
        }

        ksort($result);


        foreach ($result as $key => $value)
        {

            $result[$key] = array_values($value);
        }

        return array_values($result);
    }
}

function exams_sort($a, $b) {
    $aDate = DateTime::createFromFormat('d.m.Y H:i',$a["ConsultationDateTime"]);
    $bDate = DateTime::createFromFormat('d.m.Y H:i',$b["ConsultationDateTime"]);

    if ($aDate == $bDate) {
        return 0;
    }
    $result = ($aDate < $bDate) ? -1 : 1;

    //echo "<pre>";
    //echo print_r($a) . " / " . print_r($b) . " = " . $result . "<br />";
    //echo "</pre>";

    return $result;
}
?>