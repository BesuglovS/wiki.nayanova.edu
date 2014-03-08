<?php
header("Content-type: text/html; charset=utf-8");
require_once("Database.php");

global $database;

class Stats {

    public  $database;
    private $indexArray;
    private $eventRangeStarts;
    private $eventRangeEnds;

    public function __construct($db){
        $this->database = $db;
    }

    public function StatList(){
        $output = "";


        $query  = "SELECT * ";
        $query .= "FROM DailyScheduleStats ";
        $query .= "ORDER BY statDate DESC ";

        $queryResult = $this->database->query($query);


        $counter = 0;
        $indexArray = array();
        $eventRangeStarts = array();
        $eventRangeEnds = array();
        while(($event = $queryResult->fetch_assoc()) != null)
        {
            if($counter % 100 == 0)
            {
                $eventRangeStarts[] = $event["statDate"];
                $indexArray[] = $counter;
            }

            $eventCount = $queryResult->num_rows;

            if (($counter % 100 == 99) || ($counter == $eventCount-1))
            {
                $eventRangeEnds[] = $event["statDate"];
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

    public function commonStats(){
        $output = "";

        $query  = "SELECT groupId, count( * ) AS col ";
        $query .= "FROM `DailyScheduleStats` ";
        $query .= "GROUP BY groupId ";
        $query .= "ORDER BY col DESC, groupId ";

        $groupList = $this->database->query($query);

        if ($groupList->num_rows != 0)
        {
            $data = array();

            while($group = $groupList->fetch_assoc())
            {

                $data[$group["groupId"]] = array();

                $data[$group["groupId"]]["col"] = $group["col"];

                $groupNumQuery  = "SELECT COUNT(*) ";
                $groupNumQuery .= "FROM studentsInGroups ";
                $groupNumQuery .= "JOIN students ";
                $groupNumQuery .= "ON studentsInGroups.StudentId = students.StudentId ";
                $groupNumQuery .= "JOIN studentGroups ";
                $groupNumQuery .= "ON studentGroups.StudentGroupId = studentsInGroups.StudentGroupId ";
                $groupNumQuery .= "WHERE ((studentGroups.Name = \"" . $group["groupId"] . "\") AND (students.Expelled != 1)) ";

                $groupNumResult = $this->database->query($groupNumQuery);
                $groupNumArray = $groupNumResult->fetch_assoc();
                $groupNum = $groupNumArray["COUNT(*)"];

                $data[$group["groupId"]]["groupNum"] = $groupNum;
                $data[$group["groupId"]]["colByNum"] = $group["col"] / $groupNum;
            }

            /*
            usort($data, function($a, $b){
                if ($a["col"] > $b["col"])
                {
                    return -1;
                }
                if ($a["col"] < $b["col"])
                {
                    return 1;
                }

                return 0;
            });
            */

            echo "<table id=\"statsCommonTable\" class=\"redHeadWhiteBodyTable\" style =\"width:90%; margin: 0 auto;\">";
            echo "<tr>";
            echo "<td style=\"text-align:center\">";
            echo "Название группы";
            echo "</td>";
            echo "<td>";
            echo "Количество обращений";
            echo "</td>";
            echo "<td>";
            echo "Человек в группе";
            echo "</td>";
            echo "<td>";
            echo "Обращений на человека";
            echo "</td>";
            echo "</tr>";
            //while($group = $groupList->fetch_assoc())
            foreach ($data as $groupId => $groupData)
            {
                echo "<tr>";
                echo "<td>";
                echo $groupId;
                echo "</td>";
                echo "<td>";
                echo $groupData["col"];
                echo "</td>";
                echo "<td>";
                echo $groupData["groupNum"];
                echo "</td>";
                echo "<td>";
                echo number_format($groupData["colByNum"],3);
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        else
        {
            echo Utilities::NothingISThereString();
        }

        return $output;

    }
}

$stats = new Stats($database);

?>