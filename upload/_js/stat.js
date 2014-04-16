$(function() {
    $( "#statEventsIndexList" ).change(function() {
        $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
        var pagingId = $("#statEventsIndexList").val();
        eventsPath = "_php/includes/statsLoad.php?startFrom=" + pagingId;
        $('#statEventList').load(eventsPath, function() {
            $('#progress').empty();
        });
    });
    $('#statEventsIndexList').trigger('change');
});

$(function() {
    $( "#LogEventsIndexList" ).change(function() {
        $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />')
        var pagingId = $("#LogEventsIndexList").val();
        eventsPath = "_php/includes/LogLoad.php?startFrom=" + pagingId;
        $('#LogEventList').load(eventsPath, function() {
            $('#progress').empty();
        });
    });
    $('#LogEventsIndexList').trigger('change');
});
