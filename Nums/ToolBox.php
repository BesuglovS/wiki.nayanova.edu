<?php
function generateNum() {
    $numsList = array();

    for ($i = 1234; $i < 98766; $i++)
    {
        if (isCorrect($i) == "true")
        {
            $numsList[] = $i;
        }
    }

    return $numsList[array_rand($numsList)];
}

function isCorrect($num) {
    $len = strlen($num);
    if (($len < 4) || ($len > 5))
    {
        return "false";
    }

    if ($len == 4)
    {
        $num = "0" . $num;
    }

    $d1 = mb_substr($num, 0, 1, 'utf-8');
    $d2 = mb_substr($num, 1, 1, 'utf-8');
    $d3 = mb_substr($num, 2, 1, 'utf-8');
    $d4 = mb_substr($num, 3, 1, 'utf-8');
    $d5 = mb_substr($num, 4, 1, 'utf-8');

    if (($d1 != $d2) && ($d1 != $d3) && ($d1 != $d4) && ($d1 != $d5) &&
        ($d2 != $d3) && ($d2 != $d4) && ($d2 != $d5) &&
        ($d3 != $d4) && ($d3 != $d5) &&
        ($d4 != $d5))
    {
        return "true";
    }
    else
    {
        return "false";
    }
}

function MakeCount($guess, $ideal) {
    if (strlen($guess) == 4)
    {
        $guess = "0" . $guess;
    }

    if (strlen($ideal) == 4)
    {
        $ideal = "0" . $ideal;
    }

    $first = 0;
    $second = 0;
    for($i = 0; $i < 5; $i++)
    {
        for($j = 0; $j < 5; $j++)
        {
            if (substr($guess, $i, 1) == substr($ideal, $j, 1))
            {
                $first++;
                if ($i == $j)
                {
                    $second++;
                }
            }
        }
    }

    return $first . $second;
}