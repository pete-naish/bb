<?php 
	include('PerchContent_Pages.class.php');
	include('PerchContent_Page.class.php');

	$Pages = new PerchContent_Pages;
	$pages = $Pages->get_by_parent(0);

?>
<div class="widget">
	<h2>
		<?php 
			echo PerchLang::get('Pages');
			if ($CurrentUser->has_priv('content.pages.create')) {
				echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/core/apps/content/page/add/').'" class="add button">'.PerchLang::get('Add Page').'</a>';
			}
		?>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($pages)) {
				echo '<ul>';
				foreach($pages as $Page) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/core/apps/content/page/?id='.$Page->id()).'">';
							echo PerchUtil::html($Page->pageNavText());
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>

</div>