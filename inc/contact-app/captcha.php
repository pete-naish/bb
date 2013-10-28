<?php
include_once 'headers.php';
include_once 'common.php';
include_once 'libraries/captcha/class.captcha.php';

$captcha = new Captcha();

$pathToCaptchaFonts = dirname(__FILE__).'/fonts/'; 

$captchaColors = unserialize(CAPTCHA_COLORS);

$captcha->charsNumber = CAPTCHA_CHARS_NUMBER;
$captcha->stringType = CAPTCHA_STRING_TYPE;
$captcha->fontSize = CAPTCHA_FONT_SIZE;

$captcha->textColor = $captchaColors['text'];
$captcha->backgroundColor = $captchaColors['background'];

$captcha->ttFont = $pathToCaptchaFonts.CAPTCHA_TT_FONT.'.ttf';

$captcha->showImage(CAPTCHA_IMAGE_WIDTH, CAPTCHA_IMAGE_HEIGHT);
?>