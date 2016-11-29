<?php

$data = "(\"[{\"color\":\"w\",\"from\":\"f2\",\"to\":\"f4\",\"flags\":\"b\",\"piece\":\"p\",\"san\":\"f4\"},{\"color\":\"b\",\"from\":\"e7\",\"to\":\"e5\",\"flags\":\"b\",\"piece\":\"p\",\"san\":\"e5\"},{\"color\":\"w\",\"from\":\"g2\",\"to\":\"g4\",\"flags\":\"b\",\"piece\":\"p\",\"san\":\"g4\"},{\"color\":\"b\",\"from\":\"d8\",\"to\":\"h4\",\"flags\":\"n\",\"piece\":\"q\",\"san\":\"Qh4#\"}]\")";

$mysqli = new mysqli("u461885.mysql.masterhost.ru", "u461885", "cosT7Dgice.se", "u461885");
$mysqli->set_charset("utf8");
echo $mysqli->real_escape_string($data);