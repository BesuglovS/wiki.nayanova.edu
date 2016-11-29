<?php

require_once( $_SERVER["DOCUMENT_ROOT"] . "/_php/includes/Database.php");

//global $database;

$query = "SELECT StudentId, F, I, O, ZachNumber, BirthDate FROM students WHERE Expelled=0";

$qResult = $database->query($query);

$dayMonth = date("d") . date("m");
if (isset($_GET["date"])){
	$dayMonth = $_GET["date"];
}

$result = array();
$result["students"] = array();

while($student = $qResult->fetch_assoc())
{
    $studDayMonth = substr($student["BirthDate"], 8, 2) . substr($student["BirthDate"], 5,2);

    if ($studDayMonth == $dayMonth)
    {
		
		$groupQuery  = "SELECT Name ";
		$groupQuery .= "FROM `studentsInGroups` ";
		$groupQuery .= "JOIN studentGroups ";
		$groupQuery .= "ON studentsInGroups.StudentGroupId = studentGroups.StudentGroupId ";
		$groupQuery .= "WHERE `StudentId` = " . $student["StudentId"];
		
		$gqResult = $database->query($groupQuery);
		
		$groups = array();		
		while($group = $gqResult->fetch_assoc())
		{
			$groupName = $group["Name"];
			
			if (
			(strpos($groupName, "-") !== false) || 
			(strpos($groupName, "|") !== false) ||
			(strpos($groupName, "+") !== false))
			{
				// Bad groupname				
			} else {
				$groups[] = $groupName;
			}			
		}
		
		if (count($groups) > 0) {
			$student["group"] = $groups[0];
		}
		else {
			$student["group"] = "";
		}
		
		if ($student["group"] !== "")
		{
			$st = array();
			$st["group"] = $student["group"];
			$st["FIO"] = $student["F"] . " " . $student["I"] . " " . $student["O"];			
			$result["students"][] = $st;			
		}
    }
}
// echo "<pre>";
// echo print_r($result);
// echo "</pre>";
// exit;

function sortStudentsByGroupName($a, $b)
{
    return strcmp($a["group"], $b["group"]);
}

usort($result["students"], "sortStudentsByGroupName");


if (count($groups) > 0)
{
	echo "<div id=\"Happy\">" . "\n";
	echo "<table>" . "\n";
	echo "<tr>" . "\n";
	echo "<td><img src=\"upload/images/cake.png\"></td>" . "\n";	
	for ($i = 0; $i < count($result["students"]); $i++) {
		echo "<td title=\"" . $result["students"][$i]["FIO"] . "\">" . $result["students"][$i]["group"] . "</td>"  . "\n";
	} 
	echo "</tr>" . "\n";
	echo "</table>" . "\n";	
	echo "</div>" . "\n";
}

