<?php
/* 
-----------------------------------------------------------------------------------------------------
Make sure the 'contact-app' directory is located inside the same directory where the form script is
-----------------------------------------------------------------------------------------------------
*/

include 'contact-app/common.php';

if($showForm) {

// Was the form posted? (usually if JavaScript is disabled)

if(isSet($_POST['submit'])) {

//echo '<pre>'; print_r($_POST); echo '</pre>';

include 'contact-app/parse2.php';
}
?>

<div id="acf-area">
	<div id="acf-note" <?php if($status) echo 'style="display: block;"'; ?>><?php if($status) echo $status; ?></div>
	<div id="acf-fields">


		<form id="ajax-contact-form" method="post" action="">

			<fieldset>
				<?php
					foreach($formFields as $key => $value) {

					$name = $value['name'];
					$mandatory = $value['mandatory'];
					$type = $value['type'];
					$placeholder = $value['placeholder'];

					$attributes = $value['attributes'];

					$classAttribute = ($mandatory == 1) ? 'class="required" ' : '';

					$attributes_html = '';


					if(is_array($formFields[$key]['attributes'])) {

						foreach($formFields[$key]['attributes'] as $attribute => $value) {

					      $attributes_html .= $attribute.'="'.$value.'" ';

						}

					}

					?>

						<div>
							<label for="<?php echo $key; ?>"><?php echo $name; ?></label>

							<?php
								if($type == 'input') { 
								?>
									<div><input <?php echo $classAttribute . $attributes_html; ?> placeholder="<?php echo $placeholder; ?>" type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $_POST[$key]; ?>" /></div>
								<?php
							}

							if(is_array($type['select'])) {  ?>
								<div><select <?php echo $classAttribute . $attributes_html; ?> name="<?php echo $key; ?>" id="<?php echo $key; ?>">

								<option value="">Please choose a subject...</option>

								<?php
								foreach($formFields[$key]['type']['select'] as $value) { if($_POST[$key] == $value) $selected = 'selected'; else $selected = ''; echo '<option '.$selected.'>'.$value.'</option>'."\n"; }
								?>
								</select></div>
							<?php
							}
							 
							if($type == 'textarea') { ?>
								<div><textarea <?php echo $classAttribute . $attributes_html; ?> name="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo $_POST[$key]; ?></textarea></div>
							<?php } ?>

						</div>

				<?php } ?>

				<?php if(USE_CAPTCHA == 1) { ?>
					<div id="main_sec_div"><label for="security_code">Enter code:</label>

						<div id="sec_div_one">

							<div id="captcha_div"><img width="<?php echo CAPTCHA_IMAGE_WIDTH; ?>" height="<?php echo CAPTCHA_IMAGE_HEIGHT; ?>" id="captcha" src="/inc/contact-app/captcha.php" alt="" />&nbsp;<a id="captcha-refresh" href="#"></a></div>  

							<div id="input_box_div"><input size="<?php echo $securityCodeSettings['size']; ?>" class="required <?php echo $securityCodeSettings['class']; ?>" type="text" id="security_code" name="security_code" /></div> 

						</div>

					</div>

					<div id="sec_div_two">
						<p id="verified">
						<?php echo $securityCodeSettings['verified']; ?>
						</p>
					</div>

				<?php } ?>


				<?php if(defined('ESCTS_TEXT')) { ?>
					<div class="escts"><input id="escts" type="checkbox" name="escts" value="1">&nbsp;<label class="escts" for="escts"><?php echo ESCTS_TEXT; ?></label></div>
				<?php } ?>

				<input id="submit-button" class="button" type="submit" name="submit" value="Send" /><div id="ajax-loading">Submitting...</div>

				<input type="hidden" name="success_sent" id="success_sent" value="0" />
			</fieldset>
		</form>

	</div>

</div>

<?php
}
?>
