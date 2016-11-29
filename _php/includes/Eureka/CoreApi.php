<?php
include $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php";
include $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Utilities.php";

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

        switch ($action) {
            case "RunLog":
                $this->InsertRunLog();
                break;
        }
    }

    private function InsertRunLog()
    {
        $dateTime = Utilities::MySQLDateTime();

        $query  = "INSERT INTO " . $this->dbPrefix . "RunLog ";
        $query .= "( DateTime ) ";
        $query .= "Values (\"";
        $query .= $dateTime;
        $query .= "\") ";

        $this->database->query($query);
    }
}

