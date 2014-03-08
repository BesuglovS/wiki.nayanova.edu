<?php
require_once("Database.php");
global $database;

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$tableSelector = $_POST["tableSelector"];
$data = json_decode($_POST["data"], true);

switch ($tableSelector) {
    case "exams":
        $query = "DROP TABLE IF EXISTS `exams`";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS `exams` ( ";
        $query .= "`ExamId` int(11) NOT NULL, ";
        $query .= "`DisciplineId` int(11) NOT NULL, ";
        $query .= "`IsActive` int(4) NOT NULL, ";
        $query .= "`ConsultationDateTime` varchar(50) NOT NULL, ";
        $query .= "`ConsultationAuditoriumId` int(11) NOT NULL, ";
        $query .= "`ExamDateTime` varchar(50) NOT NULL, ";
        $query .= "`ExamAuditoriumId` int(11) NOT NULL, ";
        $query .= "PRIMARY KEY  (`ExamId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $database->query($query);

        $query  = "INSERT INTO exams(ExamId, DisciplineId, IsActive, ";
        $query .= "ConsultationDateTime, ConsultationAuditoriumId, ExamDateTime, ExamAuditoriumId) ";
        $query .= " VALUES ( ? , ? , ? , ? , ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $exam) {
            $database->bindAndExecute("iiisisi",
                $exam["ExamId"],
                $exam["DisciplineId"],
                $exam["IsActive"],
                $exam["ConsultationDateTime"],
                $exam["ConsultationAuditoriumId"],
                $exam["ExamDateTime"],
                $exam["ExamAuditoriumId"]
            );
        }
        break;
    case "examsLogEvents":

        $query = "DROP TABLE IF EXISTS `examsLogEvents`";
        $database->query($query);

        $query  = "CREATE TABLE IF NOT EXISTS `examsLogEvents` ( ";
        $query .= "`LogEventId` int(11) NOT NULL, ";
        $query .= "`OldExamId` int(11) NOT NULL, ";
        $query .= "`NewExamId` int(11) NOT NULL, ";
        $query .= "`DateTime` varchar(50) NOT NULL, ";
        $query .= "PRIMARY KEY  (`LogEventId`)";
        $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $database->query($query);

        $query  = "INSERT INTO examsLogEvents(LogEventId, OldExamId, NewExamId, DateTime) ";
        $query .= " VALUES ( ? , ? , ? , ? )";
        $database->prepare($query);

        foreach ($data as $examLogEvent) {
            $database->bindAndExecute("iiis",
                $examLogEvent["LogEventId"],
                $examLogEvent["OldExamId"],
                $examLogEvent["NewExamId"],
                $examLogEvent["DateTime"]
            );
        }
        break;
}

echo $tableSelector;

?>