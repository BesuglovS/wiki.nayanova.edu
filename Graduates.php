<?php
header("Content-type: image/png");

$Who = $_GET['Who'];
$Whom = $_GET['Whom'];
$Qualification = $_GET['Qualification'];

$ImageWidth = 500;

mb_internal_encoding("UTF-8");

header('Content-Type: image/png');

// Создание изображения
$im = imagecreatefrompng("upload/images/diplom2.png");

// Создание цветов
$black = imagecolorallocate($im, 0, 0, 0);

// Замена пути к шрифту на пользовательский
$FirstFont = 'Torhok Italic.ttf';
$SecondFont = 'Torhok Italic.ttf';
$ThirdFont = 'Torhok Italic.ttf';

function CalculateTextX($ImgWidth, $Font, $FontSize, $Text)
{
    $TextSize = imagettfbbox($FontSize, 0, $Font, $Text);
    $TextWidth = $TextSize[4] - $TextSize[6];
    $result = ($ImgWidth - $TextWidth) / 2;
    return $result;
}

function DrawLine($FontSize, $Font, $Text, $ImageWidth, $Image, $FontColor, $Y)
{
    do {
        $SecondTextWidthArray = imagettfbbox($FontSize, 0, $Font, $Text);
        $SecondTextWidth = $SecondTextWidthArray[4] - $SecondTextWidthArray[6];
        $FontSize -= 0.5;
    } while ($SecondTextWidth > $ImageWidth - 110);
    $FontSize += 0.5;
    $SecondX = CalculateTextX($ImageWidth, $Font, $FontSize, $Text);
    imagettftext($Image, $FontSize, 0, $SecondX, $Y, $FontColor, $Font, $Text);
}

$WhoArray = explode(';', $Who, 6);
$WhoArrayCount = count($WhoArray);

// 124 142 160 178 196 214
$FirstFontSize = 18;

switch ($WhoArrayCount) {
    case "1":
        DrawLine($FirstFontSize, $FirstFont, $Who, $ImageWidth, $im, $black, 178);
        break;
    case "2":
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[0], $ImageWidth, $im, $black, 142);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[1], $ImageWidth, $im, $black, 196);
        break;
    case "3":
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[0], $ImageWidth, $im, $black, 142);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[1], $ImageWidth, $im, $black, 178);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[2], $ImageWidth, $im, $black, 214);
        break;
    case "4":
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[0], $ImageWidth, $im, $black, 142);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[1], $ImageWidth, $im, $black, 160);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[2], $ImageWidth, $im, $black, 178);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[3], $ImageWidth, $im, $black, 196);
        break;
    case "5":
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[0], $ImageWidth, $im, $black, 124);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[1], $ImageWidth, $im, $black, 142);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[2], $ImageWidth, $im, $black, 160);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[3], $ImageWidth, $im, $black, 178);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[4], $ImageWidth, $im, $black, 196);
        break;
    case "6":
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[0], $ImageWidth, $im, $black, 124);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[1], $ImageWidth, $im, $black, 142);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[2], $ImageWidth, $im, $black, 160);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[3], $ImageWidth, $im, $black, 178);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[4], $ImageWidth, $im, $black, 196);
        DrawLine($FirstFontSize, $FirstFont, $WhoArray[5], $ImageWidth, $im, $black, 214);
        break;
    default:
        $FirstX = CalculateTextX($ImageWidth, $FirstFont, $FirstFontSize, $Who);
        imagettftext($im, 18, 0, $FirstX, 178, $black, $FirstFont, $Who);
        break;
}

$SecondFontSize = 24;
DrawLine($SecondFontSize, $SecondFont, $Whom, $ImageWidth, $im, $black, 405);

$ThirdFontSize = 24;
DrawLine($ThirdFontSize, $ThirdFont, $Qualification, $ImageWidth, $im, $black, 540);

imagepng($im);
imagedestroy($im);