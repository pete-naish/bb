<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
    
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
	<?php include ('_subnav.php'); ?>


    <h1><?php echo PerchLang::get('Textile formatting'); ?></h1>


    <?php echo $Alert->output(); ?>

    <div class="helptext">

    	<h2>About Textile formatting</h2>

    	 <p>Textile is a simple syntax to mark-up text in your pages. It is enabled on any field that displays the Textile link.</p>

		<h3>Phrase modifiers:</h3>
		<p>
		<em>_emphasis_</em><br />
		<strong>*bold*</strong><br />
		<cite>??citation??</cite><br />
		-<del>deleted text</del>-<br />

		+<ins>inserted text</ins>+<br />
		^<sup>superscript</sup>^<br />
		~<sub>subscript</sub>~<br />
		<span>%span%</span><br />
		<code>@code@</code><br />
		</p>

		<h3>Block modifiers:</h3>
		<p>
		<b>h1.</b> Level 1 heading<br />
		<b>h2.</b> Level 2 heading<br />
		<b>h3.</b> Level 3 heading<br />
		<b>h4.</b> Level 4 heading<br />
		<b>bq.</b> Blockquote<br />

		<b>p.</b> Paragraph<br />
		<b>bc.</b> Block code<br />
		<b>pre.</b> Pre-formatted<br />
		<b>#</b> Numeric list<br />
		<b>*</b> Bulleted list<br />

		</p>

		<h3>Links:</h3>
		<p>
		"linktext":http://&#8230;<br />
		</p>

		<h3>Punctuation:</h3>
		<p>
		<b>em -- dash</b> &rarr; em &#8212; dash<br />
		<b>en - dash</b> &rarr; en &#8211; dash<br />
		<b>foo(tm)</b> &rarr; foo&#8482;<br />
		<b>foo(r)</b> &rarr; foo&#174;<br />
		<b>foo(c)</b> &rarr; foo&#169;<br />
		</p>

	    

	</div>

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>