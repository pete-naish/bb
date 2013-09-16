<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
    
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
	<?php include ('_subnav.php'); ?>


    <h1><?php echo PerchLang::get('Textile formatting'); ?></h1>


    <?php echo $Alert->output(); ?>

    <div class="helptext">

    	<h2>About Markdown formatting</h2>

    	<p>Markdown is a simple syntax to mark-up text in your pages. It is enabled on any field that displays the Markdown link.</p>

		<h3>Phrase modifiers:</h3>
		<p>
		<em>*emphasis*</em><br />
		<strong>**bold**</strong><br />

		</p>

		<h3>Block modifiers:</h3>
		<p>
		<b>#</b> Level 1 heading<br />
		<b>##</b> Level 2 heading<br />
		<b>###</b> Level 3 heading<br />
		<b>####</b> Level 4 heading<br />
		<b>&gt;</b> Blockquote<br />


		<b>-</b> Bulleted list<br />
		<b>1.</b> Numeric list<br />

		</p>

		<h3>Links:</h3>
		<p>
		[linktext](http://&#8230;)<br />
		</p>
	    

	</div>

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>