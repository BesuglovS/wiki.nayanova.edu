$(function() {
    function ReloadGamesList() {
        $("div#gameListDiv").load('GameListHtml.php', function() {
            $('#gameList').click(function () {
                $("#gameName").val($('#gameList').val());
            });
        });
    }

    $('#refreshGameList').click(function() {
        ReloadGamesList();
    });

    function SetMakeMoveHandler() {
        $("button#makeMove").click(function() {
            $.get("MakeMove.php?" +
                "id=" + $("input#gameName").val() +
                "&password=" + $("input#password").val() +
                "&side=" + $("#Side").find("option:selected").text().substr(6,1) +
                "&move=" + $("#move").val(),
                function (data) {
                    var error = false;

                    if (data == "Игрок уже выиграл!") {
                        error = true;

                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: 'Игрок уже выиграл!'
                        });

                    }

                    if (data == "Игры с таким именем не существует.") {
                        error = true;

                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: 'Игра с таким именем не существует.'
                        });

                    }
                    if (data == "Не удалось подключится. Неверный пароль.") {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_DANGER,
                            message: 'Не удалось подключится. Неверный пароль.'
                        });
                    }

                    if (data == "Нельзя ходить пока соперник не подключился.") {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_DANGER,
                            message: 'Нельзя ходить пока соперник не подключился.'
                        });
                    }

                    if (data == "Сейчас не ваш ход.") {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: "Сейчас не ваш ход."
                        });
                    }

                    if (data.startsWith("Неверная начальная буква."))
                    {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: data
                        });
                    }

                    if (data == "Такого города не существует.")
                    {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: 'Такого города не существует.'
                        });
                    }

                    if (data == "Этот город уже был.")
                    {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: 'Этот город уже был.'
                        });
                    }


                    if (!error) {
                        var autoFlip = $("#autoFlip").find("option:selected").text();

                        //var wins = JSON.parse(data);

                        if (autoFlip == "Да")
                        {
                            if ($("#Side").find("option:selected").text() == "Игрок 1")
                            {
                                /*
                                if (wins["secondPlayerWins"] == "0") {
                                    $("#Side").val("Игрок 2");
                                }*/
                                $("#Side").val("Игрок 2");
                            }
                            else
                            {
                                /*
                                if (wins["firstPlayerWins"] == "0") {
                                    $("#Side").val("Игрок 1");
                                }*/
                                $("#Side").val("Игрок 1");
                            }
                        }

                        ShowGame("0");
                    }
                }
            );
        });

        $("input#move").keypress(function(e) {
            if(e.which == 13) {
                $( "button#makeMove" ).trigger( "click" );
            }
        });
    }

    $("button#createGame").click(function() {
        var gameId = $("input#gameName").val();
        var Side = $("#Side").find("option:selected").text();
        var flip = $("#autoFlip").find("option:selected").text();
        var num = $("#num").val();

        var autoFlip = "";
        if (flip == "Да")
        {
            autoFlip = "1";
        }
        if (flip == "Нет")
        {
            autoFlip = "0";
        }

        Side = Side.substr(6, 1);

        $.get("CreateGame.php" +
            "?id=" + gameId +
            "&Side=" + Side +
            "&autoFlip=" + autoFlip,
            function( data ) {
                var error = false;

                if (data == "Game with specified Id already exists in database")
                {
                    erorr = true;
                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_WARNING,
                        message: 'Игра с таким именем уже существует.'
                    });
                }

                if (!error)
                {
                    var GameCreatedData = JSON.parse(data);
                    var Side = $("#Side").find("option:selected").text();

                    $("#Side").prop('disabled', 'disabled');

                    var userPassword = "";
                    if (Side == "Игрок 1")
                    {
                        userPassword = GameCreatedData["Pass1"];
                    }
                    if (Side == "Игрок 2")
                    {
                        userPassword = GameCreatedData["Pass2"];
                    }

                    $("#password").val(userPassword);

                    var gameId = GameCreatedData["GameIdName"];
                    $("#gameName").val(gameId);

                    var showPath = 'ShowGame.php' +
                        '?id=' + encodeURIComponent($("input#gameName").val()) +
                        '&password=' + $("input#password").val() +
                        '&Side=' + Side;

                    $.get(showPath,
                        function(data) {
                            $("div#board").html(data);

                            BootstrapDialog.show({
                                title: 'Gut :-)',
                                type: BootstrapDialog.TYPE_SUCCESS,
                                message: 'Игра создана успешно. Ваш пароль к этой игре: ' + userPassword + '. '
                            });

                            ReloadGamesList();
                            SetMakeMoveHandler();

                            window.updateTimerId = setInterval(function(){
                                ShowGame("0");
                                ReloadGamesList();
                            }, 30000 );
                        }
                    );
                }
            }
        );
    });

    $("button#connectToGame").click(function() {
        var side = $("#Side").find("option:selected").text();
        side = side.substr(6,1);

        $.get('Connect.php?' +
            'id=' + $("input#gameName").val() +
            "&password=" + $("input#password").val()
            +"&Side=" + side,
            function (data) {
                console.log(data);

                var error = false;
                if (data == "Игры с таким именем не существует.") {
                    error = true;

                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_WARNING,
                        message: 'Игра с таким именем не существует.'
                    });

                }
                if (data == "Сторона уже занята. Неверный пароль.") {
                    error = true;

                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_DANGER,
                        message: 'Сторона уже занята. Неверный пароль.'
                    });
                }

                if (!error) {
                    if (data == "Success")
                    {
                        BootstrapDialog.show({
                            title: 'Gut :-)',
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: 'Подключение успешно.'
                        });
                    }
                    else
                    {
                        var password = JSON.parse(data);

                        var userPassword = "";
                        if (side == "1") {
                            userPassword = password["Pass1"];
                        }
                        if (side == "2") {
                            userPassword = password["Pass2"];
                        }

                        $("#password").val(userPassword);

                        $("#password").val(userPassword);

                        BootstrapDialog.show({
                            title: 'Gut :-)',
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: 'Подключение успешно. Ваш пароль к этой игре: ' + userPassword + '. '
                        });

                        clearInterval(window.updateTimerId);

                        window.updateTimerId = setInterval(function(){
                            ShowGame("0");
                            ReloadGamesList();
                        }, 30000 );
                    }

                    ShowGame("0");
                }
            }
        );
    });

    function ShowGame(watch) {
        var path = "";

        var side = $("#Side").find("option:selected").text().substr(6,1);

        if (watch != "1")
        {
            path = 'ShowGame.php' +
                '?id=' + encodeURIComponent($("input#gameName").val()) +
                '&password=' + $("input#password").val() +
                '&Side=' + side;
        }
        else
        {
            path = 'ShowGame.php?id=' + $("input#gameName").val()
            +"&watch=1";
        }

        $.get(path,
            function (data) {
                var error = false;
                if (data == "Игры с таким именем не существует.") {
                    error = true;

                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_WARNING,
                        message: 'Игра с таким именем не существует.'
                    });
                }
                if (data == "Не удалось подключится. Неверный пароль.") {
                    error = true;

                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_DANGER,
                        message: 'Не удалось подключится. Неверный пароль.'
                    });
                }

                if (!error) {
                    $("div#board").html(data);

                    SetMakeMoveHandler();

                    $("#refreshMoveList").click(function() {
                        ShowGame(watch);
                    });
                }
            }
        );
    }

    $("button#refreshGame").click(function() {
        ShowGame("0");
    });

    $("button#watchRefreshGame").click(function() {
        var gameId = $("input#gameName").val();

        $.get( "ShowGame.php?id=" + gameId + "&watch=1",
            function( data ) {
                var error = false;
                if (data == "Игры с таким именем не существует.") {
                    error = true;

                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_WARNING,
                        message: 'Игра с таким именем не существует.'
                    });
                }

                if (!error) {
                    $("div#board").html(data);
                }

            }
        );
    });


    /*
    $(document).bind('keydown', 'ctrl+q', function() {
        var gameId = $("input#gameName").val();
        var side = $("#Side").find("option:selected").text().substr(6,1);

        $.get( "GetAdvise.php?id=" + gameId + "&side=" + side,
            function( data ) {
                var advise = JSON.parse(data);
                var count = advise["count"];
                var city = advise["city"];

                BootstrapDialog.show({
                    title: 'Подсказка',
                    type: BootstrapDialog.TYPE_INFO,
                    message: 'Количество вариантов = ' + count + '\n' +
                    'Потенциальный ход = ' + city
                });
            }
        );
    });
    */


    ReloadGamesList();
});