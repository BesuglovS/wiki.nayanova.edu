<?php
header("Content-type: image/png");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/WideImageLib/WideImage.php");

$Name = $_GET['Name'];

$ImageWidth = 971;

mb_internal_encoding("UTF-8");

header('Content-Type: image/png');

// Создание изображения
$im = imagecreatefrompng("upload/images/tank.png");

// Создание цветов
$black = imagecolorallocate($im, 91, 88, 71);

// Замена пути к шрифту на пользовательский
$Font = 'Torhok Italic.ttf';

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
        $SecondTextWidthArray = imagettfbbox($FontSize, -3, $Font, $Text);
        $SecondTextWidth = $SecondTextWidthArray[4] - $SecondTextWidthArray[6];
        $FontSize -= 0.5;
    } while ($SecondTextWidth > ($ImageWidth / 2) - 200 && $FontSize > 1);
    $FontSize += 0.5;

    imagettftext($Image, $FontSize, -3, 135, $Y, $FontColor, $Font, $Text);
}

// 124 142 160 178 196 214
$FontSize = 18;
DrawLine($FontSize, $Font, $Name, $ImageWidth, $im, $black, 145);

imagepng($im);
imagedestroy($im);
/*
$img = WideImage::load('upload/images/tank.png');
$watermark = WideImage::load('https://pp.vk.me/c425430/v425430399/1889/OXIpWDUzBUA.jpg');
$watermark = $watermark->resize(160, 213);
$im = $img->merge($watermark, 660, 300, 100);
//$new->output('png');

// 768 * 1024






