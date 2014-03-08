<?php

function weeksCompare($a, $b)
{
    $dash = "-";
    $pos = mb_strpos($a,$dash);
    if ($pos !== false)
    {
        $a = mb_substr($a, 0, $pos);
    }

    $pos = mb_strpos($b,$dash);
    if ($pos !== false)
    {
        $b = mb_substr($b, 0, $pos);
    }

    if ($a > $b)
    {
        return 1;
    }
    elseif ($a < $b)
    {
        return -1;
    }

    return 0;
}

class Utilities {

    public static $DOWEnToRu = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 0 => 7);
    public static $DOWRuToEn = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 0);
    public static $Attestation = array(
        0 => "-",
        1 => "Зачёт",
        2 => "Экзамен",
        3 => "Зачёт + Экзамен",
        4 => "Зачёт с оценкой"
    );
    public static $DOW = array(1 => "Понедельник", 2 => "Вторник", 3 => "Среда",
        4 => "Четверг", 5 => "Пятница", 6 => "Суббота", 7 => "Воскресенье");

    public static function DiffTimeWithNowInMinutes($hour, $min){
        $diffTime = strtotime($hour . ":" . $min . ":00");
        $now = strtotime("now") + 60*60;
        $diff = ($diffTime - $now) / 60;
        return $diff;
    }

    public static function TagMessage($message, $tagname = "h1"){
        return "<" . $tagname . ">" . $message . "</" . $tagname . ">";
    }

    public static function NothingISThereString($tagname = "h1"){
        return "<" . $tagname . ">Нету дома никого!</" . $tagname . ">";
    }

    public static function AuditoriumBuilding($AuditoriumName){
        // Аудитории Ярмарочной начинаются на "Корп № 3"
        if (mb_substr($AuditoriumName, 0, 8, 'UTF-8') === "Корп № 3")
        {
            return "Jar";
        }

        // Аудитории Молодогвардейской имеют цифру в 6-й позиции = "Ауд. 301"
        //                                                          012345
        if (is_numeric(mb_substr($AuditoriumName, 5, 1, 'UTF-8')))
        {
            return "Mol";
        }

        // И все прочие
        return "Other";
    }

    public static function WeekFromDate($date, $semesterStarts)
    {
        //echo $date . " " . $semesterStarts . "<br />";
        $lessonDate = DateTime::createFromFormat('Y-m-d', $date);
        $start = DateTime::createFromFormat('Y-m-d', $semesterStarts);
        return (int)(floor(($lessonDate->diff($start)->format('%a')) / 7)) + 1;
    }

    public static function GatherWeeksToString($weekArray)
    {
        $result = array();
        $boolWeeks = array();
        for($i=0; $i<=20;$i++) {
            $boolWeeks[$i] = false;
        }
        foreach ($weekArray as $week) {
            $boolWeeks[$week] = true;
        }

        $prev = false;
        $baseNum = 20;
        for($i = 0; $i<=19; $i++)
        {
            if (($prev == false) && ($boolWeeks[$i] == true))
            {
                $baseNum = $i;
            }

            if (($boolWeeks[$i] == false) && (($i - $baseNum) > 2))
                {
                    $result[] = $baseNum .  "-" . ($i - 1);

                    for ($k = $baseNum; $k < $i; $k++)
                    {
                        $boolWeeks[$k] = false;
                    }
                }

                if ($boolWeeks[$i] == false)
                    $baseNum = 20;

                $prev = $boolWeeks[$i];
        }

        $prev = false;
        $baseNum = 20;
        for($i = 1; $i<=19; $i = $i + 2)
        {
            if (($prev == false) && ($boolWeeks[$i] == true))
            {
                $baseNum = $i;
            }

            if (($boolWeeks[$i] == false) && (($i - $baseNum) > 4))
            {
                $result[] = $baseNum .  "-" . ($i - 2) . " (нечёт.)";

                for ($k = $baseNum; $k < $i; $k = $k + 2)
                {
                    $boolWeeks[$k] = false;
                }
            }

            if ($boolWeeks[$i] == false)
                $baseNum = 20;

            $prev = $boolWeeks[$i];
        }

        $prev = false;
        $baseNum = 20;
        for($i = 2; $i<=20; $i = $i + 2)
        {
            if (($prev == false) && ($boolWeeks[$i] == true))
            {
                $baseNum = $i;
            }

            if (($boolWeeks[$i] == false) && (($i - $baseNum) > 4))
            {
                $result[] = $baseNum .  "-" . ($i - 2) . " (чёт.)";

                for ($k = $baseNum; $k < $i; $k = $k + 2)
                {
                    $boolWeeks[$k] = false;
                }
            }

            if ($boolWeeks[$i] == false)
                $baseNum = 20;

            $prev = $boolWeeks[$i];
        }



        for ($i = 1; $i <= 18; $i++)
        {
            if ($boolWeeks[$i])
            {
                $result[] = $i;
            }
        }

        uasort($result, "weeksCompare");

        return implode(", ", $result);
    }

    public static function GetPercentColorString($target, $guess)
    {
        if ($guess < $target*0.5)
        {
            return "#faa"; // Красный
        }

        if ($guess < $target*0.9)
        {
            return "#fb5"; // Оранжевый
        }

        if ($guess < $target)
        {
            return "#ff4"; // Жёлтый
        }

        return "#afa"; // Зелёный
    }

    public static function GetCurrentWeekNumber()
    {

    }

    public static function p($param)
    {
        echo "<pre>";
        echo print_r($param);
        echo "</pre>";
    }

    public static function pe($param)
    {
        echo "<pre>";
        echo print_r($param);
        echo "</pre>";
        exit;
    }
}
?>
