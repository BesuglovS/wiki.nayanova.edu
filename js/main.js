var Attestation = ["-", "Зачёт", "Экзамен", "Зачёт + Экзамен", "Зачёт с оценкой"];

(function() {
    $(function() {
        $( "#nuSite" ).click(function() {
            window.location = "http://nayanova.edu";
        });

        /* Datepicker #scheduleDate */
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
            dateFormat: 'dd.mm.yy', firstDay: 1,
            initStatus: '', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['ru']);

        $( "#scheduleDate" ).datepicker();        
        $( "#buildingDate" ).datepicker();

        var minDate = new Date(2016, 2 - 1, 1, 0, 0, 0, 0);
        var maxDate = new Date(2016, 6 - 1, 5, 23, 59, 59, 900);
        var today = new Date();

        $( "#scheduleDate" ).datepicker( "option", "minDate", minDate);
        $( "#scheduleDate" ).datepicker( "option", "maxDate", maxDate);
        
        $( "#buildingDate" ).datepicker( "option", "minDate", minDate);
        $( "#buildingDate" ).datepicker( "option", "maxDate", maxDate);


        if ((today >= minDate) && (today <= maxDate)) {
            $('#scheduleDate').datepicker("setDate", today);
            $('#buildingDate').datepicker("setDate", today);
        } else
        {
            if (today <= minDate)
            {
                $('#scheduleDate').datepicker("setDate", minDate);
                $('#buildingDate').datepicker("setDate", minDate);
            }
            else
            {
                $('#scheduleDate').datepicker("setDate", maxDate);
                $('#buildingDate').datepicker("setDate", maxDate);
            }
        }


        /* Datepicker #scheduleDate */
    });
})();
