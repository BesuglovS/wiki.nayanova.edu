var groupNames = [
    "13 А", "14 А", "15 А",
    "12 Б", "13 Б", "14 Б", "15 Б", "17 Б",
    "13 В", "14 В", "15 В",
    "13 Г", "14 Г", "15 Г", "17 Г",
    "13 Д", "14 Д", "15 Д", "17 Д",
    "13 Е", "14 Е", "15 Е",
    "13 У", "14 У", "15 У",
    "12 Т", "13 Т", "14 Т", "15 Т",
    "2 АА", 
    "1 АБ", "2 АБ", "3 АБ",
    "2 АВ", "3 АВ",
    "2 АГ", "3 АГ",
    "2 АД", "3 АД"];
var groupIds = [
    "1", "2", "3",
    "4", "5", "6", "7", "28",
    "8", "9", "10", 
    "11", "12", "13", "29",
    "14", "15", "16", "30",
    "17", "18", "19",
    "20", "21", "22",
    "23", "24", "25", "26",
    "83",
    "81", "77", "82",
    "84", "73", 
    "85", "74", 
    "78", "79"];

var sessionGroupIds = groupIds;

/*
var sessionGroupIds = [
    "1", "2", "3", "4",
    "5", "6", "7", "8", "48", "49",
    "9", "10", "11", "12",
    "13", "16", "17", "19", "20", "22", "44", "45",
    "23", "26", "27", "28", "30", "46", "47",
    "31", "32", "33", "34",
    "35", "36", "37", "38",
    "39", "40", "41", "42",
    "179", "180", "181",
    "182", "183", "184",
    "185", "186", "187",
    "188", "189", "190",
    "191", "192"];
    */
    
var buttonSelectors = [
    "#13Math", "#14Math", "#15Math",
    "#12Phil", "#13Phil", "#14Phil", "#15Phil", "#17Phil",
    "#13Eco", "#14Eco", "#15Eco",
    "#13Econ", "#14Econ", "#15Econ", "#17Econ",
    "#13Law", "#14Law", "#15Law", "#17Law",
    "#13PR", "#14PR", "#15PR",
    "#13Upr", "#14Upr", "#15Upr",
    "#12Tur", "#13Tur", "#14Tur", "#15Tur",
    "#2AMath",
    "#1APhil", "#2APhil", "#3APhil",
    "#2AEco", "#3AEco",
    "#2AEcon", "#3AEcon",
    "#2ALaw", "#3ALaw"];

var buildingsIndexes = [];
buildingsIndexes["Mol"] = 1;
buildingsIndexes["Jar"] = 2;
buildingsIndexes["Other"] = 3;
buildingsIndexes["SSU"] = -1;

var schoolBuildings = [];
schoolBuildings["Cha"] = 2;
schoolBuildings["Mol"] = 1;
schoolBuildings["Jar"] = 3;

var dowRU = ["","Понедельник","Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье"];

function IfDatePickerIsEmptySetToday(dateString)
{
    if (dateString == "")
    {
        dateString = $.datepicker.formatDate("yy-mm-dd", new Date());
        $('button#today').trigger("click");
        dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
    }
    return dateString;
}

$(function() {
    /* Datepicker #scheduleDate */
    $( "#scheduleDate" ).datepicker();
    $.datepicker.regional['ru'] = {clearText: 'Очистить', clearStatus: '',
        closeText: 'Закрыть', closeStatus: '',
        prevText: '<Пред',  prevStatus: '',
        nextText: 'След>', nextStatus: '',
        currentText: 'Сегодня', currentStatus: '',
        monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
            'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
        monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
            'Июл','Авг','Сен','Окт','Ноя','Дек'],
        monthStatus: '', yearStatus: '',
        weekHeader: 'Не', weekStatus: '',
        dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
        dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
        dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
        dayStatus: 'DD', dateStatus: 'D, M d',
        dateFormat: 'dd mm yy', firstDay: 1,
        initStatus: '', isRTL: false};

    $( "#scheduleDate" ).datepicker( "option", "minDate", new Date(2017, 2 - 1, 1));
    $( "#scheduleDate" ).datepicker( "option", "maxDate", new Date(2017, 6 - 1, 4));

    $.datepicker.setDefaults($.datepicker.regional['ru']);
    /* Datepicker #scheduleDate */

    /* Today / tomorrow buttons */
    $( "button#today" ).click(function() { $("#scheduleDate").datepicker("setDate", "today"); });
    $( "button#tomorrow" ).click(function() { $("#scheduleDate").datepicker("setDate", "1"); });
    /* Today / tomorrow buttons */

    /* Today / tomorrow MY schedule buttons */
    $( "button#todaySchedule" ).click(function() {
        $("#scheduleDate").datepicker("setDate", "today");
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        var dialogDate = $.datepicker.formatDate("dd.mm.yyyy", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        $('#scheduleBox').dialog( {width: 600, title: dialogDate , minHeight : "50px" , position: ['center',20]} );
        var path = '_php/includes/DailyMySchedule.php?date="' + dateString + '"';
        $('#scheduleBox').load(path);
    });

    $( "button#tomorrowSchedule" ).click(function() {
        $("#scheduleDate").datepicker("setDate", "1");
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        $('#scheduleBox').dialog( {width: 600, title: dateString , minHeight : "50px" , position: ['center',20]} );
        var path = '_php/includes/DailyMySchedule.php?date="' + dateString + '"';
        $('#scheduleBox').load(path);
    });

    $( "button#MyMyMySchedule" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        $('#scheduleBox').dialog( {width: 600, title: dateString , minHeight : "50px" , position: ['center',20]} );
        var path = '_php/includes/DailyMySchedule.php?date="' + dateString + '"';
        $('#scheduleBox').load(path);
    });
    /* Today / tomorrow MY schedule buttons */

    $( "#PDFExport" ).click(function() {
        var faculty = $('#facultiesList').val();
        var dow = $('#dowPDFSelect').val();
        window.location = 'pdfExport.php?facultyId=' + faculty + '&dow=' + dow;
    });

    $( "#PDFExport" ).click(function() {
        var faculty = $('#facultiesList').val();
        var dow = $('#dowPDFSelect').val();
        window.location = 'pdfExport.php?facultyId=' + faculty + '&dow=' + dow;
    });

    $( "#DOWSchedule" ).click(function() {
        var faculty = $('#facultiesList').val();
        var dow = $('#dowPDFSelect').val();
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {"width": dialogWidth, title: dowRU[dow], minHeight : "50px" , position: ['center',20]} );
        var path = '_php/includes/FacultyDOWSchedule.php?facultyId=' + faculty + '&dow=' + dow;
        $('#scheduleBox').load(path);
    });

});


$(function() {
    /* Кнопки расписания для групп */
    for (var i = 0; i < buttonSelectors.length; i++) {
        $( buttonSelectors[i] ).click(function() {
            var isChecked = $('div#scheduleOrChangesDiv span:first-of-type').hasClass('on');
            var groupName = groupNames[buttonSelectors.indexOf("#" + this.id)];
            var groupId = groupIds[buttonSelectors.indexOf("#" + this.id)];

            if (isChecked)
            {
                var isSecondChecked = $('div#DayOrWeekDiv span:first-of-type').hasClass('on');
                if (!isSecondChecked) {
                    var groupId = groupIds[buttonSelectors.indexOf("#" + this.id)];
                    var groupName = groupNames[buttonSelectors.indexOf("#" + this.id)];

                    $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                    var dialogWidth = ($(window).width()*0.95 > 1000)? 1000 : $(window).width()*0.95;
                    $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                    var path = '_php/includes/GroupWeekSchedule.php?groupId=' + groupId;
                    $('#scheduleBox').load(path);
                }
                else
                {
                    var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
                    dateString = IfDatePickerIsEmptySetToday(dateString);
                    var dialogDate = $.datepicker.formatDate("dd.mm.yy", $( "#scheduleDate" ).datepicker( "getDate" ));
                    $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                    $('#scheduleBox').dialog( {width: 600, title: groupName + " (" + dialogDate + ")", minHeight : "50px" , position: ['center',20]} );
                    var path = '_php/includes/DailySchedule.php?groupId="' + groupId + '"&date="' + dateString + '"';
                    $('#scheduleBox').load(path);
                }
            }
            else
            {
                var isSecondChecked = !$('div#DayOrWeekDiv span:first-of-type').hasClass('on');
                var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
                dateString = IfDatePickerIsEmptySetToday(dateString);
                var path = '_php/includes/Changes.php?groupId=' + groupId + "&date=" + dateString + "&tomorrow=" + isSecondChecked;
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                $('#scheduleBox').dialog( {width: 600, title: groupName, minHeight : "50px" , position: ['center',20]} );
                $('#scheduleBox').load(path, function() {
                    $( "#eventsIndexList" ).change(function() {
                        $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />');
                        var pagingId = $("#eventsIndexList").val();
                        groupChangesPath = "_php/includes/GroupChanges.php?groupId=" + groupId +
                                           "&date=" + dateString + "&startFrom=" + pagingId  +
                                           "&tomorrow=" + isSecondChecked;
                        $('#eventList').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                        $('#eventList').load(groupChangesPath, function() {
                            $('#progress').empty();
                        });
                    });
                    $('#eventsIndexList').trigger('change');
                });
            }
        });
    }
    /* Кнопки расписания для групп */

    /* Кнопки для расписания сессии */
    for (var i = 0; i < buttonSelectors.length; i++) {
        $( buttonSelectors[i] + "2" ).click(function() {
            var isChecked = $('div#scheduleOrChangesSessionDiv span:first-of-type').hasClass('on');
            var groupName = groupNames[buttonSelectors.indexOf("#" + this.id.substring(0, this.id.length-1))];
            var groupId = sessionGroupIds[buttonSelectors.indexOf("#" + this.id.substring(0, this.id.length-1))];
            
            //alert(groupName + "@" + groupId);

            if (isChecked)
            {
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                var dialogWidth = 590;
                $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                var path = '_php/includes/SessionSchedule.php?groupId=' + groupId;
                $('#scheduleBox').load(path);
            }
            else
            {
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                var dialogWidth = 590;
                $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                var path = '_php/includes/SessionScheduleChanges.php?groupId=' + groupId;
                $('#scheduleBox').load(path);
            }
        });
    }
    /* Кнопки для расписания сессии */

    /* Кнопки для таблиц аудиторий по корпусам */
    $( "#Mol" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var dialogDate = $.datepicker.formatDate("dd.mm.yy", $( "#scheduleDate" ).datepicker( "getDate" ));
        var path = '_php/includes/Auditoriums.php?building=' + buildingsIndexes["Mol"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dialogDate + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#Jar" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var dialogDate = $.datepicker.formatDate("dd.mm.yy", $( "#scheduleDate" ).datepicker( "getDate" ));
        var path = '_php/includes/Auditoriums.php?building=' + buildingsIndexes["Jar"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 3 (" + dialogDate + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#Other" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var dialogDate = $.datepicker.formatDate("dd.mm.yy", $( "#scheduleDate" ).datepicker( "getDate" ));
        var path = '_php/includes/Auditoriums.php?building=' + buildingsIndexes["Other"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Прочие (" + dialogDate + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });

    $( "#MolPlus" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var dialogDate = $.datepicker.formatDate("dd.mm.yy", $( "#scheduleDate" ).datepicker( "getDate" ));
        var path = '_php/includes/Auditoriums.php?building=' + buildingsIndexes["Mol"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dialogDate + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path, function() {
            var path2 = '_php/includes/Auditoriums.php?building=' + schoolBuildings["Mol"] + '&date="' + dateString + '"' + '&dbPrefix=s_';
            $('#schoolAuds').load(path2);
        });
    });
    $( "#JarPlus" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var dialogDate = $.datepicker.formatDate("dd.mm.yy", $( "#scheduleDate" ).datepicker( "getDate" ));
        var path = '_php/includes/Auditoriums.php?building=' + buildingsIndexes["Jar"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dialogDate + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path, function() {
            var path2 = '_php/includes/Auditoriums.php?building=' + schoolBuildings["Jar"] + '&date="' + dateString + '"' + '&dbPrefix=s_';
            $('#schoolAuds').load(path2);
        });
    });
    /* Кнопки для таблиц аудиторий по корпусам */


    $("input#scheduleOrChanges").switchButton({
        checked : false,
        on_label: "ИЗМЕНЕНИЯ",
        off_label: "РАСПИСАНИЕ",
        width: 50,
        height: 15,
        clear: true,
        on_toggle: function() {
            var isChecked = $('div#scheduleOrChangesDiv span:first-of-type').hasClass('on');
            if (isChecked)
            {
                $("input#DayOrWeek").switchButton({
                    checked : false,
                    on_label: "НА НЕДЕЛЮ",
                    off_label: "НА КОНКРЕТНЫЙ ДЕНЬ",
                    width: 50,
                    height: 15,
                    clear: true
                });
            }
            else
            {
                $("input#DayOrWeek").switchButton({
                    checked : false,
                    on_label: "НА ДАТУ",
                    off_label: "ВСЕГО РАСПИСАНИЯ",
                    width: 50,
                    height: 15,
                    clear: true
                });
            }
        }
    });

    $("input#DayOrWeek").switchButton({
        checked : false,
        on_label: "НА НЕДЕЛЮ",
        off_label: "НА КОНКРЕТНЫЙ ДЕНЬ",
        width: 50,
        height: 15,
        clear: true
    });

    $("input#scheduleOrChangesSession").switchButton({
        checked : false,
        on_label: "ИЗМЕНЕНИЯ",
        off_label: "РАСПИСАНИЕ",
        width: 50,
        height: 15,
        clear: true,
        enabled: false
    });
});

$(function() {
    $( "#studentGroups" ).click(function() {
        var path = '_php/includes/StudentGroups.php';
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: "Группы студентов", minHeight : "50px", position: ['center',20] } );
            $( "#groupsList" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#groupsList").val();
                var path = '_php/includes/StudentGroup.php?id=' + groupId;
                $('#groupList').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#groupsList').trigger('change');
        });
    });

    $( "#planGroups" ).click(function() {
        var path = '_php/includes/planGroups.php';
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: "Дисциплины по группам", minHeight : "50px", position: ['center',20] } );
            $( "#groupsPlanList" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#groupsPlanList").val();
                var path = '_php/includes/planGroup.php?id=' + groupId;
                $('#planGroup').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#groupsPlanList').trigger('change');
        });
    });

    $( "#planByTeacher" ).click(function() {
        var path = '_php/includes/planTeachers.php';
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 900, title: "Дисциплины по преподавателям", minHeight : "50px", position: ['center',20] } );
            $("#teachersPlanList").change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#teachersPlanList").val();
                var path = '_php/includes/planTeacher.php?id=' + groupId;

                $('#planTeacher').load(path, function() {
                    $('#progress').empty();
                });
            });
            $("#teachersPlanList").trigger('change');
        });
    });


    $('div#vkGroupLink').click(function() {
            window.location = "https://vk.com/nayanovadisp";
    });

    $('div#vkGroupLink').hover(function() {
        $(this).css('cursor','pointer');
    });

    $( "#sessionByDate" ).click(function() {
        var path = '_php/includes/SessionDates.php';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "Сессия по датам", minHeight : "50px", position: ['center',20] } );
            $( "#sessionDate" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var date = $("#sessionDate").val();
                var path = '_php/includes/SessionByDate.php?date=' + date;
                $('#SessionList').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#sessionDate').trigger('change');
        });
    });

    $( "#MathG" ).click(function() {
        var path = '_php/includes/html/graduates.html #MathEvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "16 А", minHeight : "50px", position: ['center',20] } );
        });
    });
    $( "#PhilG" ).click(function() {
        var path = '_php/includes/html/graduates.html #PhilEvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "15 Б", minHeight : "50px", position: ['center',20] } );
        });
    });
    $( "#BioG" ).click(function() {
        var path = '_php/includes/html/graduates.html #BioEvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "15 В", minHeight : "50px", position: ['center',20] } );
        });
    });

    $( "#EconG" ).click(function() {
        var path = '_php/includes/html/graduates.html #EconEvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "14 Г(Н) + 15 Г", minHeight : "50px", position: ['center',20] } );
        });
    });

    $( "#LawG" ).click(function() {
        var path = '_php/includes/html/graduates.html #LawEvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "14 Д(Н) + 15 Д", minHeight : "50px", position: ['center',20] } );
        });
    });

    $( "#PRG" ).click(function() {
        var path = '_php/includes/html/graduates.html #PREvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "15 Е", minHeight : "50px", position: ['center',20] } );
        });
    });

    $( "#UprG" ).click(function() {
        var path = '_php/includes/html/graduates.html #UprEvents';
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "16 У", minHeight : "50px", position: ['center',20] } );
        });
    });

    $('a#logoutLink').click(function(e) {
        var $this = $(this);
        e.preventDefault();
        $.post('', {'logout': '1'}, function() {
            window.location.reload(false);
        });
    });

    $( "#showTeachersSchedule" ).click(function() {
        var teacherId = jQuery("#teacherList option:selected").val();
        var teacherName = jQuery("#teacherList option:selected").text();
        var path = '_php/includes/TeacherSchedule.php?teacherId=' + teacherId;

        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: teacherName, minHeight : "50px"} );
        });
    });

    $.countdown.setDefaults($.countdown.regionalOptions['ru']);

    var summer = new Date(2017, 6 - 1, 1);
    $('#summer').countdown({until: summer});
	
	$( document ).tooltip();
});

