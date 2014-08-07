<?php
session_start();

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

function ReformatDate($semesterStarts)
{
    $semesterStartsCorrectFormat =
        mb_substr($semesterStarts, 8, 2) . "." .
        mb_substr($semesterStarts, 5, 2) . "." .
        mb_substr($semesterStarts, 0, 4);
    return $semesterStartsCorrectFormat;
}

$studentsQuery  = "SELECT F, I, O, BirthDate ";
$studentsQuery .= "FROM students ";
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

$FromNU = False;
if (($_SERVER['REMOTE_ADDR'] == "95.167.125.206") || ($_SERVER['REMOTE_ADDR'] == "85.236.163.58"))
{
    $FromNU = True;
}

if((((!isset($_SESSION['NUlogin']) || !isset($_SESSION['NUpassword']))) ||
   (!(array_key_exists($_SESSION['NUlogin'], $accounts) &&
    $accounts[$_SESSION['NUlogin']] === $_SESSION['NUpassword']))) && ($FromNU == False))
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
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"upload/_css/jquery.switchButton.css\"> ";
    echo "<!-- Main --> ";
    echo "<script src=\"upload/_js/main.js\"></script> ";
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"upload/_css/main.css\"> ";

    echo "</head> ";
    echo "<body> ";
    echo "<div id=\"container\"> ";
    echo "<header class=\"cf\"> ";
        echo "<img src=\"upload/images/DVZ-beta.png\" id=\"headerLogo\" width=\"150\" height=\"150\"> ";
        echo "<div id=\"weekDiv\"> ";
            echo "Неделя<br /> ";
            echo "<div id=\"weekNum\"> ";

                require_once("_php/includes/ConfigOptions.php");
                $now = date('Y-m-d');
                $today = DateTime::createFromFormat('Y-m-d', $now);
                $semesterStarts = $options["Semester Starts"];
                $start = DateTime::createFromFormat('Y-m-d', $semesterStarts);
                $weekNum = (int)(floor(($today->diff($start)->format('%a')) / 7)) + 1;
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
$studentIdQuery .= "FROM students ";
$studentIdQuery .= "WHERE F = '" . $F . "' ";
$studentIdQuery .= "AND BirthDate = '" . $MySQLDate . "' ";

$studentResult = $database->query($studentIdQuery);
$studentIdArray = $studentResult->fetch_assoc();
$studentId = $studentIdArray["StudentId"];




$statQuery  = "INSERT INTO LoginLog ";
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
    <script src="upload/_js/jquery.switchButton.js"></script>
    <link rel="stylesheet" type="text/css" href="upload/_css/jquery.switchButton.css">
    <!-- Main -->
    <script src="upload/_js/main.js"></script>
    <link rel="stylesheet" type="text/css" href="upload/_css/main.css">

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
    <img src="upload/images/DVZ-beta.png" id="headerLogo" width="150" height="150">
    <div id="weekDiv">
        Неделя<br />
        <div id="weekNum">
            <?php
            require_once("_php/includes/ConfigOptions.php");
            $now = date('Y-m-d');
            $today = DateTime::createFromFormat('Y-m-d', $now);
            $semesterStarts = $options["Semester Starts"];
            $start = DateTime::createFromFormat('Y-m-d', $semesterStarts);
            $weekNum = (int)(floor(($today->diff($start)->format('%a')) / 7)) + 1;
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
        <h2 id="groupsScheduleHeader">Расписание по группам</h2>
        <p>
            <button id="studentGroups">Списки групп</button>
        </p>
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
            <button id="Mol">Корп № 2</button>
            <button id="Jar">Корп № 3</button>
            <button id="Other">Прочие</button>
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
            <td><button id="12Math">12 А</button></td>
            <td><button id="13Math">13 А</button></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><button id="12Phil">12 Б</button></td>
            <td><button id="13Phil">13 Б</button></td>
            <td><button id="14Phil">14 Б</button></td>
            <td>&nbsp;</td><!--<td><button id="15Phil">15 Б</button></td>-->
        </tr>
        <tr>
            <td><button id="12Eco0">12 В0</button></td>
            <td><button id="12Eco">12 В</button></td>
            <td><button id="13Eco">13 В</button></td>
            <td><button id="14Eco">14 В</button><!--<button id="C">С</button></td>-->
            <td>&nbsp;</td><!--<td><button id="15Eco">15 В</button></td>-->
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td><button id="12Econ">12 Г</button></td>
            <td><button id="13Econ">13 Г</button></td>
            <td rowspan="2"><button id="14Econ">14 Г</button></td>
            <td rowspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td><button id="12EconN">12 Г(Н)</button></td>
            <td><button id="13EconN">13 Г(Н)</button></td>
            <!--<td><button id="14EconN">14 Г(Н)</button></td>-->
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td><button id="12Law">12 Д</button></td>
            <td><button id="13Law">13 Д</button></td>
            <td rowspan="2"><button id="14Law">14 Д</button></td>
            <td rowspan="2">&nbsp;</td><!--<td rowspan="2"><button id="15Law">15 Д</button></td>-->
        </tr>
        <tr>
            <td><button id="12LawN">12 Д(Н)</button></td>
            <td><button id="13LawN"><img src="upload/images/p16.png" width="16" height="16" /></button></td>
            <!--<td><button id="14LawN">14 Д(Н)</button></td>-->
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><button id="12PR">12 Е</button></td>
            <td rowspan="2"><button id="13PR">13 Е</button></td>
            <td rowspan="2"><button id="14PR">14 Е</button></td>
            <td>&nbsp;</td><!--<td rowspan="2"><button id="15PR">15 Е</button></td>-->
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><button id="12PRN">12 Е(Н)</button></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><button id="12Upr">12 У</button></td>
            <td><button id="13Upr">13 У</button></td>
            <td><button id="14Upr">14 У</button></td>
            <td><button id="15Upr">15 У</button></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><button id="12Tur">12 Т</button></td>
            <td><button id="13Tur">13 Т</button></td>
            <td><button id="14Tur">14 Т</button></td>
            <td>&nbsp;</td>
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

    <script type="text/javascript" src="//vk.com/js/api/openapi.js?105"></script>
    <!-- VK Widget -->
    <div id="vk_groups"></div>
    <script type="text/javascript">
        VK.Widgets.Group("vk_groups", {mode: 2, width: "300", height: "500"}, 2691142);
    </script>
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