<?php
require_once("php/Database.php");
?>
<!DOCTYPE html>
<html ng-app="main">
<head lang="en">
    <meta charset="UTF-8">
    <title>Диспетчерская учебного отдела СГОАН</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Диспетчерская учебного отдела СГОАН">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/touch/chrome-touch-icon-192x192.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-precomposed.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

    <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
    <!--
    <link rel="canonical" href="http://www.example.com/">
    -->

    <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- MDL -->
    <link rel="stylesheet" href="mdl/material.min.css">
    <script src="mdl/material.min.js"></script>
    <link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">
    <!-- MDL -->

    <link rel="stylesheet" href="css/main.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

    <script type="text/javascript" src="js/angular.min.js"></script>
    <script type="text/javascript" src="js/app.js"></script>
    <!-- Controllers -->
    <script type="text/javascript" src="js/Controllers/GroupList.js"></script>
    <script type="text/javascript" src="js/Controllers/TeachersList.js"></script>
    <script type="text/javascript" src="js/Controllers/dailyGroupSchedule.js"></script>
    <script type="text/javascript" src="js/Controllers/TeacherScheduleController.js"></script>
    <script type="text/javascript" src="js/Controllers/GroupDisciplinesController.js"></script>
    <script type="text/javascript" src="js/Controllers/TeacherDisciplinesController.js"></script>
    <script type="text/javascript" src="js/Controllers/buildingAuditoriumsController.js"></script>
    <script type="text/javascript" src="js/Controllers/BuildingListController.js"></script>
    <script type="text/javascript" src="js/Controllers/WeekScheduleController.js"></script>
    <!-- Controllers -->

    <script type="text/javascript" src="js/main.js"></script>
</head>
<body>
    <div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
        <header class="demo-header mdl-layout__header mdl-color--white mdl-color--grey-100 mdl-color-text--grey-600">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">ДУО СГОАН</span>
                <div class="mdl-layout-spacer"></div>
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
                    <label class="mdl-button mdl-js-button mdl-button--icon" for="search">
                        <i class="material-icons">search</i>
                    </label>
                    <div class="mdl-textfield__expandable-holder">
                        <input class="mdl-textfield__input" type="text" id="search" />
                        <label class="mdl-textfield__label" for="search">Увы :-( Поиск не работает</label>
                    </div>
                </div>
                <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
                    <i class="material-icons">more_vert</i>
                </button>
                <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
                    <li class="mdl-menu__item" id="nuSite">Сайт университета</li>
                    <!--
                    <li class="mdl-menu__item"><a href="http://nayanova.edu/selection-committee/speciality">Сайт университета / Приёмная комиссия / Специальности и направления подготовки</a></li>
                    <li class="mdl-menu__item"><a href="http://http://nayanova.edu/selection-committee/competitive-situation">Сайт университета / Приёмная комиссия / Конкурсная ситуация</a></li>
                    -->
                </ul>
            </div>
        </header>
        <div class="demo-drawer mdl-layout__drawer mdl-color--grey-100 mdl-color-text--grey-600">
            <header class="demo-drawer-header">
                <table>
                    <tr>
                        <td><a ng-click="tab = 1" href="#"><img src="images/DVZ.png" class="demo-avatar"></a></td>
                        <td><a ng-click="tab = 1" href="#"><img src="images/lnw.png" class="demo-avatar"></a></td>
                    </tr>
                </table>
                <div class="demo-avatar-dropdown">
                    <span>Гость</span>
                    <div class="mdl-layout-spacer"></div>
                    <button id="accbtn" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
                        <i class="material-icons">arrow_drop_down</i>
                    </button>
                    <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="accbtn">
                        <button class="mdl-menu__item"></button>

                    </ul>
                </div>
            </header>
            <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 1 }" ng-click="tab = 1" href=""><i class="material-icons">home</i>Главная</a>
                <a class="mdl-navigation__link" href="http://wiki.nayanova.edu/FlatIndex.php"><i class="material-icons">restore</i>Старая версия сайта</a>
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 2 }" ng-click="tab = 2" href="">Расписание группы</a>
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 3 }" ng-click="tab = 3" href="">Расписание преподавателя</a>
                <!--
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 4 }" ng-click="tab = 4" href="">Списки групп</a>
                -->
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 5 }" ng-click="tab = 5" href="">Дисциплины по группам</a>
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 6 }" ng-click="tab = 6" href="">Дисциплины по преподавателям</a>
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 7 }" ng-click="tab = 7" href="">Занятость аудиторий</a>
                <a class="mdl-navigation__link" ng-class="{ activeNavLink: tab === 8 }" ng-click="tab = 8" href="">Расписание на неделю</a>
                <div class="mdl-layout-spacer"></div>
                <a class="mdl-navigation__link" href="">
                    <i class="mdl-color-text--blue-grey-400 material-icons">help_outline</i>
                    Справка
                </a>
            </nav>
        </div>
        <main class="mdl-layout__content mdl-color--grey-100" ng-init="tab = 1">
            <div ng-show="tab === 1"> <!-- tab === 1 -->
                <div class="mdl-grid">
                    <!-- Расписание группы -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Расписание группы</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Расписание группы на конкретный день.
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mainPageRoundButton"
                               ng-click="tab = 2">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Расписание группы -->

                    <!-- Расписание преподавателя -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Расписание преподавателя</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Расписание преподавателя.
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mainPageRoundButton"
                               ng-click="tab = 3">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Расписание преподавателя -->

                    <!-- Списки групп
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Списки групп</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Списки групп
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored mdl-js-ripple-effect">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Списки групп -->

                    <!-- Дисциплины по группам -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Дисциплины по группам</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Дисциплины по группам
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mainPageRoundButton"
                               ng-click="tab = 5">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Дисциплины по группам -->

                    <!-- Дисциплины по преподавателю -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Дисциплины по преподавателю</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Дисциплины по преподавателю
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mainPageRoundButton"
                               ng-click="tab = 6">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Дисциплины по преподавателю -->
                    
                    <!-- Расписание пересдач -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Расписание пересдач</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
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
                    </div>
                    <!-- Расписание пересдач -->
                    
                    <!-- Занятость аудиторий -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Занятость аудиторий</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Занятость аудиторий корпуса на конкретный день.
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mainPageRoundButton"
                               ng-click="tab = 7">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Занятость аудиторий -->
                    
                    <!-- Расписание на неделю -->
                    <div class="mdl-card mdl-shadow--2dp demo-card-square" id="groupScheduleCard">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">Расписание на неделю</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            Расписание на одну неделю по группам.
                        </div>
                        <div class="mdl-card__actions mdl-card--border textRight" id="groupScheduleCardBottom">
                            <a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mainPageRoundButton"
                               ng-click="tab = 8">
                                <i class="material-icons">forward</i>
                            </a>
                        </div>
                    </div>
                    <!-- Расписание на неделю -->
                </div>
            </div> <!-- tab === 1 -->
            <div id="tab2" ng-show="tab === 2"> <!-- tab === 2 -->
                <div id="dailyScheduleContainer" ng-controller="DailyGroupScheduleController as dailySchedule">
                    <div id="dailyScheduleDatePlusTitles">
                        <h4>Дата занятий</h4>
                        <div ng-show="dailySchedule.loading" class="mdl-spinner mdl-js-spinner is-active" id="loadingSpinner"></div>
                        <input type="text" id="scheduleDate" ng-model="date" ng-change="dailySchedule.load()">
                        <div id="dailySchedule" ng-show="dailySchedule.show">
                            <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect"
                                    id="hideDailyScheduleButton"
                                    ng-click="dailySchedule.close()">
                                <i class="material-icons">close</i>
                            </button>

                            <h4>{{dailySchedule.studentGroupName}} - {{dailySchedule.date}}</h4>

                            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                                <thead>
                                <tr>
                                    <th>Время</th>
                                    <th class="mdl-data-table__cell--non-numeric">Занятие</th>
                                    <!--
                                    <th class="mdl-data-table__cell--non-numeric">Дисциплина</th>
                                    <th class="mdl-data-table__cell--non-numeric">Преподаватель</th>
                                    <th class="mdl-data-table__cell--non-numeric">Аудитория</th>
                                    <th class="mdl-data-table__cell--non-numeric">Группа</th>
                                    -->
                                </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="lesson in dailySchedule.Lessons">
                                        <td>{{lesson.Time}}</td>
                                        <td class="mdl-data-table__cell--non-numeric">
                                            {{lesson.discName}}<br />
                                            {{lesson.FIO}}<br />
                                            {{lesson.audName}}<br />
                                            {{lesson.groupName}}
                                        </td>
                                        <!--
                                        <td class="mdl-data-table__cell--non-numeric">{{lesson.discName}}</td>
                                        <td class="mdl-data-table__cell--non-numeric">{{lesson.FIO}}</td>
                                        <td class="mdl-data-table__cell--non-numeric">{{lesson.audName}}</td>
                                        <td class="mdl-data-table__cell--non-numeric">{{lesson.groupName}}</td>
                                        -->
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h4>Список групп</h4>
                    </div>
                    <div id="groupList" ng-controller="GroupListController as groups">
                        <div class="mdl-grid">
                            <div ng-repeat="group in groups.list">
                                <div class="mdl-cell mdl-cell--2-col">
                                    <button
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored
                                        mdl-js-ripple-effect dailyScheduleGroupButton"
                                        ng-click="dailySchedule.loadSchedule(group)">
                                        {{group.Name}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- tab === 2 -->

            <div ng-show="tab === 3"> <!-- tab === 3 -->
                <div id="dailyTeacherScheduleContainer" ng-controller="TeacherScheduleController as teacherSchedule">
                    <div ng-show="dailySchedule.loading" class="mdl-spinner mdl-js-spinner is-active" id="loadingSpinner"></div>
                    <div id="TeacherScedule" ng-show="teacherSchedule.show">
                        <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect"
                                id="hideDailyScheduleButton"
                                ng-click="teacherSchedule.close()">
                            <i class="material-icons">close</i>
                        </button>

                        <div class="mdl-grid">
                            <div ng-repeat="dowTime in teacherSchedule.list"
                                 class="mdl-card mdl-shadow--2dp demo-card-square teacherScheduleCard">
                                <div class="mdl-card__title mdl-card--expand">
                                    <h2 class="mdl-card__title-text">
                                        {{dowTime[0].Lesson.dow}}<br />
                                        {{dowTime[0].Lesson.Time}}
                                    </h2>
                                </div>
                                <div ng-repeat="dowTimeTfd in dowTime"  class="mdl-card__supporting-text">
                                    {{dowTimeTfd.Lesson.disciplineName}}<br />
                                    {{dowTimeTfd.Lesson.groupName}}<br />
                                    ({{dowTimeTfd.Weeks.String}})<br />
                                    <span ng-repeat="audWeeks in dowTimeTfd.AudWeeks">
                                        {{audWeeks}}<br />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="dailyTeacherScheduleDatePlusTitles">
                        <h4>Преподаватели</h4>
                    </div>
                    <div id="teachersList" ng-controller="TeachersListController as teachers">
                        <div class="mdl-grid">
                            <div ng-repeat="teacher in teachers.list">
                                <div class="mdl-cell mdl-cell--6-col">
                                    <button
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect dailyScheduleTeacherButton"
                                        ng-click="teacherSchedule.loadSchedule(teacher)">
                                        {{teacher.FIO}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- tab === 3 -->

            <div ng-show="tab === 5"> <!-- tab === 5 -->
                <div id="groupPlanContainer" ng-controller="GroupDisciplinesController as groupDisciplines">
                    <div id="groupPlanTitle">
                        <h4>Дисциплины группы</h4>
                    </div>

                    <div ng-show="groupDiciplines.loading" class="mdl-spinner mdl-js-spinner is-active" id="loadingSpinner"></div>

                    <div id="groupDisciplinesList" ng-show="groupDisciplines.show">
                        <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect"
                                id="hidegroupDisciplinesButton"
                                ng-click="groupDisciplines.close()">
                            <i class="material-icons">close</i>
                        </button>

                        <div class="mdl-grid">
                            <div ng-repeat="discipline in groupDisciplines.list"
                                 class="mdl-card mdl-shadow--2dp demo-card-square teacherScheduleCard">
                                <div class="mdl-card__title mdl-card--expand">{{discipline.Name}}</div>
                                <div class="mdl-card__supporting-text">
                                    Аудиторные часы: {{discipline.AuditoriumHours}}<br />
                                    Лекции: {{discipline.LectureHours}}<br />
                                    Практические занятия: {{discipline.PracticalHours}}<br />
                                    Форма отчётности: {{discipline.Attestation}}<br />
                                    Группа: {{discipline.StudentGroupName}}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div id="groupList" ng-controller="GroupListController as groups">
                        <div class="mdl-grid">
                            <div ng-repeat="group in groups.list">
                                <div class="mdl-cell mdl-cell--2-col">
                                    <button
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect dailyScheduleGroupButton"
                                        ng-click="groupDisciplines.loadSchedule(group)">
                                        {{group.Name}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- tab === 5 -->

            <div ng-show="tab === 6"> <!-- tab === 6 -->
                <div id="teacherPlanContainer" ng-controller="TeacherDisciplinesController as teacherDisciplines">
                    <div id="teacherPlanTitle">
                        <h4>Дисциплины преподавателя</h4>
                    </div>

                    <div ng-show="teacherDisciplines.loading" class="mdl-spinner mdl-js-spinner is-active" id="loadingSpinner"></div>

                    <div id="teacherDisciplinesList" ng-show="teacherDisciplines.show">
                        <h4>{{teacherDisciplines.teacherFIO}}</h4>

                        <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect"
                                id="hidegroupDisciplinesButton"
                                ng-click="teacherDisciplines.close()">
                            <i class="material-icons">close</i>
                        </button>

                        <div class="mdl-grid">
                            <div ng-repeat="discipline in teacherDisciplines.list"
                                 class="mdl-card mdl-shadow--2dp demo-card-square teacherScheduleCard">
                                <div class="mdl-card__title mdl-card--expand">{{discipline.Name}}</div>
                                <div class="mdl-card__supporting-text">
                                    Аудиторные часы: {{discipline.AuditoriumHours}}<br />
                                    Лекции: {{discipline.LectureHours}}<br />
                                    Практические занятия: {{discipline.PracticalHours}}<br />
                                    Форма отчётности: {{discipline.Attestation}}<br />
                                    Группа: {{discipline.StudentGroupName}}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="teachersList" ng-controller="TeachersListController as teachers">
                        <div class="mdl-grid">
                            <div ng-repeat="teacher in teachers.list">
                                <div class="mdl-cell mdl-cell--6-col">
                                    <button
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect dailyScheduleTeacherButton"
                                        ng-click="teacherDisciplines.loadSchedule(teacher)">
                                        {{teacher.FIO}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- tab === 6 -->
            
            <div ng-show="tab === 7"> <!-- tab === 7 -->
                <div id="buildingAuditoriums" ng-controller="buildingAuditoriumsController as buildingAuditoriums">
                    <div id="buildingAuditoriumsDatePlusTitles">
                        <h4>Дата занятий</h4>
                        <div ng-show="buildingAuditoriums.loading" class="mdl-spinner mdl-js-spinner is-active" id="loadingSpinner"></div>
                        <input type="text" id="buildingDate" ng-model="scheduleDate">
                        <h4>Корпуса</h4>                        
                    </div>                   
                    
                    
                    <div id="buildingsList" ng-controller="BuildingListController as buildings">                       
                        <span ng-repeat="building in buildings.list" class="buildingButton">
                            <button
                                class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored
                                mdl-js-ripple-effect buidingButton"
                                ng-click="buildingAuditoriums.loadSchedule(building.BuildingId)">{{building.Name}}</button>
                        </span>                                                
                    </div>
                    
                    <div id="buildingLessons" ng-show="buildingAuditoriums.show">
                            <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect"
                                    id="hideDailyScheduleButton"
                                    ng-click="buildingAuditoriums.close()">
                                <i class="material-icons">close</i>
                            </button>
                            
                            <div ng-bind-html="result"></div>                            
                    </div>
                </div>
            </div><!-- tab === 7 -->
            
            <div id="tab8" ng-controller="WeekScheduleController as weekAud" ng-show="tab === 8"> <!-- tab === 8 -->
            	<div id="weekScheduleTitles">
                        <h4>Неделя занятий
	                        <select id="scheduleWeek"
	                        ng-model="selectedWeek"
	                        ng-change="weekAud.load()"
	                        ng-options="week for week in weekAud.weeks">                        	                        	
	                        </select>
                        </h4>
                </div>
                
                <div ng-show="weekAud.loading" class="mdl-spinner mdl-js-spinner is-active" id="loadingSpinner"></div>
                
                <div id="weekSchedule" ng-show="weekAud.show">
                    <button class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect"
                            id="hideDailyScheduleButton"
                            ng-click="weekAud.close()">
                        <i class="material-icons">close</i>
                    </button>                    
                    <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
	                    <tbody>
	                    	<tr ng-repeat-start="dowLessons in weekAud.result"	                        	
	                        	ng-show="dowLessons['Count'] > 0" >
	                        	<td>{{dowLessons["dowName"]}}</td>	                        	
	                        	<td class="mdl-data-table__cell--non-numeric">{{dowLessons["date"]}}</td>
	                       	</tr>
	                        <tr ng-repeat-end
	                        	ng-repeat="lesson in dowLessons.Lessons"
	                        	ng-show="dowLessons['Count'] > 0" >
	                            <td>{{lesson.Time}}</td>
	                            <td class="mdl-data-table__cell--non-numeric">
	                                {{lesson.discName}}<br />
	                                {{lesson.FIO}}<br />
	                                {{lesson.audName}}<br />
	                                {{lesson.groupName}}
	                            </td>
	                        </tr>	                            
	                    	                    		
                    	</tbody>
                	</table>          
                </div>
                
                <div id="groupList" ng-controller="GroupListController as groups">
                    <div class="mdl-grid">
                        <div ng-repeat="group in groups.list">
                            <div class="mdl-cell mdl-cell--2-col">
                                <button
                                    class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored
                                    mdl-js-ripple-effect dailyScheduleGroupButton"
                                    ng-click="weekAud.loadSchedule(group)">
                                    {{group.Name}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>                 	
            </div><!-- tab === 8 -->          
            
        </main>
    </div>
</body>
</html>