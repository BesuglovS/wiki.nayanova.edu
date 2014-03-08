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
echo $NewAPI->get_schedule22($group_id);


?>