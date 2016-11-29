<?php

class Game {

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
        $output .= "\t<span id=\"gameSignLabel\">Сторона</span>\n";
        $output .= "</td>";
        $output .= "<td>";
        $output .= "<select id=\"Side\">\n";
        $output .= "<option>Игрок 1</option>";
        $output .= "<option>Игрок 2</option>";
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
        $query = "SELECT `GameIdName` FROM citiesGames";

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

    public function GeneratePositionHtml($moves1, $moves2, $makeMoveButton)
    {
        $output  = "<table id=\"positionTable\">\n";

        $output .= "<tr>\n";

        $output .= "<td>\n";

        $output .= "<select id=\"moves1List\" size=\"16\">";


        $moves1Count = count($moves1);
        if ($moves1Count > 0) {
            for ($i = 0; $i < count($moves1); $i++) {
                $output .= "<option ";
                if ($i == $moves1Count-1)
                {
                    $output .= "selected";
                }
                $output .= " >";
                $output .= $i + 1 . ") " . $moves1[$i]["Name"];
                $output .= "</option>\n";
            }
        }

        $output  .= "</select>";

        $output .= "</td>\n";

        $output .= "<td>\n";

        $output .= "<select id=\"moves2List\" size=\"16\">";


        $moves2Count = count($moves2);
        if ($moves2Count > 0) {
            for ($i = 0; $i < count($moves2); $i++) {
                $output .= "<option ";
                if ($i == $moves2Count-1)
                {
                    $output .= "selected";
                }
                $output .= " >";
                $output .= $i + 1 . ") " . $moves2[$i]["Name"];
                $output .= "</option>\n";
            }
        }


        $output  .= "</select>";

        $output .= "</td>\n";

        $output .= "</tr>\n";

        $output .= "</table>\n";

        if ($makeMoveButton == "true") {

            $output .= "<div id=\"moveDiv\">\n";

            $output .= "<input type=\"text\" id=\"move\">\n";
            $output .= "\t<button type=\"button\" class='btn btn-default' id=\"makeMove\">Сделать ход</button>\n";

            $output .= "</div>\n";
        }

        return $output;
    }
}