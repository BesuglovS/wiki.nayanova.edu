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
        $output .= "\t<span id=\"numsLabel\">Задуманное число</span>\n";
        $output .= "</td>";
        $output .= "<td>";
        $output .= "\t<input type=\"password\" id=\"num\">\n";
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
        $query = "SELECT `GameIdName` FROM `numsGames`";

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