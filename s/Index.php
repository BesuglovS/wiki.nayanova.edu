<?php
session_start();

$dbPrefix = "s_";

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

require_once("../_php/includes/Database.php");

function ReformatDate($semesterStarts)
{
    $semesterStartsCorrectFormat =
        mb_substr($semesterStarts, 8, 2) . "." .
        mb_substr($semesterStarts, 5, 2) . "." .
        mb_substr($semesterStarts, 0, 4);
    return $semesterStartsCorrectFormat;
}

$studentsQuery  = "SELECT F, I, O, BirthDate ";
$studentsQuery .= "FROM " . $dbPrefix . "students ";
$studentsQuery .= "WHERE Expelled = 0 ";

$students = $database->query($studentsQuery);
$accounts = array();
while ($student = $students->fetch_assoc())
{
    $account = array();

    $I = (mb_strlen($student["I"]) > 0) ? mb_substr($student["I"], 0, 2): "";
    $O = (mb_strlen($student["O"]) > 0) ? mb_substr($student["O"], 0, 2): "";

    $accounts[$student["F"] . " " . $I . $O] = ReformatDate($student["BirthDate"]);
}

$extraAccountsQuery  = "SELECT Login, Password ";
$extraAccountsQuery .= "FROM " . $dbPrefix . "LoginAccounts ";

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
    echo "<script src=\"../upload/_js/jquery.switchButton.js\"></script> ";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../upload/_css/jquery.switchButton.css\"> ";
    echo "<!-- Main --> ";
    echo "<script src=\"upload/js/main.js\"></script> ";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../upload/_css/main.css\"> ";

    echo "</head> ";
    echo "<body> ";
    echo "<div id=\"container\"> ";
    echo "<header class=\"cf\"> ";
    echo "<img src=\"../upload/images/DVZ.png\" id=\"headerLogo\" width=\"150\" height=\"150\"> ";
    echo "<div id=\"weekDiv\"> ";
    echo "Неделя<br /> ";
    echo "<div id=\"weekNum\"> ";


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
    echo "В поле пароля введите дату рожения в формате: День (2 цифры), точка, месяц (2 цифры), точка, год (4 цифры)";
    echo "<br /> Например: 04.04.1985</div>";
    echo "</form> ";
    echo "</div>";
    echo "</section> ";
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
$studentIdQuery .= "FROM " . $dbPrefix ."students ";
$studentIdQuery .= "WHERE F = '" . $F . "' ";
$studentIdQuery .= "AND BirthDate = '" . $MySQLDate . "' ";

$studentResult = $database->query($studentIdQuery);
$studentIdArray = $studentResult->fetch_assoc();
$studentId = $studentIdArray["StudentId"];

if ($studentId != "")
{
    $_SESSION['studentId'] = $studentId;
}


$statQuery  = "INSERT INTO " . $dbPrefix ."LoginLog ";
$statQuery .= "(StudentId, DateTime, RemoteAddr) ";
$statQuery .= "VALUES (" . $studentId . ", \"" . $today . "\", \"" . $_SERVER['REMOTE_ADDR'] . "\")";

$database->query($statQuery);

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
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
    <script src="../upload/_js/jquery.switchButton.js"></script>
    <link rel="stylesheet" type="text/css" href="../upload/_css/jquery.switchButton.css">
    <link rel="stylesheet" type="text/css" href="../upload/_css/jquery.countdown.css">
    <script src="../upload/_js/jquery.plugin.min.js"></script>
    <script src="../upload/_js/jquery.countdown.min.js"></script>
    <script src="../upload/_js/jquery.countdown-ru.js"></script>
    <!-- Main -->
    <script src="upload/js/main.js"></script>
    <link rel="stylesheet" type="text/css" href="../upload/_css/main.css">

</head>
<body>
<div id="container">
<header id="loginHeader">
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
    <img src="../upload/images/DVZ.png" id="headerLogo" width="150" height="150">
    <div id="weekDiv">
        Неделя<br />
        <div id="weekNum">
            <?php
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
        $teacherList = $database->query("SELECT * FROM " . $dbPrefix . "teachers");
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
        <h2 id="groupsScheduleHeader">Расписание по группам</h2>
        <!--
        <p>
            <button id="studentGroups">Списки групп</button>
        </p>-->
        <p>
            <button id="planGroups">Дисциплины по группам</button>
        </p>
        <p>
            <button id="planByTeacher">Дисциплины по преподавателям</button>
        </p>
        <p>
            <button id="today">Сегодня</button>
            <button id="tomorrow">Завтра</button>
        </p>
        <p>
            <input type="text" id="scheduleDate">
        </p>
        <p>
            <button id="Cha">Корп № 1</button>
            <button id="Mol">Корп № 2</button>
            <button id="Jar">Корп № 3</button>
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
            <td rowspan="2"><button id="8Math">8 А</button></td>
            <td rowspan="2"><button id="9Math">9 А</button></td>            
			<td rowspan="2"><button id="10Math">10 А</button></td>            
            <td><button id="11Math1">11 А1</button></td>            
        </tr>
        <tr>
            <td><button id="11Math2">11 А2</button></td>
        </tr>
        <tr>
            <td><button id="8Hum">8 Б</button></td>
            <td><button id="9Hum">9 Б</button></td>
            <td><button id="10Hum">10 Б</button></td>
            <td><button id="11Hum">11 Б</button></td>
        </tr>
        <tr>
            <td><button id="8Eco">8 В</button></td>
            <td><button id="9Eco">9 В</button></td>
            <td><button id="10Eco">10 В</button></td>
            <td><button id="11Eco">11 В</button></td>
        </tr>
        <tr>
            <td><button id="8Econ">8 Г</button></td>
            <td><button id="9Econ">9 Г</button></td>
            <td><button id="10Econ">10 Г</button></td>
            <td><button id="11Econ">11 Г</button></td>
        </tr>
        </tbody>
    </table>
</section>
<section id="vk">
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
                    $facultiesList = $database->query("SELECT * FROM " . $dbPrefix . "faculties");
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
	
	<img src="upload/images/snegovik.jpg" style="border-radius: 200px; display:block;
    margin:auto;" width="200px" >

    <div style="text-align: center">До нового года осталось:</div>
    <div id="summer" style="height: 50px"></div>

    <table id="dateScheduleTable2">
        <tbody>
        <tr>
            <td><button id="1A">1 А</button></td>
            <td><button id="2A">2 А</button></td>
            <td><button id="3A">3 А</button></td>
            <td><button id="4A">4 А</button></td>
            <td><button id="5A">5 А</button></td>
            <td><button id="6A">6 А</button></td>
            <td><button id="7A">7 А</button></td>
        </tr>
        <tr>
            <td><button id="1B">1 Б</button></td>
            <td><button id="2B">2 Б</button></td>
            <td><button id="3B">3 Б</button></td>
            <td><button id="4B">4 Б</button></td>
            <td><button id="5B">5 Б</button></td>
            <td><button id="6B">6 Б</button></td>
            <td><button id="7B">7 Б</button></td>
        </tr>
        <tr>
            <td><button id="1V">1 В</button></td>
            <td><button id="2V">2 В</button></td>
            <td><button id="3V">3 В</button></td>
            <td><button id="4V">4 В</button></td>
            <td><button id="5V">5 В</button></td>
            <td><button id="6V">6 В</button></td>
            <td><button id="7V">7 В</button></td>
        </tr>
        <tr>
            <td><button id="1G">1 Г</button></td>
            <td><button id="2G">2 Г</button></td>
            <td><button id="3G">3 Г</button></td>
            <td><button id="4G">4 Г</button></td>
            <td><button id="5G">5 Г</button></td>
            <td><button id="6G">6 Г</button></td>
            <td><button id="7G">7 Г</button></td>
        </tr>
        <tr>
            <td><button id="1D">1 Д</button></td>
            <td><button id="2D">2 Д</button></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><button id="6D">6 Д</button></td>
            <td><button id="7D">7 Д</button></td>
        </tr>
        </tbody>
    </table>
	
    <!--
    <script type="text/javascript" src="//vk.com/js/api/openapi.js?105"></script>
    -->
    <!-- VK Widget
    <div id="vk_groups"></div>
    <script type="text/javascript">
        VK.Widgets.Group("vk_groups", {mode: 2, width: "300", height: "320"}, 2691142);
    </script>
    -->
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