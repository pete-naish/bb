<?php
/* 
Author: Gabriel Comarita
Author's Website: http://www.bitrepository.com/

Copyright (c) BitRepository.com - You do not have rights to reproduce, republish, redistribute or resell this product without permission from the author or payment of the appropiate royalty of reuse.

* Keep this notice intact for legal use *
*/

include_once 'headers.php';
include_once 'common.php';

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
include_once 'libraries/php.mailer/class.phpmailer.php4.php'; // For PHP4 users
} else {
include_once 'libraries/php.mailer/class.phpmailer.php5.php'; // For PHP5+ users
}

// Was the method "POST" used? Continue

if(!empty($_POST)) {

include 'libraries/functions.php';

while(list($n, $v) = each($_POST)) { $$n = stripslashes(trim($v)); }

$error = array();

foreach($_POST as $key => $value) {

$_POST[$key] = stripslashes(trim($value));

if($formFields[$key]['mandatory'] == 1) { // Mandatory?

$value = stripslashes(trim($_POST[$key]));

if($formFields[$key]['validation']['basic'] == 1) { // Basic Validation

if(!$value) { 
$error[$key.'_none'] = 1;
}

}

if($formFields[$key]['validation']['email'] == 1) { // E-Mail Validation

if(!ValidateEmail($value)) { 
$error[$key.'_invalid'] = 1;
}

}

if($formFields[$key]['validation']['min_chars'] > 0) { // Minimum Chars

if(strlen($value) < $formFields[$key]['validation']['min_chars']) { 
$error[$key.'_min_chars'] = 1;
}

}

}

}


if(USE_CAPTCHA == 1) {

$security_code = trim(strtolower($_POST['security_code']));

if($security_code == '') {

$error['security_code'] = 1;

} else {

if($_SESSION['captcha_security_code'] == '') {
$token = $_COOKIE['captcha_security_code'];
} else {
$token = $_SESSION['captcha_security_code'];
}

if(md5($security_code) != $token) {
$error['security_code'] = 2;
}
}

}

if(empty($error)) {


// The following values reflect some common fields that are in a contact form: 'sender_name', 'sender_email', 'sender_message', 'sender_message'

// If for any reason, you have renamed these fields, than consider changing the variable names below. For instance if 'sender_name' is renamed to 'company_name' then rename $sender_name to $company_name

$mail             = new PHPMailer(); // defaults to using php "mail()"

$mail->From       = $sender_email;
$mail->FromName   = $sender_name; 

$mail->AddAddress(WEBMASTER_EMAIL, WEBMASTER_NAME);

// Set the message
$final_message = BODY_MESSAGE;
$final_message = nl2br($final_message);

$final_message_text = BODY_MESSSAGE_TEXT;

$ip_address = getRealIpAddress();

$ar_subject = AR_SUBJECT;

$ar_message = AR_MESSAGE;
$ar_message = nl2br($ar_message);

$ar_message_text = AR_MESSAGE_TEXT;

$replacements = array();

foreach($_POST as $pKey => $pValue) {
$replacements['{'.$pKey.'}'] = $pValue;
}

$replacements['{sender_ip_address}'] = $ip_address;
$replacements['{sender_hostname}'] = gethostbyaddr($ip_address);

foreach($replacements as $to_replace => $replacement) {

$sender_subject = str_replace($to_replace, $replacement, $sender_subject);
$ar_subject = str_replace($to_replace, $replacement, $ar_subject);

$final_message = str_replace($to_replace, $replacement, $final_message);
$final_message_text = str_replace($to_replace, $replacement, $final_message_text);

$ar_message = str_replace($to_replace, $replacement, $ar_message);
$ar_message_text = str_replace($to_replace, $replacement, $ar_message_text);

}

if($sender_subject == '') $sender_subject = CUSTOM_MAIL_SUBJECT;

$mail->Subject = $sender_subject;
$mail->MsgHTML($final_message);
$mail->AltBody = $final_message_text;

$mail->CharSet = MAIL_CHARSET;

/* --- Send the mail --- */

if($mail->Send()) {

$error['status'] = 0; // Mail sent

if($escts == 1 && defined('ESCTS_TEXT')) {

  if($sender_email) {
      
	  $mail->ClearAddresses();

      $mail->From    = $sender_email;
      $mail->FromName = $sender_name;

      $mail->AddAddress($sender_email, $sender_name);

      $mail->Subject = $sender_subject;
      $mail->MsgHTML($final_message);
      $mail->AltBody = $final_message_text;

      $mail->CharSet = MAIL_CHARSET;

      $mail->Send();
  }
}

if(AUTO_RESPONDER == 1) {

	  $mail->ClearAddresses();

	  $mail->From    = AUTO_RESPONDER_FROM_EMAIL;
	  $mail->FromName = AUTO_RESPONDER_FROM_NAME;

	  $mail->AddAddress($sender_email, $sender_name);

	  $mail->Subject = $ar_subject;
	  $mail->MsgHTML($ar_message);
	  $mail->AltBody = $ar_message_text;

	  $mail->CharSet = MAIL_CHARSET;

// Send auto responder only if the message was sent
$mail->Send();
}

} else {
$error['status'] = 2; // Mail cannot be sent (internal error)
}

} else {
$error['status'] = 1; // Errors found
}

echo json_encode($error); // output the result that will be processed by init.php

}
?>