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
        $output .= "\t<span id=\"gameSignLabel\">Белые / Чёрные</span>\n";
        $output .= "</td>";
        $output .= "<td>";
        $output .= "<select id=\"wb\">\n";
        $output .= "<option value='w'>Белые</option>";
        $output .= "<option value='b'>Чёрные</option>";
        $output .= "</select>";
        $output .= "</td>";
        $output .= "</tr>";

        $output .= "</table>";

        $output .= "\t<button type=\"button\" class='btn btn-default' id=\"connectToGame\">Подключиться</button>\n";
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

    public function GetGameListSelect($database)
    {
        $query = "SELECT `GameIdName` FROM `chessGames`";

        $queryResult = $database->query($query);

        $result = "<select id=\"gameList\" size=\"6\">";

        while ($game = $queryResult->fetch_assoc()) {
            $result .= "<option>";
            $result .= $game["GameIdName"];
            $result .= "</option>\n";
        }

        $result .= "</select>";
        return $result;
    }

    public function GenerateFieldHtml($Field, $History, $MoveListIndex)
    {
        $result = array();
        $result["FEN"] = $Field;
        $result["History"] = $History;
        $result["MoveListIndex"] = $MoveListIndex;

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}