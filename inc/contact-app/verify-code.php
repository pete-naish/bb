<?php
include 'headers.php';

if(isSet($_POST['security_code'])) {

$security_code = strtolower(trim($_POST['security_code']));
$to_check = md5($security_code);

if($_SESSION['captcha_security_code'] == "") {
$token = $_COOKIE['captcha_security_code'];
} else {
$token = $_SESSION['captcha_security_code'];
}

if($to_check == $token)  {
echo 1; // Valid Code
}

}
?>