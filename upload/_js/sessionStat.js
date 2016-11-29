$(function() {
    $( "#statEventsIndexList" ).change(function() {
        $('#progress').prepend('<img id="loading" height="16" width="16" src="upload/images/ajax-loader.gif" />');
        var pagingId = $("#statEventsIndexList").val();
        eventsPath = "_php/includes/sessionStatsLoad.php?startFrom=" + pagingId;
        $('#statEventList').load(eventsPath, function() {
            $('#progress').empty();
        });
    });
    $('#statEventsIndexList').trigger('change');
});
