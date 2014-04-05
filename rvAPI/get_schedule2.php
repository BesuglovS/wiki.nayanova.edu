<?php
header("Content-type: text/html; charset=utf-8");
require_once($_SERVER["DOCUMENT_ROOT"] . "/rvAPI/rvAPICore.php");

global $NewAPI;

if(isset($_POST['group_id']))
{
    $group_id = $_POST['group_id'];
}

if(isset($_GET['group_id']))
{
    $group_id = $_GET['group_id'];
}

echo "<pre>";
echo print_r($NewAPI->get_schedule_raw($group_id));
echo "</pre>";

?>