<?php
header("Content-type: text/html; charset=utf-8");
require_once("_php/includes/Eureka/CoreApi.php");

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$POST = $_POST["Parameters"];

if ((count($_GET)!= 0))
{
    //echo $api->PlainError("Параметры должны передаватся в POST запросе.");
    //exit;
    $POST = $_GET;
}

$api = new api($database, "Eureka_");

if ((count($POST) == 0) && (count($_GET)== 0))
{
    echo $api->WelcomeMessage();
    exit;
}

if(!isset($POST['action']) )
{
    echo $api->APIError("action - обязательный параметр при запросе.");
    exit;
}

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

echo $api->ExecuteAction($POST);
?>