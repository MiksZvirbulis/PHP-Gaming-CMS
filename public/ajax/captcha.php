<?php
session_start();

$string = "";

for($i = 0; $i < 8; $i++){
	$string .= chr(rand(97, 122));
}

$_SESSION['captcha_code'] = $string;

$image = imagecreatetruecolor(200, 60);
$black = imagecolorallocate($image, 0, 0, 0);
$color = imagecolorallocate($image, 28, 134, 238);
$white = imagecolorallocate($image, 255, 255, 255);

imagefilledrectangle($image,0,0,399,99,$white);
imagettftext($image, 30, 0, 10, 40, $color, "../../assets/fonts/arial.ttf", $_SESSION['captcha_code']);

header("Content-type: image/png");
imagepng($image);