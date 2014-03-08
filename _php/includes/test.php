<?php
   // set timezone to user timezone



date_default_timezone_set('Europe/Moscow');
$unixDate = mktime (date("H")+1 ,date("i"), date("s"), date("n"), date("j"), date("Y"));
$date = new DateTime();
$date->setTimestamp($unixDate);
$dateString = $date->format("d.m.Y H:i");
$day = $dateString[1];
echo $day;
