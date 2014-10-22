<?php
echo "<!doctype html> ";
echo "<html> ";
echo "<head> ";
echo "<meta charset=\"utf-8\"> ";
echo "<title>Диспетчерская учебного отдела СГОАН</title> ";
echo "<!--[if lt IE 9]> ";
echo "<script src=\"http://html5shiv.googlecode.com/svn/trunk/html5.js\"></script> ";
echo "<![endif]--> ";
echo "<!-- Google --> ";
echo "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js\"></script> ";
echo "<script src=\"//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js\"></script> ";
echo "<link rel=\"stylesheet\" href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.css\" type=\"text/css\" media=\"all\" /> ";
echo "<!-- Site's --> ";
echo "<!-- jquery.switchButton --> ";
echo "<script src=\"../upload/_js/jquery.switchButton.js\"></script> ";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../upload/_css/jquery.switchButton.css\"> ";
echo "<!-- Main --> ";
echo "<script src=\"upload/js/main.js\"></script> ";
echo "<script src=\"upload/js/logins.js\"></script> ";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../upload/_css/main.css\"> ";

echo "</head> ";
echo "<body> ";
echo "<div id=\"container\"> ";
echo "<header class=\"cf\"> ";
echo "<img src=\"../upload/images/DVZ.png\" id=\"headerLogo\" width=\"150\" height=\"150\"> ";
echo "<div id=\"weekDiv\"> ";
echo "Неделя<br /> ";
echo "<div id=\"weekNum\"> ";
$dbPrefix = "s_";
require_once("../_php/includes/ConfigOptions.php");
$now = date('Y-m-d');
$today = DateTime::createFromFormat('Y-m-d', $now);
$semesterStarts = $options["Semester Starts"];
$start = DateTime::createFromFormat('Y-m-d', $semesterStarts);
if ($today >= $start)
{
    $weekNum = (int)(floor(($today->diff($start)->format('%a')) / 7)) + 1;
}
else
{
    $weekNum = (int)floor(($today->diff($start)->format('%a') - 1) / 7) + 1;
    echo "-";
}
echo $weekNum;

echo "</div> ";
echo "</div> ";
echo "<h1>Диспетчерская учебного отдела СГОАН</h1> ";
echo "</header> ";

require_once("../_php/includes/Database.php");
$accountsQuery = "SELECT * FROM " . $dbPrefix ."LoginAccounts";
$result = $database->query($accountsQuery);

echo "&nbsp;<br />";

echo "<div style='margin-left: 1em'><button type=\"button\" id=\"addAccount\">Добавить аккаунт</button></div>";

echo "<table class=\"redHeadWhiteBodyTable\" style='width: 95%; margin: 1em auto'> ";
echo "<tr>";
echo "<td>Логин</td>";
echo "<td>Δ</td>";
echo "<td>-</td>";
echo "</tr>";
while ($account = $result->fetch_assoc())
{
    echo "<tr>";
    echo "<td>" . $account["Login"] . "</td>";
    echo "<td>" . "<input type=\"text\" id=\"" . $account["Login"] . "\">";
    echo "<button type=\"button\" onclick='ChangePassword(\"" . $account["Login"] . "\");'>Изменить пароль</button>";
    echo "</td>";
    echo "<td>";
    echo "<button type=\"button\" onclick='RemoveAccount(\"" . $account["Login"] . "\");'>Удалить аккаунт</button>";
    echo "</td>";

    echo "</tr>";
}
echo "</table>";
echo "<footer style='margin-top: 1em'> ";
echo "<p> ";
echo "&copy; Диспетчерская учебного отдела СГОАН, " . date("Y");
echo "</p> ";
echo "</footer> ";

echo "<div id=\"dialog-form\" title=\"Добавить аккаунт\"> ";
echo "  <form> ";
echo "    <fieldset> ";
echo "      <label for=\"name\">Логин</label> ";
echo "      <input type=\"text\" name=\"name\" id=\"name\" value=\"\" class=\"text ui-widget-content ui-corner-all\"> ";
echo "      <label for=\"password\">Пароль</label> ";
echo "      <input type=\"password\" name=\"password\" id=\"password\" value=\"\" class=\"text ui-widget-content ui-corner-all\"> ";

echo "      <input type=\"submit\" tabindex=\"-1\" style=\"position:absolute; top:-1000px\"> ";
echo "</fieldset> ";
echo "</form> ";
echo "</div> ";
echo "</body> ";
echo "</html> ";