<?php
require_once("_php/includes/Database.php");
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
    <header class="cf">
        <img src="upload/images/DVZ-beta.png" id="headerLogo" width="150" height="150">
        <div id="weekDiv">
            Неделя<br />
            <div id="weekNum">-1<!--
                <?php
                require_once("_php/includes/ConfigOptions.php");
                $now = date('Y-m-d');
                $today = DateTime::createFromFormat('Y-m-d', $now);
                $semesterStarts = $options["Semester Starts"];
                $start = DateTime::createFromFormat('Y-m-d', $semesterStarts);
                $weekNum = (int)(floor(($today->diff($start)->format('%a')) / 7)) + 1;
                echo $weekNum;
                ?> -->
            </div>
        </div>
        <h1>Диспетчерская учебного отдела СГОАН</h1>
    </header>
    <section id="content" class="cf">
        <section id="facultyDOWButtons">
            <div id="DOWButtonwrapper">
                <button id="Math">А</button>
                <button id="Phil">Б</button>
                <button id="Bio">В</button>
                <button id="Econ">Г</button>
                <button id="Law">Д</button>
                <button id="PR">Е</button>
                <button id="Upr">У</button>
                <button id="Tur">Т</button>
            </div>
        </section>
        <section id="graduates">
            <div id="graduatesDiv">
                Развлечения для выпускников:<br />
                <button id="MathG">16 А</button>
                <button id="PhilG">15 Б</button>
                <button id="BioG">15 В</button>
                <button id="EconG">14 Г(Н) + 15 Г</button>
                <button id="LawG">14 Д(Н) + 15 Д</button>
                <button id="PRG">15 Е</button>
                <button id="UprG">16 У</button>
            </div>
        </section>
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
            <div id="vkGroupLink">
                <p>
                    Диспетчерская в контакте
                    <br />
                    <span id="vklinktext">https://vk.com/nayanovadisp</span>
                </p>
            </div>
            <div id="sessiusImageDiv">
                <img src="upload/images/sessius-sdavamus.jpg" id="sessiussdavamus" width="221" height="215">
            </div>

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
                    <td><button id="12Math2">12 А</button></td>
                    <td><button id="13Math2">13 А</button></td>
                    <td><button id="14Math2">14 А</button></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><button id="12Phil2">12 Б</button></td>
                    <td><button id="13Phil2">13 Б</button></td>
                    <td><button id="14Phil2">14 Б</button></td>
                    <td><button id="15Phil2">15 Б</button></td>
                </tr>
                <tr>
                    <td><button id="12Eco2">12 В</button></td>
                    <td><button id="13Eco2">13 В</button></td>
                    <td><button id="14Eco2">14 В</button></td>
                    <td><button id="15Eco2">15 В</button></td>
                </tr>
                <tr>
                    <td><button id="12Econ2">12 Г</button></td>
                    <td><button id="13Econ2">13 Г</button></td>
                    <td><button id="14Econ2">14 Г</button></td>
                    <td><button id="15Econ2">15 Г</button></td>
                </tr>
                <tr>
                    <td><button id="12EconN2">12 Г(Н)</button></td>
                    <td><button id="13EconN2">13 Г(Н)</button></td>
                    <td><button id="14EconN2">14 Г(Н)</button></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><button id="12Law2">12 Д</button></td>
                    <td><button id="13Law2">13 Д</button></td>
                    <td><button id="14Law2">14 Д</button></td>
                    <td><button id="15Law2">15 Д</button></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><button id="13LawN2">13 Д(Н)</button></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><button id="12PR2">12 Е</button></td>
                    <td><button id="13PR2">13 Е</button></td>
                    <td><button id="14PR2">14 Е</button></td>
                    <td><button id="15PR2">15 Е</button></td>
                </tr>
                <tr>
                    <td><button id="12Upr2">12 У</button></td>
                    <td><button id="13Upr2">13 У</button></td>
                    <td><button id="14Upr2">14 У</button></td>
                    <td><button id="15Upr2">15 У</button></td>
                </tr>
                <tr>
                    <td><button id="12Tur2">12 Т</button></td>
                    <td><button id="13Tur2">13 Т</button></td>
                    <td><button id="14Tur2">14 Т</button></td>
                    <td><button id="15Tur2">15 Т</button></td>
                </tr>
                </tbody>
            </table>
        </section>
    </section>
    <footer>
        <p>
            &copy; Диспетчерская учебного отдела СГОАН, , <?php echo date("Y"); ?>
        </p>
    </footer>
</div><!-- end .container -->
<div id="scheduleBox" style="display: none"></div>
</body>
</html>