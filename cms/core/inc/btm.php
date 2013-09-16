
<div class="footer">        

    <?php  if ($CurrentUser->logged_in()) { ?>	
	<div class="version">
	    <?php
	        if (($CurrentUser->has_priv('perch.updatenotices')) && (version_compare($Perch->version, $Settings->get('latest_version')->settingValue(), '<'))) {
	            echo '<a href="http://grabaperch.com/update">' . sprintf(PerchLang::get('You are running version %s - a newer version is available.'), $Perch->version) . '</a>';
	        }
	    ?>
	</div>
	<?php  } ?>
	<div class="credit">
        <?php
            if (!$Settings->get('hideBranding')->settingValue()) {
        ?>
		<p><a href="http://grabaperch.com"><img src="<?php echo PERCH_LOGINPATH; ?>/core/assets/img/perch.png" width="35" height="12" alt="Perch" /></a>
		<?php echo PerchUtil::html(PerchLang::get('by')); ?> <a href="http://edgeofmyseat.com">edgeofmyseat.com</a></p>
        <?php
            }else{
                echo '&nbsp;';
            }
    	?>
	</div>
</div>
</div>
<?php
	if ($CurrentUser->logged_in()) {
?>
<script type="text/javascript">
	Perch.Lang.init({
	    'Apps':'<?php echo addslashes(PerchLang::get('Apps')); ?>',
	    'Save':'<?php echo addslashes(PerchLang::get('Save')); ?>',
	    'Undo':'<?php echo addslashes(PerchLang::get('Undo')); ?>',
	    'Image title':'<?php echo addslashes(PerchLang::get('Image title')); ?>',
	    'File title':'<?php echo addslashes(PerchLang::get('File title')); ?>',
	    'File to upload':'<?php echo addslashes(PerchLang::get('File to upload')); ?>',
	    'Style':'<?php echo addslashes(PerchLang::get('Style')); ?>',
	    'Upload':'<?php echo addslashes(PerchLang::get('Upload')); ?>',
	    'or':'<?php echo addslashes(PerchLang::get('or')); ?>',
	    'Cancel':'<?php echo addslashes(PerchLang::get('Cancel')); ?>',
	    'New':'<?php echo addslashes(PerchLang::get('New')); ?>',
	    'Mixed':'<?php echo addslashes(PerchLang::get('Mixed')); ?>',
	    'Toggle sidebar':'<?php echo addslashes(PerchLang::get('Toggle sidebar')); ?>',
	    'Delete this item?':'<?php echo addslashes(PerchLang::get('Delete this item?')); ?>',
	    'Add Another':'<?php echo addslashes(PerchLang::get('Add Another')); ?>'
	});
	Perch.token = '<?php $CSRFForm = new PerchForm('csrf'); echo $CSRFForm->get_token(); ?>';
	Perch.path = '<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>';
	<?php echo $Perch->get_javascript_blocks(); ?>
</script>
<?php
        echo $Perch->get_foot_content();
    }
    if (PERCH_DEBUG) {
    	PerchUtil::debug('Queries: '. PerchDB_MySQL::$queries);
    	PerchUtil::output_debug(); 
    }
?>
</body>
</html>