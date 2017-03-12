<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$list = array();

$i1 = array();
$i1["a"] = "a";
$i1["b"] = "2016-01-01";

$i2 = array();
$i2["a"] = "b";
$i2["b"] = "2016-02-01";


$i3 = array();
$i3["a"] = "c";
$i3["b"] = "2016-03-01";


$i4 = array();
$i4["a"] = "d";
$i4["b"] = "2016-04-01";


$list[] = $i4;
$list[] = $i3;
$list[] = $i2;
$list[] = $i1;

function cmp($a, $b)
{
    $aDate = DateTime::createFromFormat('Y-m-d', $a["b"]);
    $bDate = DateTime::createFromFormat('Y-m-d', $b["b"]);

    if ($aDate > $bDate) {
        $result = 1;
    } else {
        if ($aDate < $bDate) {
            $result = -1;
        } else {
            $result = 0;
        }

    }

//    echo "<pre>";
//    echo "aDate <br />";
//    echo print_r($aDate);
//    echo "</pre>";
//
//    echo "<pre>";
//    echo "aDate <br />";
//    echo print_r($bDate);
//    echo "</pre>";

    echo date_format($aDate, 'd.m.Y') . " " .
        date_format($bDate, 'd.m.Y') . " = " . $result . "<br />";

    return $result;
}

usort($list, "cmp");

echo "<pre>";
echo print_r($list);
echo "</pre>";