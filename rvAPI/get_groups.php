<?php
header("Content-type: text/html; charset=utf-8");
require_once($_SERVER["DOCUMENT_ROOT"] . "/rvAPI/rvAPICore.php");

global $NewAPI;

if(isset($_POST['faculty_id']))
{
    $faculty_id = $_POST['faculty_id'];
}

if(isset($_GET['faculty_id']))
{
    $faculty_id = $_GET['faculty_id'];
}

echo $NewAPI->get_groups($faculty_id);

?>