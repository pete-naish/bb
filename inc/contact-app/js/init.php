<?php
/* 
Author: Gabriel Comarita
Author's Website: http://www.bitrepository.com/

Copyright (c) BitRepository.com - You do not have rights to reproduce, republish, redistribute or resell this product without permission from the author or payment of the appropiate royalty of reuse.

* Keep this notice intact for legal use *
*/

include '../common.php';

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
  include_once '../libraries/javascript.packer/class.JavaScriptPacker.php4.php';
} else {
  include_once '../libraries/javascript.packer/class.JavaScriptPacker.php5.php';
}

function JavaScriptCompress($buffer) {
   $myPacker = new JavaScriptPacker($buffer);
   return $myPacker->pack();
}

header("Content-type: application/x-javascript");
?>

/* 
Author: Gabriel Comarita
Author's Website: http://www.bitrepository.com/

Copyright (c) BitRepository.com
*/

<?php
ob_start("JavaScriptCompress");
?>

<?php if($jQueryPrefix != '$') { ?>

// Ensure that JQuery won't conflict with other libraries
var <?php echo $jQueryPrefix; ?> = jQuery.noConflict();

<?php } ?>

<?php echo $jQueryPrefix; ?>(function() { // When DOM is ready

    // Important for JS validation
    var total_required_inputs = <?php echo $jQueryPrefix; ?>(":input.required").length; /* (usually name, email, subject, message & security code) */

    // Are we using the basic form with no captcha? Then decrease the number of total required inputs
    if((<?php echo $jQueryPrefix; ?>("#security_code").length == 0)) {
    total_required_inputs--;
    }

    // Preload Icons
	// This way they will show instantly without waiting for the browser to load them (for instance the 'ajax loading spinner')
	
	img1 = new Image(18, 15);
    img1.src = '<?php echo PATH_TO_IMAGES; ?>ajax-loader.gif';
    
	img2 = new Image(22, 22);
    img2.src = '<?php echo PATH_TO_IMAGES; ?>icon-dialog-error.png';
	
	img3 = new Image(22, 22);
    img3.src = '<?php echo PATH_TO_IMAGES; ?>icon-button-ok.png';

	img4 = new Image(16, 16);
    img4.src = '<?php echo PATH_TO_IMAGES; ?>icon-refresh.png';

    /*
    -------------------------------------------------
    Is the form (ID: 'ajax-contact-form') submitted?
    -------------------------------------------------
    */

    <?php echo $jQueryPrefix; ?>('#ajax-contact-form').submit(function() {

    <?php if(JS_REALTIME_VALIDATOR == 1) { 
		
    foreach($formFields as $key => $value) {

		$mandatory = $value['mandatory'];

		if($mandatory == 1) {
            echo 'check_'.$key.'();'."\n";
		}
    }
   ?>

       check_security_code();
       check_status();

       if(<?php echo $jQueryPrefix; ?>(".ok").length < total_required_inputs) {
			  return false;
	   }
    
    <?php 	
    }
    ?>

	// Hide 'Submit' Button
    <?php echo $jQueryPrefix; ?>('#submit-button').hide();

    // Show GIF Spinning Rotator
    <?php echo $jQueryPrefix; ?>('#ajax-loading').show();

    var formData = <?php echo $jQueryPrefix; ?>(this).serialize(); // Serializes a set of input elements into a string data (example: first_name=John&last_name=Doe)

    <?php echo $jQueryPrefix; ?>.ajax({
      type: "POST",
      url: '<?php echo PATH_TO_PHP_PROCESS_FILE; ?>',
      data: formData,
      success: function(response) {

	  //alert(response);

	   var possible_error = 'Could not instantiate mail function.';

	   if(response.indexOf(possible_error) != '-1') {
	   var result = '<div class="notification_error"><?php echo $form_notifications['mail_cannot_be_sent_e']; ?><br /><br />'+ possible_error +'</div>';

	<?php
    if(HIDE_FORM_AFTER_SUBMIT == 1) {
    ?>
           <?php echo $jQueryPrefix; ?>("#acf-fields").hide();
	<?php } ?>

	   } else {

        var status = <?php echo $jQueryPrefix; ?>.evalJSON(response).status;

       if(status == 0) { // Message sent

        <?php if(CUSTOM_THANK_YOU_URL === false) { ?>

          var result = '<div class="notification_ok"><?php echo $form_notifications['message_sent_s']; ?></div>';

          <?php echo $jQueryPrefix; ?>('#success_sent').val(1);

          <?php if(HIDE_FORM_AFTER_SUBMIT == 1) { ?>

                <?php echo $jQueryPrefix; ?>("#acf-fields").hide();

          <?php } else { ?>

			  <?php if(CLEAR_FIELDS_AFTER_SUCCESS_SUBMIT == 1) { ?>

              <?php echo $jQueryPrefix; ?>('#ajax-contact-form')[0].reset();
              <?php echo $jQueryPrefix; ?>(':input:not(:hidden)').removeClass('ok');

			  <?php }
			  }

          } else { 
              echo 'window.location.replace("'.CUSTOM_THANK_YOU_URL.'");';
          } 
          ?>

       }
       else if(status == 1) { // Errors found?

	          var result = '<div class="notification_error"><?php echo $form_notifications['correct_errors_e']; ?><br /><br />';
			  
			  <?php if(SHOW_ERRORS_IN_ITALICS) { ?> result += '<em>'; <?php } ?>

			  // First, remove all errors to avoid adding the same errors twice in the page
			  // If any errors are found, the script will change the 'class' value(s) (error)

		      <?php echo $jQueryPrefix; ?>('div.error').remove();

		      <?php
		      foreach($formFields as $key => $value) {

		         echo "if(".$jQueryPrefix.".evalJSON(response).".$key."_none) {
			       ".$jQueryPrefix."('#".$key."').addClass('error').removeClass('ok');

		            result += '".$formFields[$key]['errors']['none']."<br />';
			  
			     } else if(".$jQueryPrefix.".evalJSON(response).".$key."_invalid) {
			 
			        result += '".$formFields[$key]['errors']['invalid']."<br />';

			     } else if(".$jQueryPrefix.".evalJSON(response).".$key."_min_chars) {
			 
			        result += '".str_replace('[min_chars]', $formFields[$key]['validation']['min_chars'], $formFields[$key]['errors']['min_chars'])."<br />';

			     } else  {
                    ".$jQueryPrefix."('#".$key."').addClass('ok').removeClass('error');
                 }";

		      }
		      ?>

			  if(<?php echo $jQueryPrefix; ?>.evalJSON(response).security_code == 1) {
			  <?php echo $jQueryPrefix; ?>('#security_code').addClass('error').removeClass('ok');
			  
              result += '<?php echo $form_notifications['security_code_e']; ?><br />';

			  } else if(<?php echo $jQueryPrefix; ?>.evalJSON(response).security_code == 2) {
			  <?php echo $jQueryPrefix; ?>('#security_code').addClass('error').removeClass('ok');
			  
			  result += '<?php echo $form_notifications['security_code_i_e']; ?><br />';

              } else {

              <?php echo $jQueryPrefix; ?>('#sec_div_one').hide();
			  <?php echo $jQueryPrefix; ?>('#sec_div_two').show();

			  <?php echo $jQueryPrefix; ?>('#captcha_div').hide();
              <?php echo $jQueryPrefix; ?>('#sc_error').remove();

              }

			  <?php if(SHOW_ERRORS_IN_ITALICS) { ?> result += '</em>'; <?php } ?>
			  
			  result += '</div>';

			  // Mail cannot be sent?
       } else if(status == 2) {
	         var result = '<div class="notification_error"><?php echo $form_notifications['mail_cannot_be_sent_e']; ?></div>';

			 <?php if(HIDE_FORM_AFTER_SUBMIT == 1) { ?> <?php echo $jQueryPrefix; ?>("#acf-fields").hide(); <?php } ?>
	   }

	   }

              // Hide GIF Spinning Rotator
	          <?php echo $jQueryPrefix; ?>('#ajax-loading').hide();
	         
	          // Show 'Submit' Button
	          <?php echo $jQueryPrefix; ?>('#submit-button').show();

			  // Could be notification error or a notification that the form has been submitted successfully / Show the notification with a "fade in" effect
	          <?php echo $jQueryPrefix; ?>('#acf-note').html(result).slideDown();

}

});

return false; // prevent the form from being submitted in the classical way

        });

function new_captcha()
{
var c_currentTime = new Date();
var c_miliseconds = c_currentTime.getTime();

document.getElementById('captcha').src = '<?php echo SCRIPT_PATH; ?>captcha.php?x='+ c_miliseconds;

return false;
};

<?php echo $jQueryPrefix; ?>('#captcha-refresh').bind('click', new_captcha);

<?php if(JS_REALTIME_VALIDATOR == 1) { ?>

/* [RealTime Validation] */

<?php
// Parse each field

foreach($formFields as $key => $value) {

	$name = $value['name'];
	$validation = $value['validation'];
	$mandatory = $value['mandatory'];

	if(SHOW_ERRORS_IN_ITALICS == 1) {
		$formFields[$key]['errors']['none'] = '<i>'.$formFields[$key]['errors']['none'].'</i>';
		$formFields[$key]['errors']['invalid'] = '<i>'.$formFields[$key]['errors']['invalid'].'</i>';
		$formFields[$key]['errors']['min_chars'] = '<i>'.$formFields[$key]['errors']['min_chars'].'</i>';
	}
?>

<?php if($mandatory == 1) { ?>

var check_<?php echo $key; ?> = function() {


<?php if($validation['email'] == 1) { ?> var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i; <?php } ?>

<?php if($validation['basic'] == 1) { ?>

if(<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').val() == '') {

removeErrors('<?php echo $key; ?>', 'none');

<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').addClass('error').removeClass('ok');

<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').after('<div id="<?php echo $key; ?>_error" class="error"><?php echo $formFields[$key]['errors']['none']; ?></div>');

check_status();

return false;
} 
<?php } ?>

<?php if($validation['email'] == 1) { ?>

if (!filter.test(<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').val())) {

removeErrors('<?php echo $key; ?>', 'none');

<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').addClass('error').removeClass('ok').after('<div id="<?php echo $key; ?>_error_invalid" class="error"><?php echo $formFields[$key]['errors']['invalid']; ?></div>');

check_status();

return false;
}

<?php } ?>

<?php if($validation['min_chars'] > 0) {
$min_chars = $validation['min_chars'];
?>

if (<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').val().length < <?php echo $min_chars; ?>) { // if the message's legth is less than 15 characters

removeErrors('<?php echo $key; ?>', 'none');

<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').addClass('error').removeClass('ok').after('<div id="<?php echo $key; ?>_error_min_chars" class="error"><?php echo str_replace('[min_chars]', $min_chars, $formFields[$key]['errors']['min_chars']); ?></div>');

check_status();

return false;
}

<?php } ?>

else {

removeErrors('<?php echo $key; ?>', 'slideUp');

<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').addClass('ok').removeClass('error');
}



};

<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').bind('change', check_<?php echo $key; ?>);
<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').bind('blur', function() { if(<?php echo $jQueryPrefix; ?>('#<?php echo $key; ?>').val()) { check_<?php echo $key; ?>(); } });

<?php } ?>

<?php
}
?>

/*
-----------------
Security Code
-----------------
*/

var check_security_code = function() {

if (<?php echo $jQueryPrefix; ?>('#captcha_div').is(':visible')) {

<?php echo $jQueryPrefix; ?>('#sc_error').remove();
	

if(<?php echo $jQueryPrefix; ?>('#security_code').val() == '') {

<?php echo $jQueryPrefix; ?>('#security_code').addClass('error').removeClass('ok');
<?php echo $jQueryPrefix; ?>('#sec_div_one').after('<div id="sc_error" class="error"><?php echo $form_notifications['security_code_e']; ?></div>');

check_status();

} else {

var c_currentTime = new Date();
var c_miliseconds = c_currentTime.getTime();

var validCode = <?php echo $jQueryPrefix; ?>('#security_code').val();

/* [Start] AJAX Call */

<?php echo $jQueryPrefix; ?>.ajax({ url: '<?php echo SCRIPT_PATH; ?>verify-code.php?x='+ c_miliseconds, 
	     data: "security_code="+ validCode,
	     type: 'post', 
	     datatype: 'html', 
	     success: function(outData) { 

	      	          if(outData != 1) {

	      	            if(<?php echo $jQueryPrefix; ?>("#sc_error.error").length == 0) {
	      	                <?php echo $jQueryPrefix; ?>('#security_code').addClass('error').removeClass('ok');
	      	                <?php echo $jQueryPrefix; ?>('#sec_div_one').after('<div id="sc_error" class="error"><?php echo $form_notifications['security_code_i_e']; ?></div>');

							check_status();
	      	            }

	      	          } else {

                      <?php echo $jQueryPrefix; ?>('#security_code').remove();

					  <?php echo $jQueryPrefix; ?>('#sec_div_one').hide(); 
					  <?php echo $jQueryPrefix; ?>('#captcha_div').hide(); 

                      <?php echo $jQueryPrefix; ?>('#sec_div_two').fadeIn('fast', function() { 
					             <?php echo $jQueryPrefix; ?>('#submit-button').before('<input class="ok" type="hidden" name="security_code" id="security_code" value="'+ validCode +'" />'); 
					  });
					  
					  }
					  
		              }, 

	     error: function(errorMsg) { alert('Error occured: ' + errorMsg); }});

/* [End] AJAX Call */

}

}

};

var checkSecurityCodeLive = function() {

//alert(1);

var c_currentTime = new Date();
var c_miliseconds = c_currentTime.getTime();

var validCode = <?php echo $jQueryPrefix; ?>('#security_code').val();

/* [Start] AJAX Call */

<?php echo $jQueryPrefix; ?>.ajax({ url: '<?php echo SCRIPT_PATH; ?>verify-code.php?x='+ c_miliseconds, 
	     data: "security_code="+ validCode,
	     type: 'post', 
	     datatype: 'html', 
	     success: function(outData) { 

	      	          if(outData == 1) {

					  <?php echo $jQueryPrefix; ?>('#sc_error').remove();

                      <?php echo $jQueryPrefix; ?>('#security_code').remove();
					  
					  <?php echo $jQueryPrefix; ?>('#sec_div_one').hide(); 
					  <?php echo $jQueryPrefix; ?>('#captcha_div').hide(); 
					  <?php echo $jQueryPrefix; ?>('#main_sec_div').hide(); 
					  

                      <?php echo $jQueryPrefix; ?>('#sec_div_two').fadeIn('fast', function() { 
					             <?php echo $jQueryPrefix; ?>('#submit-button').before('<input class="ok" type="hidden" name="security_code" id="security_code" value="'+ validCode +'" />'); 
					  });
					  
					  <?php echo $jQueryPrefix; ?>('div').removeClass("highlighted");

					  check_status();

					  }
					  
		              }, 

	     error: function(errorMsg) { alert('Error occured: ' + errorMsg); }});

/* [End] AJAX Call */

};

var checkSecurityCodeIfNotNULL = function() {
if(<?php echo $jQueryPrefix; ?>('#security_code').val()) { check_security_code(); }
};

<?php echo $jQueryPrefix; ?>('#security_code').change(check_security_code);
<?php echo $jQueryPrefix; ?>('#security_code').blur(checkSecurityCodeIfNotNULL);
<?php echo $jQueryPrefix; ?>('#security_code').keyup(checkSecurityCodeLive);

<?php echo $jQueryPrefix; ?>(':input.required').bind('change blur keyup', check_status);

function check_status() {

// Necessary if the form was reseted

if(<?php echo $jQueryPrefix; ?>('#success_sent').val() == 1) { 
      <?php echo $jQueryPrefix; ?>('#acf-note').slideUp('slow');
	  
      <?php echo $jQueryPrefix; ?>('#acf-note').html('');

      <?php echo $jQueryPrefix; ?>('#success_sent').val(0); 
	  return true; 
} 

<?php if(SHOW_ERRORS_IN_ITALICS == 1) { ?>

<?php echo $jQueryPrefix; ?>("div[id$='_error']").addClass('styled');

<?php } ?>

if(<?php echo $jQueryPrefix; ?>("div.error").length > 0) { 
	// Show the top notice error
	<?php echo $jQueryPrefix; ?>('#acf-note').html('<div class="notification_error"><?php echo $form_notifications['correct_errors_e']; ?></div>').slideDown('slow');
}

if(<?php echo $jQueryPrefix; ?>("div.error").length == 0) { 
	<?php echo $jQueryPrefix; ?>('#acf-note').slideUp('slow'); // Hide the top notice error using a 'slide' effect (if necessary)
}

return true;

};

function removeErrors(keyField, mode) {

if(mode == 'slideUp') {

<?php echo $jQueryPrefix; ?>('#'+ keyField +'_error').slideUp("fast", function() { <?php echo $jQueryPrefix; ?>(this).remove(); } );
<?php echo $jQueryPrefix; ?>('#'+ keyField +'_error_invalid').slideUp("fast", function() { <?php echo $jQueryPrefix; ?>(this).remove(); } );
<?php echo $jQueryPrefix; ?>('#'+ keyField +'_error_min_chars').slideUp("fast", function() { <?php echo $jQueryPrefix; ?>(this).remove(); } );

} else {

<?php echo $jQueryPrefix; ?>('#'+ keyField +'_error').remove();
<?php echo $jQueryPrefix; ?>('#'+ keyField +'_error_invalid').remove();
<?php echo $jQueryPrefix; ?>('#'+ keyField +'_error_min_chars').remove();

}

};

   <?php if(HIGHLIGHT_FIELD_ZONE == 1) { ?>

    var fields = ["<?php echo implode('", "', array_keys($formFields)); ?>", "security_code"];

    <?php echo $jQueryPrefix; ?>.each(fields, function() {

	    if(this == 'security_code') {
	
	      <?php echo $jQueryPrefix; ?>('#' + this).focus(function() { <?php echo $jQueryPrefix; ?>(this).parent('div').parent('div').parent('div').addClass("highlighted"); })
				        .blur(function() { <?php echo $jQueryPrefix; ?>(this).parent('div').parent('div').parent('div').removeClass("highlighted"); });
        } else {
          <?php echo $jQueryPrefix; ?>('#' + this).focus(function() { <?php echo $jQueryPrefix; ?>(this).parent('div').parent('div').addClass("highlighted"); })
				        .blur(function() { <?php echo $jQueryPrefix; ?>(this).parent('div').parent('div').removeClass("highlighted"); });
	    }

    });

   <?php } ?>

<?php } ?>

});
<?php ob_end_flush(); ?>