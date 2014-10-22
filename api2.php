<?php
header("Content-type: text/html; charset=utf-8");
require_once("_php/includes/API/apiCore.php");

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

var_dump($rest_json);
var_dump($_POST);
exit;

$POST = $_POST["Parameters"];
global $api;

if ((count($POST) == 0) && (count($_GET)== 0))
{
    echo $api->WelcomeMessage();
    exit;
}

if ((count($_GET)!= 0))
{
    echo $api->PlainError("Параметры должны передаватся в POST запросе.");
    exit;
}
if(!isset($POST['action']) )
{
    echo $api->APIError("action - обязательный параметр при запросе.");
    exit;
}

echo $api->ExecuteAction($POST);

?>