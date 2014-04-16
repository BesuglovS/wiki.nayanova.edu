<?php
header("Content-type: image/png");
require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/WideImageLib/WideImage.php");

$Name = $_GET['Name'];
$ImageURL = $_GET['img'];

$ImageWidth = 971;

mb_internal_encoding("UTF-8");

header('Content-Type: image/png');

/**/

$image = WideImage::load('upload/images/tank.png');
$canvas = $image->getCanvas();

$canvas->useFont('Torhok Italic.ttf', 20, $image->allocateColor(91, 88, 71));
$canvas->writeText(140, 125, $Name, 3);

$watermark = WideImage::load($ImageURL);
$watermark = $watermark->resize(160, 213);
$image = $image->merge($watermark, 660, 300, 100);

$image->output('png');


