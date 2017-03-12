var dbPrefix = 's_';

var groupNames = [
    "8 А", "9 А", "10 А", "11 А1", "11 А2",
    "8 Б", "9 Б", "10 Б", "11 Б",
    "8 В", "9 В", "10 В", "11 В",
    "8 Г", "9 Г", "10 Г", "11 Г",
    "1 А", "2 А", "3 А", "4 А", "5 А", "6 А", "7 А",
    "1 Б", "2 Б", "3 Б", "4 Б", "5 Б", "6 Б", "7 Б",
    "1 В", "2 В", "3 В", "4 В", "5 В", "6 В", "7 В",
    "1 Г", "2 Г", "3 Г", "4 Г", "5 Г", "6 Г", "7 Г",
    "1 Д", "2 Д", "6 Д", "7 Д"
];
var groupIds = [
    "59", "63", "69", "75", "76",
    "60", "66", "72", "77",
    "61", "67", "73", "80",
    "62", "68", "74", "83",
    "1", "6", "17", "25", "35", "45", "52",
    "2", "7", "18", "28", "36", "46", "53",
    "3", "8", "19", "29", "39", "47", "54",
    "4", "11","22", "32", "42", "48", "55",
    "5", "14", "51", "58"
];

var buttonSelectors = [
    "#8Math", "#9Math", "#10Math", "#11Math1", "#11Math2",
    "#8Hum", "#9Hum", "#10Hum", "#11Hum",
    "#8Eco", "#9Eco", "#10Eco", "#11Eco",
    "#8Econ", "#9Econ", "#10Econ", "#11Econ",
    "#1A", "#2A", "#3A", "#4A", "#5A", "#6A", "#7A",
    "#1B", "#2B", "#3B", "#4B", "#5B", "#6B", "#7B",
    "#1V", "#2V", "#3V", "#4V", "#5V", "#6V", "#7V",
    "#1G", "#2G", "#3G", "#4G", "#5G", "#6G", "#7G",
    "#1D", "#2D", "#6D", "#7D"
];

var buildingsIndexes = [];
buildingsIndexes["Mol"] = 1;
buildingsIndexes["Cha"] = 2;
buildingsIndexes["Jar"] = 3;

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

    $( "#scheduleDate" ).datepicker( "option", "minDate", new Date(2017, 1 - 1,  9));
    $( "#scheduleDate" ).datepicker( "option", "maxDate", new Date(2017, 6 - 1, 4));

    $.datepicker.setDefaults($.datepicker.regional['ru']);
    /* Datepicker #scheduleDate */

    /* Today / tomorrow buttons */
    $( "button#today" ).click(function() { $("#scheduleDate").datepicker("setDate", "today"); });
    $( "button#tomorrow" ).click(function() { $("#scheduleDate").datepicker("setDate", "1"); });
    /* Today / tomorrow buttons */

    $( "#PDFExport" ).click(function() {
        var faculty = $('#facultiesList').val();
        var dow = $('#dowPDFSelect').val();
        window.location = '../pdfExport.php?facultyId=' + faculty + '&dow=' + dow + '&dbPrefix=' + dbPrefix;
    });

    $( "#DOWSchedule" ).click(function() {
        var faculty = $('#facultiesList').val();
        var dow = $('#dowPDFSelect').val();
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {"width": dialogWidth, title: dowRU[dow], minHeight : "50px" , position: ['center',20]} );
        var path = '../_php/includes/FacultyDOWSchedule.php?facultyId=' + faculty + '&dow=' + dow + '&dbPrefix=' + dbPrefix;
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
                        var path = '../_php/includes/TeacherSchedule.php?teacherId=' + ui.item.option.value + '&dbPrefix=' + dbPrefix;
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

                    $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
                    var dialogWidth = ($(window).width()*0.95 > 1000)? 1000 : $(window).width()*0.95;
                    $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                    var path = '../_php/includes/GroupWeekSchedule.php?groupId=' + groupId + '&dbPrefix=' + dbPrefix;
                    $('#scheduleBox').load(path);
                }
                else
                {
                    var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
                    dateString = IfDatePickerIsEmptySetToday(dateString);
                    $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
                    $('#scheduleBox').dialog( {width: 600, title: groupName + " (" + dateString + ")", minHeight : "50px" , position: ['center',20]} );
                    var path = '../_php/includes/DailySchedule.php?groupId="' + groupId + '"&date="' + dateString + '"' + '&dbPrefix=' + dbPrefix;
                    $('#scheduleBox').load(path);
                }
            }
            else
            {
                var isSecondChecked = !$('div#DayOrWeekDiv span:first-of-type').hasClass('on');
                var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
                dateString = IfDatePickerIsEmptySetToday(dateString);
                var path = '../_php/includes/Changes.php?groupId=' + groupId + "&date=" + dateString + "&tomorrow=" + isSecondChecked + '&dbPrefix=' + dbPrefix;
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
                $('#scheduleBox').dialog( {width: 600, title: groupName, minHeight : "50px" , position: ['center',20]} );
                $('#scheduleBox').load(path, function() {
                    $( "#eventsIndexList" ).change(function() {
                        $('#progress').prepend('<img id="loading" height="16" width="16" src="../upload/images/ajax-loader.gif" />')
                        var pagingId = $("#eventsIndexList").val();
                        groupChangesPath = "../_php/includes/GroupChanges.php?groupId=" + groupId +
                            "&date=" + dateString + "&startFrom=" + pagingId  +
                            "&tomorrow=" + isSecondChecked + '&dbPrefix=' + dbPrefix;
                        $('#eventList').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
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
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
                var dialogWidth = 590;
                $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                var path = '../_php/includes/SessionSchedule.php?groupId=' + groupId + '&dbPrefix=' + dbPrefix;
                $('#scheduleBox').load(path);
            }
            else
            {
                $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
                var dialogWidth = 590;
                $('#scheduleBox').dialog( {width: dialogWidth, title: groupName, minHeight : "50px" , position: ['center',20]} );
                var path = '../_php/includes/SessionScheduleChanges.php?groupId=' + groupId + '&dbPrefix=' + dbPrefix;
                $('#scheduleBox').load(path);
            }
        });
    }
    /* Кнопки для расписания сессии */

    /* Кнопки для таблиц аудиторий по корпусам */
    $( "#Mol" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '../_php/includes/Auditoriums.php?building=Mol&date="' + dateString + '"' + '&dbPrefix=' + dbPrefix;
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="../upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });

    $( "#Cha" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '../_php/includes/Auditoriums.php?building=' + buildingsIndexes["Cha"] + '&date="' + dateString + '"' + '&dbPrefix=' + dbPrefix;
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 1 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#Mol" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '../_php/includes/Auditoriums.php?building=' + buildingsIndexes["Mol"] + '&date="' + dateString + '"' + '&dbPrefix=' + dbPrefix;
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 2 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
    });
    $( "#Jar" ).click(function() {
        var dateString = $.datepicker.formatDate("yy-mm-dd", $( "#scheduleDate" ).datepicker( "getDate" ));
        dateString = IfDatePickerIsEmptySetToday(dateString);
        var path = '../_php/includes/Auditoriums.php?building=' + buildingsIndexes["Jar"] + '&date="' + dateString + '"' + '&dbPrefix=' + dbPrefix;
        $('#scheduleBox').html('<div style="text-align: center"><img id="loading" height="100" width="100" src="upload/images/ajax-loader2.gif" /></div>');
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').dialog( {width: dialogWidth, title: "Корпус № 3 (" + dateString + ")", minHeight : "50px", position: ['center',20]} );
        $('#scheduleBox').load(path);
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
        var path = '../_php/includes/StudentGroups.php' + '?dbPrefix=' + dbPrefix;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: "Группы студентов", minHeight : "50px", position: ['center',20] } );
            $( "#groupsList" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="../upload/images/ajax-loader.gif" />')
                var groupId = $("#groupsList").val();
                var path = '../_php/includes/StudentGroup.php?id=' + groupId + '&dbPrefix=' + dbPrefix;
                $('#groupList').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#groupsList').trigger('change');
        });
    });

    $( "#planGroups" ).click(function() {
        var path = '../_php/includes/planGroups.php' + '?dbPrefix=' + dbPrefix;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 600, title: "Дисциплины по группам", minHeight : "50px", position: ['center',20] } );
            $( "#groupsPlanList" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="../upload/images/ajax-loader.gif" />')
                var groupId = $("#groupsPlanList").val();
                var path = '../_php/includes/planGroup.php?id=' + groupId + '&dbPrefix=' + dbPrefix;
                $('#planGroup').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#groupsPlanList').trigger('change');
        });
    });

    $( "#planByTeacher" ).click(function() {
        var path = '../_php/includes/planTeachers.php' + '?dbPrefix=' + dbPrefix;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: 900, title: "Дисциплины по преподавателям", minHeight : "50px", position: ['center',20] } );
            $("#teachersPlanList").change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var groupId = $("#teachersPlanList").val();
                var path = '../_php/includes/planTeacher.php?id=' + groupId + '&dbPrefix=' + dbPrefix;

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
        var path = '../_php/includes/SessionDates.php' + '?dbPrefix=' + dbPrefix;
        var dialogWidth = ($(window).width()*0.95 > 900)? 900 : $(window).width()*0.95;
        $('#scheduleBox').load(path, function() {
            $('#scheduleBox').dialog( {width: dialogWidth, title: "Сессия по датам", minHeight : "50px", position: ['center',20] } );
            $( "#sessionDate" ).change(function() {
                $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
                var date = $("#sessionDate").val();
                var path = '../_php/includes/SessionByDate.php?date=' + date + '&dbPrefix=' + dbPrefix;
                $('#SessionList').load(path, function() {
                    $('#progress').empty();
                });
            });
            $('#sessionDate').trigger('change');
        });
    });

    $('a#logoutLink').click(function(e) {
        var $this = $(this);
        e.preventDefault();
        $.post('', {'logout': '1'}, function() {
            window.location.reload(false);
        });
    });

    var summer = new Date(2017, 6 - 1, 1);
    $('#summer').countdown({until: summer});

    $( document ).tooltip();
});

