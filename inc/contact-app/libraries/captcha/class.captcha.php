<?php
/* 
Author: Gabriel Comarita
Author's Website: http://www.bitrepository.com/

Copyright (c) BitRepository.com - You do not have rights to reproduce, republish, redistribute or resell this product without permission from the author or payment of the appropiate royalty of reuse.

* Keep this notice intact for legal use *
*/

class Captcha {

// Number of characters
var $charsNumber;

// Numbers (1), Letters (2), Letters & Numbers (3)
var $stringType;

// Font Range
var $fontSize;

// Text Color
var $textColor;

// Background Color
var $backgroundColor;

// Path to TrueType Font
var $ttFont;

// Transparent captcha background
var $transparent_bg;

/* Show Captcha Image */
function showImage($width = 88, $height = 31) {

if(isSet($this->ttFont)) {
if(!file_exists($this->ttFont)) exit('The path to the true type font is incorrect.');
}

if($this->charsNumber < 3) exit('The captcha code must have at least 3 characters');

$string = $this->generateString();

$im = ImageCreate($width, $height);

if($this->transparent_bg) {

/* Set a White & Transparent Background Color */

$bg = ImageColorAllocateAlpha($im, 255, 255, 255, 127); // (PHP 4 >= 4.3.2, PHP 5)
ImageFill($im, 0, 0, $bg);

} else {

$bg = ImageColorAllocate($im, $this->backgroundColor['r'], $this->backgroundColor['g'], $this->backgroundColor['b']); // (PHP 4 >= 4.3.2, PHP 5)
ImageFill($im, 0, 0, $bg);

}

$textcolor = ImageColorAllocate($im, $this->textColor['r'], $this->textColor['g'], $this->textColor['b']);

if(isSet($this->ttFont) && function_exists('imagettftext')) { // Use TrueType Font
$y = $height - round($height / 4);

for($i = 0; $i < $this->charsNumber; $i++) {
$char = $string[$i];

$factor = 17;
$x = ($factor * ($i + 1)) - 5;
$angle = rand(1, 15);

$font = rand($this->fontSize - 3, $this->fontSize + 3);

imagettftext($im, $font, $angle, $x, $y, $textcolor, $this->ttFont, $char);
}
} else { // Use Standard Font

// Font Size
$font = 5;

$font_width = ImageFontWidth($font);
$font_height = ImageFontHeight($font);

/*
-----------
Text Width
-----------
*/

$chars = preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);

$showString = '';

foreach($chars as $char) {
$showString .= $char.' ';
}

$showString = trim($showString);

$text_width = $font_width * strlen($showString);

// Position to align in center
$position_center = ceil(($width - $text_width) / 2);

/*
-----------
Text Height
-----------
*/

$text_height = $font_height;

// Position to align in abs middle
$position_middle = ceil(($height - $text_height) / 2);

$image_string = ImageString($im, $font, $position_center, $position_middle, $showString, $textcolor);

}

$token = md5(strtolower($string));

$_SESSION['captcha_security_code'] = $token;

setcookie('captcha_security_code', $token, time() + (3600 * 24), '/', $_SERVER['HTTP_HOST']);

/* Output the verification image */
header("Content-type: image/png");
ImagePNG($im);

exit;
}

function generateString() {
// letters
if($this->stringType == 1) {
$array = range('a','z');
}
// numbers
else if($this->stringType == 2) {
$array = range(1,9);
}
// letters & numbers
else {
$x = ceil($this->charsNumber / 2);

$array_one = array_rand(array_flip(range('a','z')), $x);

if($x <= 2) $x = $x - 1;

$array_two = array_rand(array_flip(range(1,9)), $this->charsNumber - $x);

$array = array_merge($array_one, $array_two);
}

$rand_keys = array_rand($array, $this->charsNumber);
	
$string = '';

foreach($rand_keys as $key) {
$string .= $array[$key];
}

return $string;
}

}
?>