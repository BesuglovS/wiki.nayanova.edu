<?php
header("Content-type: text/html; charset=utf-8");
require_once($_SERVER["DOCUMENT_ROOT"] . "/rvAPI/rvAPICore.php");

global $NewAPI;

echo $NewAPI->get_faculties();

?>