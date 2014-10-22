<?php
require_once("Database.php");
global $database;

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$dbPrefix = $_POST["dbPrefix"];
$tableSelector = $_POST["tableSelector"];
$data = json_decode($_POST["data"], true);

switch ($tableSelector) {
    case "auditoriums":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "auditoriums";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "auditoriums ( ";
        $query .= "`AuditoriumId` int(11) NOT NULL, ";
        $query .= "`Name` varchar(100) NOT NULL, ";
        $query .= "`BuildingId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`AuditoriumId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "auditoriums(AuditoriumId, Name, BuildingId) VALUES ( ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $auditorium) {
            $database->bindAndExecute("isi", $auditorium["AuditoriumId"], $auditorium["Name"], $auditorium["BuildingId"]);
        }
        break;
    case "buildings":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "buildings";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "buildings ( ";
        $query .= "`BuildingId` int(11) NOT NULL, ";
        $query .= "`Name` varchar(100) NOT NULL, ";
        $query .= "PRIMARY KEY  (`BuildingId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        echo $query;

        $query  = "INSERT INTO " . $dbPrefix . "buildings(BuildingId, Name) VALUES ( ? , ? )";
        $database->prepare($query);

        foreach ($data as $building) {
            $database->bindAndExecute("is", $building["BuildingId"], $building["Name"]);
        }
        break;
    case "calendars":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "calendars";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "calendars ( ";
        $query .= "`CalendarId` int(11) NOT NULL, ";
        $query .= "`Date` date NOT NULL, ";
        $query .= "PRIMARY KEY  (`CalendarId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "calendars(CalendarId, Date) VALUES ( ? , ? )";
        $database->prepare($query);

        foreach ($data as $calendar) {
            $database->bindAndExecute("is", $calendar["CalendarId"], $calendar["Date"]);
        }
        break;
    case "rings":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "rings";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "rings ( ";
        $query .= "`RingId` int(11) NOT NULL, ";
        $query .= "`Time` TIME NOT NULL, ";
        $query .= "PRIMARY KEY  (`RingId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "rings(RingId, `Time`) VALUES ( ? , ? )";
        $database->prepare($query);

        foreach ($data as $ring) {
            $database->bindAndExecute("is", $ring["RingId"], $ring["Time"]);
        }
        break;
    case "students":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "students";
        echo $query . "<br />";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "students ( ";
        $query .= "`StudentId` int(11) NOT NULL, ";
        $query .= "`F` varchar(100) NOT NULL, ";
        $query .= "`I` varchar(100) NOT NULL, ";
        $query .= "`O` varchar(100) NOT NULL, ";
        $query .= "`ZachNumber` varchar(10) NOT NULL, ";
        $query .= "`BirthDate` Date NOT NULL, ";
        $query .= "`Address` varchar(300) NOT NULL, ";
        $query .= "`Phone` varchar(300) NOT NULL, ";
        $query .= "`Orders` varchar(300) NOT NULL, ";
        $query .= "`Starosta` BOOLEAN NOT NULL, ";
        $query .= "`NFactor` BOOLEAN NOT NULL, ";
        $query .= "`PaidEdu` BOOLEAN NOT NULL, ";
        $query .= "`Expelled` BOOLEAN NOT NULL, ";
        $query .= "PRIMARY KEY  (`StudentId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        echo $query . "<br />";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "students(StudentId, F, I, O, `ZachNumber`, `BirthDate`, ";
        $query .= "`Address`, `Phone`, `Orders`, `Starosta`, `NFactor`, `PaidEdu`, `Expelled` )";
        $query .= " VALUES ( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? )";
        echo $query . "<br />";
        $database->prepare($query);

        foreach ($data as $student) {
            $database->bindAndExecute("issssssssiiii",
                $student["StudentId"], $student["F"], $student["I"], $student["O"],
                $student["ZachNumber"], $student["BirthDate"], $student["Address"], $student["Phone"],
                $student["Orders"], $student["Starosta"], $student["NFactor"], $student["PaidEdu"],
                $student["Expelled"] );
        }
        break;
    case "studentGroups":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "studentGroups";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "studentGroups ( ";
        $query .= "`StudentGroupId` int(11) NOT NULL, ";
        $query .= "`Name` varchar(100) NOT NULL, ";
        $query .= "PRIMARY KEY  (`StudentGroupId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "studentGroups(StudentGroupId, Name) VALUES ( ? , ? )";
        $database->prepare($query);

        foreach ($data as $studentGroup) {
            $database->bindAndExecute("is", $studentGroup["StudentGroupId"], $studentGroup["Name"]);
        }
        break;
    case "teachers":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "teachers";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "teachers ( ";
        $query .= "`TeacherId` int(11) NOT NULL, ";
        $query .= "`FIO` varchar(300) NOT NULL, ";
        $query .= "`Phone` varchar(150) NOT NULL, ";
        $query .= "PRIMARY KEY  (`TeacherId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "teachers(TeacherId, FIO, Phone) VALUES ( ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $teacher) {
            $database->bindAndExecute("iss", $teacher["TeacherId"], $teacher["FIO"], $teacher["Phone"]);
        }
        break;
    case "disciplines":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "disciplines";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "disciplines ( ";
        $query .= "`DisciplineId` int(11) NOT NULL, ";
        $query .= "`Name` varchar(200) NOT NULL, ";
        $query .= "`Attestation` TINYINT NOT NULL, ";
        $query .= "`AuditoriumHours` SMALLINT NOT NULL, ";
        $query .= "`LectureHours` SMALLINT NOT NULL, ";
        $query .= "`PracticalHours` SMALLINT NOT NULL, ";
        $query .= "`StudentGroupId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`DisciplineId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "disciplines (DisciplineId, Name, Attestation, ";
        $query .= "`AuditoriumHours`, `LectureHours`, `PracticalHours`, `StudentGroupId`) ";
        $query .= "VALUES ( ? , ? , ? , ? , ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $discipline) {
            $database->bindAndExecute("isiiiii",
                $discipline["DisciplineId"], $discipline["Name"], $discipline["Attestation"],
                $discipline["AuditoriumHours"], $discipline["LectureHours"], $discipline["PracticalHours"],
                $discipline["StudentGroupId"]);
        }
        break;
    case "studentsInGroups":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "studentsInGroups";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "studentsInGroups ( ";
        $query .= "`StudentsInGroupsId` INT NOT NULL, ";
        $query .= "`StudentId` INT NOT NULL, ";
        $query .= "`StudentGroupId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`StudentsInGroupsId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "studentsInGroups(StudentsInGroupsId, StudentId, StudentGroupId) VALUES ( ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $sig) {
            $database->bindAndExecute("iii", $sig["StudentsInGroupsId"], $sig["StudentId"], $sig["StudentGroupId"]);
        }
        break;
    case "teacherForDisciplines":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "teacherForDisciplines";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "teacherForDisciplines ( ";
        $query .= "`TeacherForDisciplineId` INT NOT NULL, ";
        $query .= "`TeacherId` INT NOT NULL, ";
        $query .= "`DisciplineId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`TeacherForDisciplineId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "teacherForDisciplines(TeacherForDisciplineId, TeacherId, DisciplineId) VALUES ( ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $tfd) {
            $database->bindAndExecute("iii", $tfd["TeacherForDisciplineId"], $tfd["TeacherId"], $tfd["DisciplineId"]);
        }
        break;
    case "lessons":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "lessons";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "lessons ( ";
        $query .= "`LessonId` INT NOT NULL, ";
        $query .= "`IsActive` BOOLEAN NOT NULL, ";
        $query .= "`TeacherForDisciplineId` INT NOT NULL, ";
        $query .= "`CalendarId` INT NOT NULL, ";
        $query .= "`RingId` INT NOT NULL, ";
        $query .= "`AuditoriumId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`LessonId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "lessons(LessonId, IsActive, TeacherForDisciplineId, ";
        $query .= "`CalendarId`, `RingId`, `AuditoriumId`) VALUES ( ? , ? , ? , ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $lesson) {
            $database->bindAndExecute("iiiiii", $lesson["LessonId"], $lesson["IsActive"],
                $lesson["TeacherForDisciplineId"], $lesson["CalendarId"], $lesson["RingId"], $lesson["AuditoriumId"]);
        }
        break;
    case "configs":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "configs";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "configs ( ";
        $query .= "`ConfigOptionId` int(11) NOT NULL, ";
        $query .= "`Key` varchar(100) NOT NULL, ";
        $query .= "`Value` varchar(300) NOT NULL, ";
        $query .= "PRIMARY KEY  (`ConfigOptionId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "configs(ConfigOptionId, `Key`, `Value`) VALUES ( ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $configOption) {
            $database->bindAndExecute("iss", $configOption["ConfigOptionId"], $configOption["Key"], $configOption["Value"]);
        }
        break;
    case "lessonLogEvents":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "lessonLogEvents";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "lessonLogEvents ( ";
        $query .= "`LessonLogEventId` int(11) NOT NULL, ";
        $query .= "`OldLessonId` INT NOT NULL, ";
        $query .= "`NewLessonId` INT NOT NULL, ";
        $query .= "`DateTime` DATETIME NOT NULL, ";
        $query .= "`PublicComment` varchar(300) NOT NULL, ";
        $query .= "`HiddenComment` varchar(300) NOT NULL, ";
        $query .= "PRIMARY KEY  (`LessonLogEventId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "lessonLogEvents(LessonLogEventId, OldLessonId, NewLessonId, DateTime, ";
        $query .= "PublicComment, HiddenComment) VALUES ( ? , ? , ? , ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $lessonLogEvent) {
            $database->bindAndExecute("iiisss", $lessonLogEvent["LessonLogEventId"],
                $lessonLogEvent["OldLessonId"], $lessonLogEvent["NewLessonId"], $lessonLogEvent["DateTime"],
                $lessonLogEvent["PublicComment"], $lessonLogEvent["HiddenComment"]);
        }
        break;
    case "auditoriumEvents":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "auditoriumEvents";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "auditoriumEvents ( ";
        $query .= "`AuditoriumEventId` INT NOT NULL, ";
        $query .= "`Name` varchar(100) NOT NULL, ";
        $query .= "`CalendarId` INT NOT NULL, ";
        $query .= "`RingId` INT NOT NULL, ";
        $query .= "`AuditoriumId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`AuditoriumEventId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "auditoriumEvents(`AuditoriumEventId`, `Name`, `CalendarId`, ";
        $query .= "`RingId`, `AuditoriumId`) VALUES ( ? , ? , ? , ? , ? )";
        $database->prepare($query);



        foreach ($data as $AuditoriumEvent) {
            $database->bindAndExecute("isiii", $AuditoriumEvent["AuditoriumEventId"],
                $AuditoriumEvent["Name"], $AuditoriumEvent["CalendarId"],
                $AuditoriumEvent["RingId"], $AuditoriumEvent["AuditoriumId"]);
        }
        break;
    case "faculties":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "faculties";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "faculties ( ";
        $query .= "`FacultyId` INT NOT NULL, ";
        $query .= "`Name` varchar(100) NOT NULL, ";
        $query .= "`Letter` varchar(100) NOT NULL, ";
        $query .= "`SortingOrder` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`FacultyId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "faculties(FacultyId, Name, Letter, SortingOrder) VALUES ( ? , ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $faculty) {
            $database->bindAndExecute("issi",
                $faculty["FacultyId"], $faculty["Name"], $faculty["Letter"], $faculty["SortingOrder"]);
        }
        break;
    case "GroupsInFaculties":
        $query = "DROP TABLE IF EXISTS " . $dbPrefix . "GroupsInFaculties";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS " . $dbPrefix . "GroupsInFaculties ( ";
        $query .= "`GroupsInFacultyId` INT NOT NULL, ";
        $query .= "`StudentGroupId` INT NOT NULL, ";
        $query .= "`FacultyId` INT NOT NULL, ";
        $query .= "PRIMARY KEY  (`GroupsInFacultyId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $database->query($query);

        $query  = "INSERT INTO " . $dbPrefix . "GroupsInFaculties(GroupsInFacultyId, StudentGroupId, FacultyId) VALUES ( ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $gif) {
            $database->bindAndExecute("iii", $gif["GroupsInFacultyId"], $gif["StudentGroupId"], $gif["FacultyId"]);
        }
        break;
}

echo $tableSelector;

?>