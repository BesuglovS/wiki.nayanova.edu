var dbPrefix = 'old_';
var groupNames = [
    "12 А", "13 А", "14 А",
    "12 Б", "13 Б", "14 Б", "15 Б", "16 Б",
    "12 В", "13 В", "14 В", "15 В", "16 В",
    "12 Г", "12 Г(Н)", "13 Г", "13 Г(Н)", "14 Г", "14 Г(Н)", "15 Г", "16 Г",
    "12 Д", "13 Д", "13 Д(Н)", "14 Д", "15 Д",  "16 Д",
    "12 Е", "12 Е(Н)", "13 Е", "14 Е", "15 Е",
    "12 У", "13 У", "14 У", "15 У",
    "12 Т", "13 Т", "14 Т", "15 Т",
    "1 АГ", "1 АД",
    "2 АА", "2 АБ", "2 АВ", "2 АГ", "2 АД",
    "3 АА", "3 АД"];
var groupIds = [
    "26", "1", "2",
    "27", "3", "4", "5", "144",
    "28", "6", "7", "8", "9",
    "29", "34", "10", "35", "11", "36", "12", "145",
    "30", "13", "38", "14", "15", "146",
    "31", "137", "16", "17", "18",
    "32", "19", "20", "21",
    "33", "23", "24", "25",
    "154", "155",
    "157", "158", "159", "160", "161",
    "162", "164"];

var buttonSelectors = [
    "#12Math", "#13Math", "#14Math",
    "#12Phil", "#13Phil", "#14Phil", "#15Phil", "#16Phil",
    "#12Eco", "#13Eco", "#14Eco", "#15Eco", "#16Eco",
    "#12Econ", "#12EconN", "#13Econ", "#13EconN", "#14Econ", "#14EconN", "#15Econ", "#16Econ",
    "#12Law", "#13Law", "#13LawN", "#14Law", "#15Law", "#16Law",
    "#12PR", "#12PRN", "#13PR", "#14PR", "#15PR",
    "#12Upr", "#13Upr", "#14Upr", "#15Upr",
    "#12Tur", "#13Tur", "#14Tur", "#15Tur",
    "#1AEcon", "#1ALaw",
    "#2AMath", "#2APhil", "#2AEco", "#2AEcon", "#2ALaw",
    "#3AMath", "#3ALaw"];

var buildingsIndexes = new Array();
buildingsIndexes["Mol"] = 2;
buildingsIndexes["Jar"] = 3;
buildingsIndexes["Other"] = 4;
buildingsIndexes["SSU"] = 5;

var dowRU = new Array("","Понедельник","Вторник", "Среда", "Четверг", "Пятница", "Суббота", "Воскресенье");

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

    $( "#scheduleDate" ).datepicker( "option", "minDate", new Date(2014, 9 - 1, 1));
    $( "#scheduleDate" ).datepicker( "option", "maxDate", new Date(2014, 12 - 1, 31));

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
        dateString = IfDatePickerIsEmptySetToday(dateString);
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        $('#scheduleBox').dialog( {width: 600, title: dateString , minHeight : "50px" , position: ['center',20]} );
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

/* TeacherList Combobox Autocomplete */
(function( $ ) {
    $.widget( "custom.combobox", {
        _create: function() {
            this.wrapper = $( "<span>" )
                .addClass( "custom-combobox" )
                .insertAfter( this.element );
            this.element.hide();
            this._createAutocomplete();
            this._createShowAllButton();
        },
        _createAutocomplete: function() {
            var selected = this.element.children( ":selected" ),
                value = selected.val() ? selected.text() : "";
            this.input = $( "<input>" )
                .appendTo( this.wrapper )
                .val( value )
                .attr( "title", "" )
                .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    source: $.proxy( this, "_source" ),
                    select: function( event, ui ) {
                        var path = '_php/includes/TeacherSchedule.php?teacherId=' + ui.item.option.value;
                        $('#scheduleBox').load(path, function() {
                            $('#scheduleBox').dialog( {width: 600, title: ui.item.value, minHeight : "50px"} );
                        });
                    }
                })
                .tooltip({
                    tooltipClass: "ui-state-highlight"
                });
            this._on( this.input, {
                autocompleteselect: function( event, ui ) {
                    ui.item.option.selected = true;
                    this._trigger( "select", event, {
                        item: ui.item.option
                    });
                },
                autocompletechange: "_removeIfInvalid"
            });
        },
        _createShowAllButton: function() {
            var input = this.input,
                wasOpen = false;
            $( "<a>" )
                .attr( "tabIndex", -1 )
                .attr( "title", "" ) // "Show All Items"
                .tooltip()
                .appendTo( this.wrapper )
                .button({
                    icons: {
                        primary: "ui-icon-triangle-1-s"
                    },
                    text: false
                })
                .removeClass( "ui-corner-all" )
                .addClass( "custom-combobox-toggle ui-corner-right" )
                .mousedown(function() {
                    wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                })
                .click(function() {
                    input.focus();
                    // Close if already visible
                    if ( wasOpen ) {
                        return;
                    }
                    // Pass empty string as value to search for, displaying all results
                    input.autocomplete( "search", "" );
                });
        },
        _source: function( request, response ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
            response( this.element.children( "option" ).map(function() {
                var text = $( this ).text();
                if ( this.value && ( !request.term || matcher.test(text) ) )
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }) );
        },
        _removeIfInvalid: function( event, ui ) {
            // Selected an item, nothing to do
            if ( ui.item ) {
                return;
            }
            // Search for a match (case-insensitive)
            var value = this.input.val(),
                valueLowerCase = value.toLowerCase(),
                valid = false;
            this.element.children( "option" ).each(function() {
                if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                    this.selected = valid = true;
                    return false;
                }
            });
            // Found a match, nothing to do
            if ( valid ) {
                return;
            }
            // Remove invalid value
            this.input
                .val( "" )
                /*.attr( "title", value + " didn't match any item" )*/
                .attr( "title", value + " не найден" )
                .tooltip( "open" );
            this.element.val( "" );
            this._delay(function() {
                this.input.tooltip( "close" ).attr( "title", "" );
            }, 2500 );
            this.input.data( "ui-autocomplete" ).term = "";
        },
        _destroy: function() {
            this.wrapper.remove();
            this.element.show();
        }
    });
})( jQuery );
$(function() {
    $( "#teacherList" ).combobox();
});
/* TeacherList Combobox Autocomplete */

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
                    var path = '_php/includes/GroupWeekSchedule.php?dbPrefix=' + dbPrefix + '&groupId=' + groupId;
                    $('#scheduleBox').load(path);
                }
                else
                {
                    var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
                    dateString = IfDatePickerIsEmptySetToday(dateString);
                    $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                    $('#scheduleBox').dialog( {width: 600, title: groupName + " (" + dateString + ")", minHeight : "50px" , position: ['center',20]} );
                    var path = '_php/includes/DailySchedule.php?dbPrefix=' + dbPrefix + '&groupId="' + groupId + '"&date="' + dateString + '"';
                    $('#scheduleBox').load(path);
                }
            }
            else
            {
                var isSecondChecked = !$('div#DayOrWeekDiv span:first-of-type').hasClass('on');
                var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
                dateString = IfDatePickerIsEmptySetToday(dateString);
                var path = '_php/includes/Changes.php?dbPrefix=' + dbPrefix + '&groupId=' + groupId + "&date=" + dateString + "&tomorrow=" + isSecondChecked;
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                $('#scheduleBox').dialog( {width: 600, title: groupName, minHeight : "50px" , position: ['center',20]} );
                $('#scheduleBox').load(path, function() {
                    $( "#eventsIndexList" ).change(function() {
                        $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                        var pagingId = $("#eventsIndexList").val();
                        groupChangesPath = "_php/includes/GroupChanges.php?dbPrefix=' + dbPrefix + '&groupId=" + groupId +
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
            var groupId = groupIds[buttonSelectors.indexOf("#" + this.id.substring(0, this.id.length-1))];

            if (isChecked)
            {
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                var dialogWidth = 590;
                $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                var path = '_php/includes/SessionSchedule.php?dbPrefix=' + dbPrefix + '&groupId=' + groupId;
                $('#scheduleBox').load(path);
            }
            else
            {
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
                var dialogWidth = 590;
                $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                var path = '_php/includes/SessionScheduleChanges.php?dbPrefix=' + dbPrefix + '&groupId=' + groupId;
                $('#scheduleBox').load(path);
            }
        });
    }
    /* Кнопки для расписания сессии */

    /* Кнопки для таблиц аудиторий по корпусам */
    $( "#Mol" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '_php/includes/Auditoriums.php?dbPrefix=' + dbPrefix + '&building=' + buildingsIndexes["Mol"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#Jar" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '_php/includes/Auditoriums.php?dbPrefix=' + dbPrefix + '&building=' + buildingsIndexes["Jar"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 3 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#SSU" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '_php/includes/Auditoriums.php?dbPrefix=' + dbPrefix + '&building=' + buildingsIndexes["SSU"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Самарский государственный университет (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#Other" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '_php/includes/Auditoriums.php?dbPrefix=' + dbPrefix + '&building=' + buildingsIndexes["Other"] + '&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Прочие (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });

    $( "#MolPlus" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '_php/includes/Auditoriums.php?dbPrefix=' + dbPrefix + '&building=Mol&date="' + dateString + '"';
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path, function() {
            var path2 = '_php/includes/Auditoriums.php?building=Mol&date="' + dateString + '"' + '&dbPrefix=s_';
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
        var path = '_php/includes/StudentGroups.php?dbPrefix=' + dbPrefix;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: "Группы студентов", minHeight : "50px", position: ['center',20] } );
            $( "#groupsList" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#groupsList").val();
                var path = '_php/includes/StudentGroup.php?dbPrefix=' + dbPrefix + '&id=' + groupId;
                $('#groupList').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#groupsList').trigger('change');
        });
    });

    $( "#planGroups" ).click(function() {
        var path = '_php/includes/planGroups.php?dbPrefix=' + dbPrefix;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: "Дисциплины по группам", minHeight : "50px", position: ['center',20] } );
            $( "#groupsPlanList" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#groupsPlanList").val();
                var path = '_php/includes/planGroup.php?dbPrefix=' + dbPrefix + '&id=' + groupId;
                $('#planGroup').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#groupsPlanList').trigger('change');
        });
    });

    $( "#planByTeacher" ).click(function() {
        var path = '_php/includes/planTeachers.php?dbPrefix=' + dbPrefix;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 900, title: "Дисциплины по преподавателям", minHeight : "50px", position: ['center',20] } );
            $("#teachersPlanList").change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#teachersPlanList").val();
                var path = '_php/includes/planTeacher.php?dbPrefix=' + dbPrefix + '&id=' + groupId;

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
                var path = '_php/includes/SessionByDate.php?dbPrefix=' + dbPrefix + '&date=' + date;
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
});