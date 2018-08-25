<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();

$login_tables_prefix = "";

if($_POST['logout'] == "1")
{
    session_destroy();
    session_start();
}

$NUlogin = "";
$NUpassword = "";

if (isset($_POST['NUlogin']))
{
    $_SESSION['NUlogin'] = $_POST['NUlogin'];
}
if (isset($_POST['NUpassword']))
{
    $_SESSION['NUpassword'] = $_POST['NUpassword'];
}

require_once("_php/includes/Database.php");

require_once("_php/includes/ZachDates.php");

function ReformatDate($semesterStarts)
{
    $semesterStartsCorrectFormat =
        mb_substr($semesterStarts, 8, 2) . "." .
        mb_substr($semesterStarts, 5, 2) . "." .
        mb_substr($semesterStarts, 0, 4);
    return $semesterStartsCorrectFormat;
}

$studentsQuery  = "SELECT F, I, O, BirthDate ";
$studentsQuery .= "FROM " . $login_tables_prefix . "students ";
$studentsQuery .= "WHERE Expelled = 0 ";

$students = $database->query($studentsQuery);
$accounts = array();
while ($student = $students->fetch_assoc())
{
    $account = array();

    $I = (mb_strlen($student["I"]) > 0) ? mb_substr($student["I"], 0, 1): "";
    $O = (mb_strlen($student["O"]) > 0) ? mb_substr($student["O"], 0, 1): "";

    $accounts[$student["F"] . " " . $I . $O] = ReformatDate($student["BirthDate"]);
}

$isStudent = 0;

if (array_key_exists($_SESSION['NUlogin'], $accounts))
{
    $isStudent = 1;
}


$extraAccountsQuery  = "SELECT Login, Password ";
$extraAccountsQuery .= "FROM LoginAccounts ";

$extraAccounts = $database->query($extraAccountsQuery);

while($extAccount = $extraAccounts->fetch_assoc())
{
    $accounts[$extAccount["Login"]] = $extAccount["Password"];
}

$FromNU = False;
if (($_SERVER['REMOTE_ADDR'] == "95.167.125.206") || ($_SERVER['REMOTE_ADDR'] == "85.236.163.58"))
{
    $FromNU = True;    
}



if((((!isset($_SESSION['NUlogin']) || !isset($_SESSION['NUpassword']))) ||
   (!(array_key_exists($_SESSION['NUlogin'], $accounts) &&
    $accounts[$_SESSION['NUlogin']] === $_SESSION['NUpassword'])))
  &&
  ($FromNU == False))
{
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
    echo "<script src=\"upload/_js/jquery.switchButton.js\"></script> ";
	//echo "<script src=\"upload/_js/EvoCanvas.js\"></script> ";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"upload/_css/jquery.switchButton.css\"> ";
    echo "<!-- Main --> ";
    echo "<script src=\"upload/_js/main.js\"></script> ";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"upload/_css/main.css\"> ";
    echo "<!-- vk.com --> ";
    echo "<script type=\"text/javascript\" src=\"//vk.com/js/api/openapi.js?115\"></script>";
    echo "<script type=\"text/javascript\"> ";
    echo "VK.init({apiId: 4638017, onlyWidgets: true}); ";
    echo "</script> ";

    echo "</head> ";
    echo "<body> ";    
    echo "<div id=\"container\"> ";
    echo "<header class=\"cf\"> ";
        echo "<img src=\"upload/images/DVZ.png\" id=\"headerLogo\" width=\"150\" height=\"150\"> ";
        echo "<div id=\"weekDiv\"> ";
            echo "Неделя<br /> ";
            echo "<div id=\"weekNum\"> ";

                require_once("_php/includes/ConfigOptions.php");
				include $_SERVER["DOCUMENT_ROOT"] . "/php/Utilities.php";
                $now = date('Y-m-d');
                $semesterStarts = $options["Semester Starts"];                
				$weekNum = Utilities::WeekFromDate($now, $semesterStarts);
                echo $weekNum;

            echo "</div> ";
        echo "</div> ";
        echo "<h1>Диспетчерская учебного отдела СГОАН</h1> ";
    echo "</header> ";

    echo "<section id=\"content\" class=\"cf\"> ";
    echo "<div id=\"loginButtonDiv\"> ";
    echo "<form name='input' action='{$_SERVER['PHP_SELF']}' method='post'> ";
    echo "<table id=\"submittable\"> ";

    echo "<tr>";
    echo "<td style='text-align: left; vertical-align: middle'> Пользователь";
    echo "</td>";
    echo "<td style='vertical-align: middle'> <input type='text' value='$NUlogin' id='NUlogin' name='NUlogin'/>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td style='text-align: left; vertical-align: middle'> Пароль";
    echo "</td>";
    echo "<td style='vertical-align: middle'> <input type='password' value='$NUpassword' id='NUpassword' name='NUpassword'/>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2'> <input type=\"submit\" value=\"Войти\" style='width: 100%' />";
    echo "</td>";
    echo "</tr>";
    echo "</table> ";
    echo "<div style='font-size:xx-small; text-align:justify; margin-left: 1em; margin-right: 1em'>";
    echo "Имя пользователя в формате: Фамилия, пробел, Заглавная первая буква имени, Заглавная первая буква отчества";
    echo "<br /> Например: Иванов ИИ";
    echo "<br />";
    echo "В поле пароля введите дату рождения в формате: День (2 цифры), точка, месяц (2 цифры), точка, год (4 цифры)";
    echo "<br /> Например: 04.04.1985</div>";
	/*
	echo "login:" . $_SESSION['NUlogin'] . "<br />";
	echo "pass:" . $_SESSION['NUpassword'] . "<br />";
	echo "accounts";
	echo "<pre>";
	echo print_r($accounts);
	echo "</pre>";
	*/
    echo "</form> ";
    echo "</div>";
    echo "</section> ";
    /*
    echo "<table>";
    echo "<tr>";
    echo "<td>";
    echo "<div id=\"vk_comments\"></div> ";
    echo "<script type=\"text/javascript\"> ";
    echo "VK.Widgets.Comments(\"vk_comments\", {limit: 5, width: \"310\", attach: \"*\"}); ";
    echo "</script> ";
    echo "</td>";
    echo "<td>";
    echo "<!-- VK Widget --> ";
    echo "<div id=\"vk_groups\"></div> ";
    echo "<script type=\"text/javascript\"> ";
    echo "VK.Widgets.Group(\"vk_groups\", {mode: 1, width: \"310\", height: \"148\", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 2691142); ";
    echo "</script> ";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    */
    echo "<footer> ";
        echo "<p> ";
            echo "&copy; Диспетчерская учебного отдела СГОАН, " . date("Y");
        echo "</p> ";
    echo "</footer> ";
    echo "</div><!-- end .container --> ";
    echo "</body> ";
    echo "</html> ";
    exit;
}

function ReformatDateToMySQL($date)
{
    // 0123456789
    // 25.10.1995 => 1995-10-25
    $semesterStartsCorrectFormat =
        mb_substr($date, 6, 4) . "-" .
        mb_substr($date, 3, 2) . "-" .
        mb_substr($date, 0, 2);
    return $semesterStartsCorrectFormat;
}

$todayStamp  = mktime(date("G")+4, date("i"), date("s"), date("m"), date("d"), date("Y"));
$today = gmdate("y.m.d H:i:s", $todayStamp);

$FIO = explode(' ',trim($_SESSION['NUlogin']));
$F = $FIO[0];
$MySQLDate = ReformatDateToMySQL($_SESSION['NUpassword']);
$studentIdQuery  = "SELECT StudentId ";
$studentIdQuery .= "FROM students ";
$studentIdQuery .= "WHERE F = '" . $F . "' ";
$studentIdQuery .= "AND BirthDate = '" . $MySQLDate . "' ";
$studentIdQuery .= "AND Expelled = 0 ";

$studentResult = $database->query($studentIdQuery);
$studentIdArray = $studentResult->fetch_assoc();
$userId = $studentIdArray["StudentId"];
$sessionStudentId = "";

if ($userId != "")
{
    $_SESSION['studentId'] = $userId;
    $sessionStudentId = $userId;
}

if ($isStudent == 0)
{
    $altUserId = $_SESSION['NUlogin'];
    $_SESSION['AltUserId'] = $altUserId;
}

if ($FromNU)
{
    $altUserId = "FROM NU";
    $_SESSION['AltUserId'] = $altUserId;
}



$statQuery  = "INSERT INTO LoginLog ";
$statQuery .= "(UserId, DateTime, RemoteAddr, UserAgent, AltUserId) ";
$statQuery .= "VALUES (\"" . $userId . "\", \"" .
    $today . "\", \"" .
    $_SERVER['REMOTE_ADDR'] . "\" , \"" .
    $_SERVER['HTTP_USER_AGENT'] . "\" , \"" .
    $altUserId  ."\")";


$database->query($statQuery);

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="/favicon.ico" />
    <title>Диспетчерская учебного отдела СГОАН</title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Google -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />
    <!-- Site's -->
    <!-- jquery.switchButton -->
    <script src="upload/_js/jquery.switchButton.js"></script>
    <link rel="stylesheet" type="text/css" href="upload/_css/jquery.switchButton.css">

    <!--Countdown -->
    <link rel="stylesheet" type="text/css" href="upload/_css/jquery.countdown.css">
    <script src="upload/_js/jquery.plugin.min.js"></script>
    <script src="upload/_js/jquery.countdown.min.js"></script>
    <script src="upload/_js/jquery.countdown-ru.js"></script>
	<!--<script src="upload/_js/EvoCanvas.js"></script> -->

    <!-- Bootstrap
    <link rel="stylesheet" type="text/css" href="upload/bootstrap/css/bootstrap.min.css">
    <script src="upload/bootstrap/js/bootstrap.min.js"></script>-->
    <!-- Main -->
    <script src="upload/_js/main.js"></script>
    <link rel="stylesheet" type="text/css" href="upload/_css/main.css">
    <?php
    if ($FromNU) {
        echo "<script type=\"text/javascript\"> var FromNU = 1; </script>";
    }
    ?>
    <!-- vk.com -->

    <script type="text/javascript">
        var quotes;
        $.get("upload/quotes.txt", function(data) {
            quotes = data.split('\n');

            var quote = quotes[Math.floor(Math.random()*quotes.length)];
            $(function() {
                $("#leftHeader").text(quote);
            });
        });
    </script>

</head>
<body>
<div id="container">
<header id="loginHeader">
    <p style="float:left; font-size: 10px" id="leftHeader"></p>
    <p>
        <?php
            if (!$FromNU)
            {
                echo $_SESSION['NUlogin'] . " | <a id=\"logoutLink\" href=\"\">Выйти</a>";
            }
        ?>
    </p>
</header>
<header class="cf">
    <img src="upload/images/DVZ.png" id="headerLogo" width="150" height="150">
    <div id="weekDiv">
        Неделя<br />
        <div id="weekNum">
            <?php
            
            	require_once("_php/includes/ConfigOptions.php");
				include $_SERVER["DOCUMENT_ROOT"] . "/php/Utilities.php";
                $now = date('Y-m-d');
                $semesterStarts = $options["Semester Starts"];                
				$weekNum = Utilities::WeekFromDate($now, $semesterStarts);
                echo $weekNum;
				
            ?>
        </div>
    </div>
    <h1>Диспетчерская учебного отдела СГОАН</h1>
</header>
<section id="content" class="cf">
<section id="groupsSchedule">
    <div id="dateScheduleControls">
        <h2>Расписание преподавателя</h2>
        <?php
        global $database;
        $teacherList = $database->query("SELECT * FROM `teachers`");
        $tList = array();
        while ($teacher = $teacherList->fetch_assoc())
        {
            $tList[$teacher["TeacherId"]] = $teacher["FIO"];
        }
        asort($tList);
        ?>
        <select id="teacherList">
            <?php
            foreach ($tList as $id => $FIO)
            {
                echo '<option value="';
                echo $id;
                echo '">';
                echo $FIO;
                echo '</option>';
            }
            ?>
        </select>
        <p>
            <button id="showTeachersSchedule" type="button">Показать</button>
        </p>
        <h2 id="groupsScheduleHeader">Расписание по группам</h2>
        <p>
            <button id="studentGroups" type="button">Списки групп</button>
        </p>
        <p>
            <button id="planGroups" type="button">Дисциплины по группам</button>
        </p>
        <p>
            <button id="planByTeacher" type="button">Дисциплины по преподавателям</button>
        </p>
        <?php
            if (array_key_exists("studentId", $_SESSION))
            {
                echo "<p>";
                echo "<div id=\"mySchedule\">";
                echo "МОЁ РАСПИСАНИЕ НА<br />";
                echo "<button id=\"todaySchedule\">Сегодня</button>";
                echo "<button id=\"tomorrowSchedule\">Завтра</button>";
                echo "<button id=\"MyMyMySchedule\">Дату</button>";
                echo "</div>";
                echo "</p>";
            }
        ?>
        <p>
            <button id="today">Сегодня</button>
            <button id="tomorrow">Завтра</button>
        </p>
        <p>
            <input type="text" id="scheduleDate">
        </p>
        <p>
            <button id="Mol">Корп № 2</button>
            <button id="Jar">Корп № 3</button>            
            <button id="Other">Прочие</button>
        </p>
        <p>
            <button id="MolPlus">Корп № 2+</button>
            <button id="JarPlus">Корп № 3+</button>
        </p>
    </div>
    <div id="flipWraper">
        <div id="scheduleOrChangesDiv">
            <input type="checkbox" name="scheduleOrChanges" id="scheduleOrChanges">
        </div>

        <div id="DayOrWeekDiv">
            <input type="checkbox" name="DayOrWeek" id="DayOrWeek">
        </div>
    </div>
    <br />
    <table id="dateScheduleTable">
        <tbody>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td> </td>
            <td><button id="15Math">15 А</button></td>
            <td>&nbsp;</td>
            <td> </td>
        </tr>
        <tr>
            <td><button id="12Phil">12 Б</button></td>
            <td> </td>
            <td><button id="14Phil">14 Б</button></td>
            <td><button id="15Phil">15 Б</button></td>
            <td><button id="16Phil">16 Б</button></td>
            <td><button id="17Phil">17 Б</button></td>
        </tr>
        <tr>
            <td><button id="12Eco">12 В</button></td>
            <td>&nbsp;</td>
            <td> </td>
            <td><button id="15Eco">15 В</button></td>
            <td>&nbsp;</td>
            <td> </td>
        </tr>
        <tr>
            <td><button id="12Econ">12 Г</button></td>
            <td>&nbsp;</td>
            <td><button id="14Econ">14 Г</button></td>
            <td><button id="15Econ">15 Г</button></td>
            <td><button id="16Econ">16 Г</button></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><button id="12Law">12 Д</button></td>
            <td><button id="13Law">13 Д</button></td>
            <td> </td>
            <td><button id="15Law">15 Д</button></td>
            <td><button id="16Law">16 Д</button></td>
            <td><button id="17Law">17 Д</button></td>
        </tr>
        <tr>
            <td><button id="12PR">12 Е</button></td>
            <td><button id="13PR">13 Е</button></td>
            <td> </td>
            <td><button id="15PR">15 Е</button></td>
            <td>&nbsp;</td>
            <td> </td>
        </tr>        
        <tr>
            <td><button id="12Upr1">12 У1</button></td>
            <td><button id="12Upr2">12 У2</button></td>
            <td> </td>
            <td><button id="15Upr">15 У</button></td>
            <td>&nbsp;</td>
            <td> </td>
        </tr>
        <tr>
            <td><button id="12Tur">12 Т</button></td>
            <td><button id="13Tur">13 Т</button></td>
            <td><button id="14Tur">14 Т</button></td>
            <td><button id="15Tur">15 Т</button></td>
            <td>&nbsp;</td>
            <td> </td>
        </tr>        
        <tr>
            <td><button id="1AMath">1 АА</button></td>
            <td> </td>
            <td><button id="3AMath">3 АА</button></td>
            <td><button id="4AMath">4 АА</button></td>
        </tr>
        <tr>
            <td><button id="1APhil">1 АБ</button></td>
            <td> </td>
            <td><button id="3APhil">3 АБ</button></td>
        </tr>
        <tr>
            <td><button id="1AEco">1 АВ</button></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><button id="4AEco">4 АВ</button></td>
        </tr>
        <tr>
            <td><button id="1AEcon">1 АГ</button></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><button id="1ALaw">1 АД</button></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>        
        </tbody>
    </table>
</section>
<section id="vk">
	<?php require_once("_php/API/Happy.php");?>
    <table style="width: 267px; margin:0 auto; margin-bottom: 0.5em">
        <tr>
            <td>
                <p>
                    День недели
                </p>
            </td>
            <td style="width: 10px">
            </td>
            <td>
                <p>
                    <select id="dowPDFSelect">
                        <option value="1">Понедельник</option>
                        <option value="2">Вторник</option>
                        <option value="3">Среда</option>
                        <option value="4">Четверг</option>
                        <option value="5">Пятница</option>
                        <option value="6">Суббота</option>
                        <option value="7">Воскресенье</option>
                    </select>
                </p>
            </td>
        </tr>
        <tr style="height: 10px">
        </tr>
        <tr>
            <td style="vertical-align: middle">
                <p>
                    Факультет
                </p>
            </td>
            <td style="width: 10px">
            </td>
            <td>
                <p>
                    <?php
                    global $database;
                    $facultiesList = $database->query("SELECT * FROM `faculties`");
                    $fList = array();
                    while ($faculty = $facultiesList->fetch_assoc())
                    {
                        $fList[$faculty["FacultyId"]] = $faculty["Name"];
                    }
                    ?>
                    <select id="facultiesList" style="width:150px">
                        <?php
                        foreach ($fList as $id => $Name)
                        {
                            echo '<option value="';
                            echo $id;
                            echo '">';
                            echo $Name;
                            echo '</option>';
                        }
                        ?>
                    </select>
                </p>
            </td>
        </tr>
    </table>


    <p style="text-align: center; margin-bottom: 0.5em">
        <button id="DOWSchedule">Показать</button>

        <button id="PDFExport">Расписание в PDF</button>
    </p>

    <section id="vk">
        <div id="sessiusImageDiv">
            <!--<img src="upload/images/sessius-sdavamus.jpg" id="sessiussdavamus" width="221" height="215"> -->
            <img src="upload/images/rocket.gif" style="border-radius: 150px" width="150px" >
			<!--<canvas style="display: inline" id="evoCanvas" width="220" height="220"></canvas>-->
            <?php

            if ($sessionStudentId != "")
            {
                $dates = new ZachDates();
                echo $dates->GetNearestZach("", $sessionStudentId);
            }
            ?>            
            <!--<img src="upload/images/fail.jpg" style="margin:auto; border-radius: 10px; border: solid 3px black;" width="290px" >-->
            <!--<div style="text-align: center">До лета осталось:</div>-->
            <!--<div id="summer" style="height: 50px"></div>-->          
            <!--<img id="rightSideImage" src="upload/images/maslenitza.jpg" width="183" height="300">-->
        </div>

		<!--
        <div id="peresdachiContainer">
        	Расписание пересдач <br />
        	<table border="1" id="peresdachi">
        		<tr>
        			<td><a href="upload/Peresdachi/1Math.docx">А</a></td>        			
        			<td><a href="upload/Peresdachi/2Phil.docx">Б</a></td>
        			<td><a href="upload/Peresdachi/3Bio.docx">В</a></td>
        			<td><a href="upload/Peresdachi/4Econ.docx">Г</a></td>
        			<td><a href="upload/Peresdachi/5Law.docx">Д</a></td>
        			<td><a href="upload/Peresdachi/6PR.docx">Е</a></td>
        			<td><a href="upload/Peresdachi/7Upr.docx">У</a></td>
        			<td><a href="upload/Peresdachi/8Tur.docx">Т</a></td>
        		</tr>        	
        	</table>
        </div>
		-->
        
        <!-- <script type="text/javascript" src="//vk.com/js/api/openapi.js?98"></script> -->
        <!-- VK Widget -->
        <!--
        <div id="vk_groups" style="text-align:center"></div>
        <script type="text/javascript">
            VK.Widgets.Group("vk_groups", {mode: 2, width: "285", height: "300"}, 2691142);
        </script>
        -->

		
		
		<h2 id="sessionScheduleHeader">Расписание сессии</h2>

        <div id="scheduleOrChangesSessionDiv">
            <input type="checkbox" name="scheduleOrChangesSession" id="scheduleOrChangesSession">
        </div>

        <p id="sessionByDateP">
            <button id="sessionByDate">Сессия по датам</button>
        </p>

        <table id="SessionScheduleTable">
            <tbody>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td> </td>
                <td><button id="15Math2">15 А</button></td>
                <td>&nbsp;</td>
                <td> </td>
            </tr>
            <tr>
                <td><button id="12Phil2">12 Б</button></td>
                <td> </td>
                <td><button id="14Phil2">14 Б</button></td>
                <td><button id="15Phil2">15 Б</button></td>
                <td><button id="16Phil2">16 Б</button></td>
                <td><button id="17Phil2">17 Б</button></td>
            </tr>
            <tr>
                <td><button id="12Eco2">12 В</button></td>
                <td>&nbsp;</td>
                <td> </td>
                <td><button id="15Eco2">15 В</button></td>
                <td>&nbsp;</td>
                <td> </td>
            </tr>
            <tr>
                <td><button id="12Econ2">12 Г</button></td>
                <td>&nbsp;</td>
                <td><button id="14Econ2">14 Г</button></td>
                <td><button id="15Econ2">15 Г</button></td>
                <td><button id="16Econ2">16 Г</button></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><button id="12Law2">12 Д</button></td>
                <td><button id="13Law2">13 Д</button></td>
                <td> </td>
                <td><button id="15Law2">15 Д</button></td>
                <td><button id="16Law2">16 Д</button></td>
                <td><button id="17Law2">17 Д</button></td>
            </tr>
            <tr>
                <td><button id="12PR2">12 Е</button></td>
                <td><button id="13PR2">13 Е</button></td>
                <td> </td>
                <td><button id="15PR2">15 Е</button></td>
                <td>&nbsp;</td>
                <td> </td>
            </tr>
            <tr>
                <td><button id="12Upr12">12 У1</button></td>
                <td><button id="12Upr22">12 У2</button></td>
                <td> </td>
                <td><button id="15Upr2">15 У</button></td>
                <td>&nbsp;</td>
                <td> </td>
            </tr>
            <tr>
                <td><button id="12Tur2">12 Т</button></td>
                <td><button id="13Tur2">13 Т</button></td>
                <td><button id="14Tur2">14 Т</button></td>
                <td><button id="15Tur2">15 Т</button></td>
                <td>&nbsp;</td>
                <td> </td>
            </tr>
            <tr>
                <td><button id="1AMath2">1 АА</button></td>
                <td> </td>
                <td><button id="3AMath2">3 АА</button></td>
                <td><button id="4AMath2">4 АА</button></td>
            </tr>
            <tr>
                <td><button id="1APhil2">1 АБ</button></td>
                <td> </td>
                <td><button id="3APhil">3 АБ</button></td>
            </tr>
            <tr>
                <td><button id="1AEco2">1 АВ</button></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><button id="4AEco2">4 АВ</button></td>
            </tr>
            <tr>
                <td><button id="1AEcon2">1 АГ</button></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><button id="1ALaw2">1 АД</button></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            </tbody>
        </table>
       
    </section>
</section>
</section>
<footer>
    <p>
        &copy; Диспетчерская учебного отдела СГОАН, <?php echo date("Y"); ?>
    </p>
</footer>
</div><!-- end .container -->
<div id="scheduleBox" style="display: none"></div>
</body>
</html>