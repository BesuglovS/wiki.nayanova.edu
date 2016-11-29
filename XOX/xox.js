$(function() {
    function ReloadGamesList() {
        $("div#gameListDiv").load('GameListHtml.php', function() {
            $('#gameList').click(function () {
                $("#gameName").val($('#gameList').val());
            });
        });

    }

    if (typeof String.prototype.startsWith != 'function') {
        // see below for better implementation!
        String.prototype.startsWith = function (str){
            return this.indexOf(str) === 0;
        };
    }

    $("button#createGame").click(function() {
        var gameId = $("input#gameName").val();
        var pass = $("input#password").val();
        var XO = $("#XO").find("option:selected").text();
        var flip = $("#autoFlip").find("option:selected").text();
        var autoFlip = "";
        if (flip == "Да")
        {
            autoFlip = "1";
        }
        if (flip == "Нет")
        {
            autoFlip = "0";
        }

        $.get( "CreateGame.php?id=" + gameId + "&password=" + pass + "&XO=" + XO + "&autoFlip=" + autoFlip,
            function( data ) {
            if (data == "Game with specified Id already exists in database")
            {
                BootstrapDialog.show({
                    title: 'Ошибочка',
                    type: BootstrapDialog.TYPE_WARNING,
                    message: 'Игра с таким именем уже существует.'
                });
            }
            else
            {
                var GameCreatedData = JSON.parse(data);
                var XO = $("#XO").find("option:selected").text();

                $("#XO").prop('disabled', 'disabled');

                var userPassword = "";
                if (XO == "X")
                {
                    userPassword = GameCreatedData["xPass"];
                }
                if (XO == "O")
                {
                    userPassword = GameCreatedData["oPass"];
                }

                $("#password").val(userPassword);

                var gameId = GameCreatedData["GameIdName"];
                $("#gameName").val(gameId);

                console.log(GameCreatedData);

                var showPath = 'ShowGame.php?id=' + encodeURIComponent($("input#gameName").val()) +
                    '&password=' + $("input#password").val() +
                    '&XO=' + XO
                    +"&MoveListIndex=-1 ";

                console.log();

                $.get(showPath,
                    function(data) {
                        $("div#board").html(data);

                        BootstrapDialog.show({
                            title: 'Gut :-)',
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: 'Игра создана успешно. Ваш пароль к этой игре: ' + userPassword
                        });

                        ReloadGamesList();
                        SetMakeMoveHandlers();

                        window.updateTimerId = setInterval(function(){
                            ShowGame("End", "0");
                            ReloadGamesList();
                        }, 30000 );
                    }
                );
            }
        });
    });

    function ShowGame(MoveListIndex, watch) {
        console.log("ShowGame " + watch);

        if (MoveListIndex == undefined)
        {
            MoveListIndex = -1;
        }

        console.log("ShowGame " + MoveListIndex);
        var path = "";
        if (watch != "1")
        {
            path = 'ShowGame.php?id=' + $("input#gameName").val() +
            "&password=" + $("input#password").val()
            +"&XO=" + $("#XO").find("option:selected").text()
            +"&MoveListIndex=" + MoveListIndex;
        }
        else
        {
            path = 'ShowGame.php?id=' + $("input#gameName").val()
            +"&watch=1"
            +"&MoveListIndex=" + MoveListIndex;
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

                    SetMakeMoveHandlers();

                    var movesList = $("#movesList");

                    $("#toBegin").click(function() {
                        ShowGame("0", watch);
                    });

                    $("#toEnd").click(function() {
                        ShowGame("End", watch);
                    });

                    $("#showPrevMove").click(function() {
                        var index = movesList[0].selectedIndex - 1;
                        ShowGame(index, watch);
                    });

                    $("#showNextMove").click(function() {
                        var index = $("#movesList")[0].selectedIndex + 1;
                        ShowGame(index, watch);
                    });

                    $("#refreshMoveList").click(function() {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, watch);
                    });

                    movesList.click(function () {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, watch);
                    });
                }
            }
        );
    }

    $("button#connectToGame").click(function() {
        $.get('Connect.php?id=' + $("input#gameName").val() + "&password=" + $("input#password").val()
            +"&XO=" + $("#XO").find("option:selected").text(),
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
                if (data == "Знак уже занят. Неверный пароль.") {
                    error = true;

                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_DANGER,
                        message: 'Знак уже занят. Неверный пароль.'
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

                        var XO = $("#XO").find("option:selected").text();
                        var userPassword = "";
                        if (XO == "X") {
                            userPassword = password["xPass"];
                        }
                        if (XO == "O") {
                            userPassword = password["oPass"];
                        }

                        $("#password").val(userPassword);

                        BootstrapDialog.show({
                            title: 'Gut :-)',
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: 'Подключение успешно. Ваш пароль к этой игре: ' + userPassword
                        });

                        clearInterval(window.updateTimerId);

                        window.updateTimerId = setInterval(function(){
                            ShowGame("End", "0");
                            ReloadGamesList();
                        }, 30000 );
                    }

                    ShowGame("End", "0");
                }
            }
        );
    });

    $("button#refreshGame").click(function() {
        ShowGame("End", "0");
    });

    $("button#watchRefreshGame").click(function() {
        var gameId = $("input#gameName").val();

        $.get( "ShowGame.php?id=" + gameId + "&watch=1&MoveListIndex=End",
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

                    var movesList = $("#movesList");

                    $("#toBegin").click(function() {
                        ShowGame("0", "1");
                    });

                    $("#toEnd").click(function() {
                        ShowGame("End", "1");
                    });

                    $("#showPrevMove").click(function() {
                        var index = movesList[0].selectedIndex - 1;
                        ShowGame(index, "1");
                    });

                    $("#showNextMove").click(function() {
                        var index = $("#movesList")[0].selectedIndex + 1;
                        ShowGame(index, "1");
                    });

                    $("#refreshMoveList").click(function() {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, "1");
                    });

                    movesList.click(function () {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, "1");
                    });
                }

            }
        );
    });

    ReloadGamesList();

    function SetMakeMoveHandlers() {
        for (var i = 0; i < 3; i++) {
            for (var j = 0; j < 3; j++) {
                for (var k = 0; k < 3; k++) {
                    for (var l = 0; l < 3; l++) {
                        id = "s" + i + j + k + l;

                        $("td#" + id).click((function(ii,jj,kk,ll) {
                            return function() {
                                $.get("MakeMove.php?" +
                                    "id=" + $("input#gameName").val() +
                                    "&password=" + $("input#password").val() +
                                    "&XO=" + $("#XO").find("option:selected").text() +
                                    "&i=" + ii +
                                    "&j=" + jj +
                                    "&k=" + kk +
                                    "&l=" + ll,
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

                                        if (data == "Поле уже занято.") {
                                            error = true;
                                            BootstrapDialog.show({
                                                title: 'Ошибочка',
                                                type: BootstrapDialog.TYPE_WARNING,
                                                message: 'Поле уже занято.'
                                            });
                                        }

                                        if (data == "Сейчас не ваш ход.") {
                                            error = true;
                                            BootstrapDialog.show({
                                                title: 'Ошибочка',
                                                type: BootstrapDialog.TYPE_WARNING,
                                                message: 'Сейчас не ваш ход.'
                                            });
                                        }

                                        if (data.startsWith("Ход не в то поле.")) {
                                            error = true;
                                            var correctI = parseInt(data[17]);
                                            var correctJ = parseInt(data[18]);
                                            BootstrapDialog.show({
                                                title: 'Ошибочка',
                                                type: BootstrapDialog.TYPE_WARNING,
                                                message: 'Ход не в то поле. Нужно сделать ход в поле ' +
                                                (correctI+1) + " : " + (correctJ+1)
                                            });
                                        }

                                        if (data == "Крестики уже выиграли!") {
                                            error = true;

                                            BootstrapDialog.show({
                                                title: 'Ошибочка',
                                                type: BootstrapDialog.TYPE_WARNING,
                                                message: 'Крестики уже выиграли!'
                                            });

                                        }

                                        if (data == "Нолики уже выиграли!") {
                                            error = true;

                                            BootstrapDialog.show({
                                                title: 'Ошибочка',
                                                type: BootstrapDialog.TYPE_WARNING,
                                                message: 'Нолики уже выиграли!'
                                            });

                                        }

                                        if (data[0] == "@")
                                        {
                                            error = true;

                                            $("div#board").html(data.substring(2));

                                            BootstrapDialog.show({
                                                title: 'Победа',
                                                type: BootstrapDialog.TYPE_SUCCESS,
                                                message: ((data[1] == "1")?'Крестики': 'Нолики') + ' победили!'
                                            });
                                        }

                                        if (!error) {
                                            $("div#board").html(data);
                                            SetMakeMoveHandlers();

                                            var autoFlip = $("#autoFlip").find("option:selected").text();

                                            if (autoFlip == "Да")
                                            {
                                                if ($("#XO").find("option:selected").text() == "X")
                                                {
                                                    $("#XO").val("O");
                                                }
                                                else
                                                {
                                                    $("#XO").val("X");
                                                }
                                            }
                                        }

                                    }
                                );
                            };
                        })(i,j,k,l));
                    }
                }
            }
        }
    }

    $('#refreshGameList').click(function() {
        ReloadGamesList();
    });
});
