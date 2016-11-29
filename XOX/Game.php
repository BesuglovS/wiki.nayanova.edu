<?php

class Game {
    public static function GenerateFieldHtml($Moves, $MoveListIndex) {
        $output = "";

        $newGameString ="9999@000000000000000000000000000000000000000000000000000000000000000000000000000000000";
        $newBigfield = "000000000";

        $currentSign = 1;

        if ($MoveListIndex == 'End')
        {
            $MoveListIndex = count($Moves) - 1;
        }

        if ($MoveListIndex != "-1") {
            for ($i = 0; $i <= $MoveListIndex; $i++) {
                $MoveIndex = 5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $Moves[$i][2] * 3 + $Moves[$i][3];
                $newGameString[$MoveIndex] = $currentSign;

                if ($newBigfield[$Moves[$i][0] * 3 + $Moves[$i][1]] == "0") {
                    for ($ee = 0; $ee < 3; $ee++) {
                        if (($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $ee * 3 + 0] ==
                             $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $ee * 3 + 1]) &&
                            ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $ee * 3 + 1] ==
                             $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $ee * 3 + 2]) &&
                            ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $ee * 3 + 0] != "0")
                        ) {
                            $newBigfield[$Moves[$i][0] * 3 + $Moves[$i][1]] =
                                $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + $ee * 3 + 0];
                        }
                    }

                    for ($ee = 0; $ee < 3; $ee++) {
                        if (($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 0 * 3 + $ee] ==
                             $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + $ee]) &&
                            ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + $ee] ==
                             $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 2 * 3 + $ee]) &&
                            ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 0 * 3 + $ee] != "0")
                        ) {
                            $newBigfield[$Moves[$i][0] * 3 + $Moves[$i][1]] =
                                $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 0 * 3 + $ee];
                        }
                    }

                    if (($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 0 * 3 + 0] ==
                         $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1]) &&
                        ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1] ==
                         $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 2 * 3 + 2]) &&
                        ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1] != "0")
                    ) {
                        $newBigfield[$Moves[$i][0] * 3 + $Moves[$i][1]] =
                            $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1];
                    }

                    if (($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 2 * 3 + 0] ==
                         $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1]) &&
                        ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1] ==
                         $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 0 * 3 + 2]) &&
                        ($newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1] != "0")
                    ) {
                        $newBigfield[$Moves[$i][0] * 3 + $Moves[$i][1]] =
                            $newGameString[5 + $Moves[$i][0] * 27 + $Moves[$i][1] * 9 + 1 * 3 + 1];
                    }
                }

                $currentSign = ($currentSign == 1) ? 2 : 1;
            }


            $newGameString[0] = $Moves[$MoveListIndex][0];
            $newGameString[1] = $Moves[$MoveListIndex][1];
            $newGameString[2] = $Moves[$MoveListIndex][2];
            $newGameString[3] = $Moves[$MoveListIndex][3];
        }


        $gameString = $newGameString;
        $lastMoveString = substr($gameString, 0, 4);

        $bigField = $newBigfield;

        $output .= "<table id=\"BigTable\">\n";

        $output .= "<tr>\n";
        $output .= "<td>\n";

        $output .= "<table id=\"Field\">\n";

        for ($i = 0; $i < 3; $i++)
        {
            $output .= "\t<tr id=\"bigRow" . $i . "\">\n";

            for ($j = 0; $j < 3; $j++) {
                $output .= "\t\t<td class=\"bigSquare ";
                if ($i == 1) {
                    $output .= "h ";
                }
                if ($j == 1) {
                    $output .= "v ";
                }
                $output .= "\" ";

                if ($bigField[$i*3 + $j] == "1")
                {
                    $output .= "background=\"X.png\" style=\"background-repeat:no-repeat;background-position:center;\" ";
                }
                if ($bigField[$i*3 + $j] == "2")
                {
                    $output .= "background=\"O.png\" style=\"background-repeat:no-repeat;background-position:center;\" ";
                }

                $output .= ">\n";


                $output .= "\t\t\t<table class='center'>\n";

                for ($k = 0; $k < 3; $k++) {
                    $output .= "\t\t\t\t<tr id=\"row" . $i . $j . $k . "\">\n";

                    for ($l = 0; $l < 3; $l++) {
                        $cellSign = $gameString[5 + $i*27 + $j * 9 + $k * 3 + $l];

                        $output .= "\t\t\t\t\t<td class=\"square ";
                        if ($k == 1) {
                            $output .= "h ";
                        }
                        if ($l == 1) {
                            $output .= "v ";
                        }
                        if (($i . $j . $k . $l) == $lastMoveString)
                        {
                            $output .= "red ";
                        }
                        else
                        {
                            if ($cellSign == "1") {
                                $output .= "green ";
                            }
                            if ($cellSign == "2") {
                                $output .= "blue ";
                            }
                        }
                        $output .= "\" ";

                        $output .= "id = \"s" . $i . $j . $k . $l . "\"";

                        $output .= ">";

                        if ($cellSign == "1")
                        {
                            $output .= "X";
                        }
                        if ($cellSign == "2")
                        {
                            $output .= "O";
                        }

                        $output .= "</td>\n";
                    }

                    $output .= "\t\t\t\t</tr>\n";
                }

                $output .= "\t\t\t</table>";

                $output .= "\t\t</td>\n";
            }

            $output .= "\t</tr>\n";
        }

        $output .= "</table>";

        $output .= "</td>\n";

        $output .= "<td id=\"movesTableCell\">\n";

        $output .= "<select id=\"movesList\" size=\"22\">";

        if (count($Moves) > 0) {
            for ($i = 0; $i < count($Moves); $i++) {
                $output .= "<option ";
                if ($i == $MoveListIndex) {
                    $output .= "selected";
                }
                $output .= ">";
                $output .= $i + 1 . ") " . $Moves[$i][0] . " " . $Moves[$i][1] . " " . $Moves[$i][2] . " " . $Moves[$i][3];
                $output .= "</option>\n";
            }
        }

        $output  .= "</select>";

        $output .= "<div id=\"movesArrowsFirstRowDiv\">";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"showPrevMove\"><</button>\n";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"showNextMove\">></button>\n";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"refreshMoveList\"><span class='glyphicon glyphicon-refresh'></span></button>\n";
        $output .= "</div>\n";

        $output .= "<div id=\"movesArrowsSecondRowDiv\">";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"toBegin\"><<</button>\n";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"toEnd\">>></button>\n";
        $output .= "</div>\n";



        $output .= "</td>\n";
        $output .= "</tr>\n";

        $output .= "</table>"; // BigTable

        return $output;
    }

    public function CreateConnectPrompt($database)
    {
        $output = "";

        $output .= "<div id=\"createConnect\">\n";
        $output .= "<table>";
        $output .= "<tr>";

        $output .= "<td>";


        $output .= "<table>";

        $output .= "<tr>";
        $output .= "<td>";
        $output .= "\t<span id=\"gameNameLabel\">Имя игры</span>\n";
        $output .= "</td>";
        $output .= "<td>";
        $output .= "\t<input type=\"text\" id=\"gameName\">\n";
        $output .= "</td>";
        $output .= "</tr>";

        $output .= "<tr>";
        $output .= "<td>";
        $output .= "\t<span id=\"passwordLabel\">Пароль</span>\n";
        $output .= "</td>";
        $output .= "<td>";
        $output .= "\t<input type=\"password\" id=\"password\">\n";
        $output .= "</td>";
        $output .= "</tr>";

        $output .= "<tr>";
        $output .= "<td>";
        $output .= "\t<span id=\"gameSignLabel\">X / O</span>\n";
        $output .= "</td>";
        $output .= "<td>";
        $output .= "<select id=\"XO\">\n";
        $output .= "<option>X</option>";
        $output .= "<option>O</option>";
        $output .= "</select>";
        $output .= "</td>";
        $output .= "</tr>";

        $output .= "<tr>";
        $output .= "<td>";
        $output .= "\t<span id=\"autoFlipLabel\">Автопереключение знака</span>\n";
        $output .= "</td>";
        $output .= "<td style='text-align: center'>";
        $output .= "<select id=\"autoFlip\">\n";
        $output .= "<option selected>Нет</option>";
        $output .= "<option>Да</option>";
        $output .= "</select>";
        $output .= "</td>";
        $output .= "</tr>";

        $output .= "</table>";

        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"connectToGame\">Подключится</button>\n";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"createGame\">Создать</button>\n";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"refreshGame\">Обновить</button>\n";
        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"watchRefreshGame\">Смотреть</button>\n";

        $output .= "</td>";

        $output .= "<td>";
        $output .= "<div id=\"gameListDiv\">";
        $output .= $this->GetGameListSelect($database);
        $output .= "</div>";
        $output .= "<button type=\"button\" id=\"refreshGameList\" class='btn btn-default'>Обновить список игр</button>";
        $output .= "</td>";

        $output .= "</tr>";
        $output .= "</table>";
        $output .= "</div>\n";

        return $output;
    }

    /**
     * @param $output
     * @param $result
     * @return string
     */
    public function GetGameListSelect($database)
    {
        $query = "SELECT `GameIdName` FROM `xoxGames`";

        $queryResult = $database->query($query);

        $result = "<select id=\"gameList\" size=\"8\">";

        while ($game = $queryResult->fetch_assoc()) {
            $result .= "<option>";
            $result .= $game["GameIdName"];
            $result .= "</option>\n";
        }

        $result .= "</select>";
        return $result;
    }
}