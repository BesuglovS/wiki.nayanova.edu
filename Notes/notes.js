$(function(){
    var goButton = $("button#go");
    var inputFilter = $("input#filter");

    goButton.click(function() {
        var filter = inputFilter.val();

        var path = 'notes.php?filter=' + encodeURIComponent(filter);

        $('#notes').load(path);
    });

    inputFilter.keypress(function(e) {
        if(e.which == 13) {
            goButton.trigger( "click" );
        }
    });
});