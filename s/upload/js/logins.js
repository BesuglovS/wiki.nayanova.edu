$(function() {
    var dialog, form;

    function addUser() {
        var login = $("#name").val();
        var pass =  $("#password").val();

        $.post('AddAccount.php', { Login: login, Pass : pass}, function() {
            window.location.reload(false);
        });
        return true;
    }

    dialog = $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 230,
        width: 350,
        modal: true,
        buttons: {
            "Создать": function() {
                addUser();
                dialog.dialog( "close" );
            },
            Cancel: function() {
                dialog.dialog( "close" );
            }
        },
        close: function() {
            form[ 0 ].reset();
            allFields.removeClass( "ui-state-error" );
        }
    });

    form = dialog.find( "form" ).on( "submit", function( event ) {
        event.preventDefault();
        addUser();

    });

    $( "#addAccount" ).button().on( "click", function() {
        dialog.dialog( "open" );
    });
});

function RemoveAccount(Login) {
    if (Login == "bs")
    {
        $("<div title=\"Ха-ха!!!\"><p> Наивная)))</p></div>").dialog({
            modal: true,
            buttons: {
                OK: function() {
                    $( this ).dialog( "close" );
                }
            }
        });

        return;
    }

    $.post('RemoveAccount.php', { Login: Login }, function() {
        window.location.reload(false);
    });
}

function ChangePassword(Login) {
    if (Login == "bs")
    {
        $("<div title=\"Ха-ха!!!\"><p> Наивная)))</p></div>").dialog({
            modal: true,
            buttons: {
                OK: function() {
                    $( this ).dialog( "close" );
                }
            }
        });

        return;
    }

    var pass = $("input#" + Login).val();
    $.post('ChangePassword.php', { Login: Login, Pass: pass }, function() {
        window.location.reload(false);
    });
}