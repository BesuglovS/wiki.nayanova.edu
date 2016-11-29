$(function () {
    var ShowGameResult = function (game) {
        if (game.in_draw() === true || game.in_stalemate() || game.in_threefold_repetition()) {
            BootstrapDialog.show({
                title: 'Игра окончена',
                type: BootstrapDialog.TYPE_SUCCESS,
                message: "Ничья"
            });
        }

        if (game.in_checkmate() === true) {
            var side = game.fen().split(' ')[1];
            BootstrapDialog.show({
                title: 'Игра окончена',
                type: BootstrapDialog.TYPE_SUCCESS,
                message: (side == "w") ? "Белым мат" : "Чёрным мат"
            });
        }
    };
    var SetupPosition = function (FenPosition, side, History, MoveListIndex) {
        if (History === undefined || History == "")
        {
            History = "[]";
        }

        var history = JSON.parse(History);
        if (MoveListIndex == "End")
        {
            MoveListIndex = history.length-1;
        }

        if (MoveListIndex < 0) {
            MoveListIndex = 0;
        }

        if (MoveListIndex > history.length-1) {
            MoveListIndex = history.length-1;
        }

        var game = new Chess();
        for (var i = 0; i <= MoveListIndex; i++) {
            game.move(history[i]);
        }

        var board;

        // do not pick up pieces if the game is over
        // only pick up pieces for the side to move
        var onDragStart = function (source, piece, position, orientation) {
            ShowGameResult(game);

            if ((MoveListIndex != history.length-1) ||
                (game.game_over() === true) ||
                (game.turn() === 'w' && piece.search(/^b/) !== -1) ||
                (game.turn() === 'b' && piece.search(/^w/) !== -1) ||
                (game.turn() === 'w' && side == "b") ||
                (game.turn() === 'b' && side == "w") ||
                (side == "")) {
                return false;
            }
        };

        var onDrop = function (source, target, piece, newPosition,
                               oldPosition, currentOrientation) {
            // see if the move is legal
            var move = game.move({
                from: source,
                to: target,
                promotion: 'q' // NOTE: always promote to a queen for example simplicity
            });

            // illegal move
            if (move === null) return 'snapback';


            var history = game.history({verbose: true});
            var h = JSON.stringify(history);


            $.get("./php/MakeMove.php?" +
                "id=" + $("input#gameName").val() +
                "&password=" + $("input#password").val() +
                "&wb=" + $("#wb").val() +
                "&FEN=" + game.fen() +
                "&History=" + encodeURIComponent(JSON.stringify(history)),
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


                    if (data == "Сейчас не ваш ход.") {
                        error = true;
                        BootstrapDialog.show({
                            title: 'Ошибочка',
                            type: BootstrapDialog.TYPE_WARNING,
                            message: 'Сейчас не ваш ход.'
                        });
                    }


                    if (!error) {
                        SetupPosition(game.fen(), side, JSON.stringify(game.history({verbose: true})), "End");

                        ShowGameResult(game);
                    }

                }
            );

            //updateStatus();
        };

// update the board position after the piece snap
// for castling, en passant, pawn promotion
        var onSnapEnd = function () {
            board.position(game.fen());
        };

        var updateStatus = function () {
            var status = '';

            var moveColor = 'White';
            if (game.turn() === 'b') {
                moveColor = 'Black';
            }

            // checkmate?
            if (game.in_checkmate() === true) {
                status = 'Game over, ' + moveColor + ' is in checkmate.';
            }

            // draw?
            else if (game.in_draw() === true) {
                status = 'Game over, drawn position';
            }

            // game still on
            else {
                status = moveColor + ' to move';

                // check?
                if (game.in_check() === true) {
                    status += ', ' + moveColor + ' is in check';
                }
            }
        };

        var cfg = {
            draggable: true,
            position: game.fen().split(' ')[0],
            onDragStart: onDragStart,
            onDrop: onDrop,
            onSnapEnd: onSnapEnd,
            orientation: (side == "w" || side == "") ? "white" : "black"
        };
        board = ChessBoard('board', cfg);

        var movesList = $("select#movesList");
        movesList.empty();

        for (var j = 0; j < history.length; j++) {
            var move = history[j];
            var selected = (j == MoveListIndex);

            movesList.append($('<option>', {
                value: j,
                text: Math.floor((j / 2) + 1) + ") " + move["san"],
                selected: selected
            }));
        }

        //updateStatus();
    };

    SetupPosition("rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1", "", "[]", "End");

    function ReloadGamesList() {
        $("div#gameListDiv").load('./php/GameListHtml.php', function () {
            $('#gameList').click(function () {
                $("#gameName").val($('#gameList').val());
            });
        });
    }


    if (typeof String.prototype.startsWith != 'function') {
        // see below for better implementation!
        String.prototype.startsWith = function (str) {
            return this.indexOf(str) === 0;
        };
    }

    $("button#createGame").click(function () {
        var gameId = $("input#gameName").val();
        var wb = $("#wb").val();


        $.get("./php/CreateGame.php?id=" + gameId + "&wb=" + wb,
            function (data) {
                if (data == "Game with specified Id already exists in database") {
                    BootstrapDialog.show({
                        title: 'Ошибочка',
                        type: BootstrapDialog.TYPE_WARNING,
                        message: 'Игра с таким именем уже существует.'
                    });
                }
                else {
                    var GameCreatedData = JSON.parse(data);

                    console.log("GameCreatedData");
                    console.log(GameCreatedData);

                    var wb = $("#wb").val();

                    $("#wb").prop('disabled', 'disabled');

                    var userPassword = "";

                    console.log("wb = " + wb);

                    if (wb == "w") {
                        userPassword = GameCreatedData["wPass"];
                    }
                    if (wb == "b") {
                        userPassword = GameCreatedData["bPass"];
                    }

                    $("#password").val(userPassword);

                    var gameId = GameCreatedData["GameIdName"];
                    $("#gameName").val(gameId);

                    console.log(GameCreatedData);

                    var showPath = './php/ShowGame.php?id=' + encodeURIComponent($("input#gameName").val()) +
                        '&password=' + $("input#password").val() +
                        '&wb=' + wb +
                        "&MoveListIndex=-1 ";

                    $.get(showPath,
                        function (data) {
                            var GameData = JSON.parse(data);
                            SetupPosition(GameData.FEN, wb, GameData.History, GameData.MoveListIndex);

                            BootstrapDialog.show({
                                title: 'Gut :-)',
                                type: BootstrapDialog.TYPE_SUCCESS,
                                message: 'Игра создана успешно. Ваш пароль к этой игре: ' + userPassword
                            });

                            ReloadGamesList();

                            window.updateTimerId = setInterval(function () {
                                ShowGame("Current", "0", wb);
                                ReloadGamesList();
                            }, 30000);
                        }
                    );
                }
            });
    });

    function ShowGame(MoveListIndex, watch, side) {

        console.log("ShowGame", MoveListIndex, watch, side);

        if (MoveListIndex == undefined) {
            MoveListIndex = -1;
        }

        if (MoveListIndex == "Current") {
            var movesList = $("#movesList");
            MoveListIndex = movesList[0].selectedIndex;
        }

        var movesCount = $("#movesList option").length;

        if (MoveListIndex == -1) {
            MoveListIndex = movesCount - 1;
        }

        if (MoveListIndex == movesCount) {
            MoveListIndex = 0;
        }

        var path = "";
        if (watch != "1") {
            path = './php/ShowGame.php?id=' + $("input#gameName").val() +
                "&password=" + $("input#password").val()
                + "&wb=" + $("#wb").val()
                + "&MoveListIndex=" + MoveListIndex;
        }
        else {
            path = './php/ShowGame.php?id=' + $("input#gameName").val()
                + "&watch=1"
                + "&MoveListIndex=" + MoveListIndex;
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
                    var GameData = JSON.parse(data);
                    SetupPosition(GameData.FEN, side, GameData.History, GameData.MoveListIndex);

                    var movesList = $("#movesList");


                    $("#toBegin").off();
                    $("#toBegin").click(function () {
                        ShowGame("0", watch, side);
                    });

                    $("#toEnd").off();
                    $("#toEnd").click(function () {
                        ShowGame("End", watch, side);
                    });

                    $("#showPrevMove").off();
                    $("#showPrevMove").click(function () {
                        var index = movesList[0].selectedIndex - 1;
                        ShowGame(index, watch, side);
                    });

                    $("#showNextMove").off();
                    $("#showNextMove").click(function () {
                        var index = $("#movesList")[0].selectedIndex + 1;
                        ShowGame(index, watch, side);
                    });

                    $("#refreshMoveList").off();
                    $("#refreshMoveList").click(function () {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, watch, side);
                    });

                    movesList.off();
                    movesList.click(function () {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, watch, side);
                    });
                }
            }
        );
    }

    $("button#connectToGame").click(function () {
        $.get('./php/Connect.php?id=' + $("input#gameName").val() +
            "&password=" + $("input#password").val()
            + "&wb=" + $("#wb").val(),
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
                    $("#wb").prop('disabled', 'disabled');

                    if (data == "Success") {
                        BootstrapDialog.show({
                            title: 'Gut :-)',
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: 'Подключение успешно.'
                        });
                    }
                    else {
                        var password = JSON.parse(data);

                        var wb = $("#wb").val();

                        var userPassword = "";
                        if (wb == "w") {
                            userPassword = password["wPass"];
                        }
                        if (wb == "b") {
                            userPassword = password["bPass"];
                        }

                        $("#password").val(userPassword);

                        BootstrapDialog.show({
                            title: 'Gut :-)',
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: 'Подключение успешно. Ваш пароль к этой игре: ' + userPassword
                        });
                    }

                    clearInterval(window.updateTimerId);

                    window.updateTimerId = setInterval(function () {
                        var wb = $("#wb").val();
                        ShowGame("Current", "0", wb);
                        ReloadGamesList();
                    }, 30000);

                    ShowGame("End", "0", $("#wb").val());
                }
            }
        );
    });

    $("button#refreshGame").click(function () {
        ShowGame("End", "0", $("#wb").val());
    });

    $("button#watchRefreshGame").click(function () {
        var gameId = $("input#gameName").val();

        $.get("./php/ShowGame.php?id=" + gameId + "&watch=1&MoveListIndex=End",
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

                if (!error) {
                    var GameData = JSON.parse(data);
                    SetupPosition(GameData.FEN, "", GameData.History, GameData.MoveListIndex);

                    var movesList = $("#movesList");

                    $("#toBegin").click(function () {
                        ShowGame("0", "1", "");
                    });

                    $("#toEnd").click(function () {
                        ShowGame("End", "1", "");
                    });

                    $("#showPrevMove").click(function () {
                        var index = movesList[0].selectedIndex - 1;
                        ShowGame(index, "1", "");
                    });

                    $("#showNextMove").click(function () {
                        var index = $("#movesList")[0].selectedIndex + 1;
                        ShowGame(index, "1", "");
                    });

                    $("#refreshMoveList").click(function () {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, "1", "");
                    });

                    movesList.click(function () {
                        var index = $("#movesList")[0].selectedIndex;
                        ShowGame(index, "1", "");
                    });
                }

            }
        );
    });

    ReloadGamesList();

    $('#refreshGameList').click(function () {
        ReloadGamesList();
    });

});

