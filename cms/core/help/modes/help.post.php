<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
    
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
	<?php include ('_subnav.php'); ?>


    <h1><?php echo PerchLang::get('Help &amp; Support'); ?></h1>



    <?php echo $Alert->output(); ?>

    <div class="helptext">

	    <p>Welcome to the help page for your content administration panel.</p>

	    <h2 id="getstarted">Editing Content on your website</h2>
	    <p>
	        To get started editing content click the Pages link in the header of the administration section. This page displays all of the available editable pages across your website.
	    </p>
	    <p>
	        If a page has an arrow to the left, that means you can click the arrow to see pages beneath this page in the website structure. If this list is too long you can use the filter bar at the top to display content by type - for example displaying only text blocks across the site.
	    </p>
	    <p>
	    	Click on the name of a page to see a list of editable regions on that page.
	    </p>
	    
	    <h2 id="regions">Edit a region</h2>
	    
	    <p>To edit any region click the region name. You can then complete the form changing the content as required. Click the Save button to make the change and the content on your website will be immediately updated.</p>

	    <h2 id="multiple">Regions that allow multiple blocks of content</h2>
	    <p>Some regions will allow multiple items of content. These can be ordered with the newest block posted at the top or at the bottom of the content. Blocks may also be deleted.</p>
	    <p>For multiple-item regions, clicking Save and Add Another will append an additional item to the region.</p>

	</div>

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>