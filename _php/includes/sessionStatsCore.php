<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");

global $database;

class SessionStats {

    public  $database;


    public function __construct($db){
        $this->database = $db;
    }

    public function StatList(){
        $output = "";

        $query  = "SELECT DateTime, GroupId ";
        $query .= "FROM " . $dbPrefix . "sessionStats ";
        $query .= "ORDER BY DateTime DESC ";

        $queryResult = $this->database->query($query);


        $counter = 0;
        $indexArray = array();
        $eventRangeStarts = array();
        $eventRangeEnds = array();
        while(($event = $queryResult->fetch_assoc()) != null)
        {
            if($counter % 100 == 0)
            {
                $eventRangeStarts[] = $event["DateTime"];
                $indexArray[] = $counter;
            }

            $eventCount = $queryResult->num_rows;

            if (($counter % 100 == 99) || ($counter == $eventCount-1))
            {
                $eventRangeEnds[] = $event["DateTime"];
            }

            $counter++;
        }


        $output .= "<select id=\"statEventsIndexList\">";
        for($i = 0; $i < count($eventRangeStarts); $i++)
        {
            $start = $eventRangeStarts[$i];
            $end = $eventRangeEnds[$i];

            $output .= "<option value=\"";
            $output .= $indexArray[$i];
            $output .= "\">";
            $output .= $start . " - " . $end;
            $output .= "</option>";
        }
        $output .= "</select>";

        $output .= "<span id=\"progress\"></span>";

        $output .= "<br /><br />";
        $output .= "<div id=\"statEventList\"></div>";


        return $output;
    }

}

$sessionStats = new SessionStats($database);

?>