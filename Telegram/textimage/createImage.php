<?php
require_once 'class.textPainter.php';

$x = $_GET["x"];
$y = $_GET["y"];

$R = $_GET["r"];
$G = $_GET["g"];
$B = $_GET["b"];

$size = $_GET["size"];

$text = $_GET["text"];

$img = new textPainter('./paisaje.jpg', $text, './arial.ttf', $size);

if(!empty($x) && !empty($y)){
    $img->setPosition($x, $y);
}

if(!empty($R) && !empty($G) && !empty($B)){
    $img->setTextColor($R,$G,$B);
}

$img->show();
?>
