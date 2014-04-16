<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/_php/includes/WideImageLib/WideImage.php");

WideImage::load("http://wiki.nayanova.edu/upload/images/beta.jpg")->resize(500, 300)->output('jpg', 90);


?>
