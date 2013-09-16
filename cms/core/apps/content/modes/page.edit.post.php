<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>

    <p>
        <?php echo PerchLang::get('These are the options for this page. Each page has a title, and navigation text which can be different from the title. The navigation text is used in menus and is often shorter than the main page title. '); ?>
    </p>

    <p>
        <?php echo PerchLang::get('If the link for the page is incorrect, it can be fixed using the Path option. You shouldn\'t change that unless you are sure what you\'re doing. An incorrect value could break links in your navigation. Changing this value doesn\'t move the page itself.'); ?>
    </p>

    <p>
        <?php echo PerchLang::get('If you want to hide the page from navigation, just check the checkbox. This will also hide any pages below this one when displaying a navigation tree.'); ?>
    </p>


<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<?php include ('_subnav.php'); ?>



	    <h1><?php 
	            printf(PerchLang::get('Editing %s Page'),' &#8216;' . PerchUtil::html($Page->pageNavText()) . '&#8217; '); 
	        ?></h1>

    
    <?php echo $Alert->output(); ?>

	<ul class="smartbar">
        <li><a href="<?php echo PERCH_LOGINPATH . '/core/apps/content/page/?id='.PerchUtil::html($Page->id());?>">Regions</a></li>
		<?php
			if ($CurrentUser->has_priv('content.pages.edit')) {
	            echo '<li class="selected"><a href="'.PERCH_LOGINPATH . '/core/apps/content/page/edit/?id='.PerchUtil::html($Page->id()).'">' . PerchLang::get('Page Options') . '</a></li>';
	        }
		?>
    </ul>
    
    <?php
        if ($details['pageSubpagePath']==''){
            $details['pageSubpagePath'] = PerchUtil::strip_file_name($Page->pagePath());
        }
        
        $Pages->find_site_path();
        if (!file_exists(PerchUtil::file_path(PERCH_SITEPATH.$details['pageSubpagePath']))) {
            $Alert->set('error', PerchLang::get('Subpage folder does not exist'));
            $Alert->output();
            PerchUtil::debug(PerchUtil::file_path(PERCH_SITEPATH.$details['pageSubpagePath']));
        }
    ?>

    <h2><?php echo PerchLang::get('Details'); ?></h2>
    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="magnetic-save-bar">

        <div class="field">
        	<?php echo $Form->label('pageTitle', 'Page title'); ?>
        	<?php echo $Form->text('pageTitle', $Form->get($details, 'pageTitle')); ?>
        </div>

        <div class="field">
            <?php echo $Form->label('pageNavText', 'Navigation text'); ?>
            <?php echo $Form->text('pageNavText', $Form->get($details, 'pageNavText')); ?>
        </div>

        <div class="field">
            <?php echo $Form->label('pagePath', 'Path'); ?>
            <?php echo $Form->text('pagePath', $Form->get($details, 'pagePath')); ?>
        </div>
        
        <div class="field">
            <?php echo $Form->label('pageParentID', 'Parent page'); ?>
            <?php 
                $opts = array();
                $opts[] = array('label'=>PerchLang::get('Top level'), 'value'=>0);
                $pages = $Pages->get_page_tree();

                if (PerchUtil::count($pages)) {
                    foreach($pages as $Item) {
                        if ($Item->role_may_create_subpages($CurrentUser)) {
                            $disabled = false;

                            if ($Item->id()==$Page->id()) $disabled=true;
                            
                        }else{
                            $disabled = true;
                        }
                        
                        $depth = $Item->pageDepth()-1;
                        if ($depth < 0 ) $depth = 0;
                        
                        $opts[] = array('label'=>str_repeat('-', $depth).' '.$Item->pageNavText(), 'value'=>$Item->id(), 'disabled'=>$disabled);
                    }
                }
                
            
                echo $Form->select('pageParentID', $opts, $Form->get($details, 'pageParentID')); 
            ?>
        </div>
        
        <?php
            $members = $Perch->get_app('perch_members');
        ?>


        <div class="field <?php echo $members ? '' : 'last' ?>">
            <?php echo $Form->label('pageHidden', 'Hide from main navigation'); ?>
            <?php echo $Form->checkbox('pageHidden', '1', $Form->get($details, 'pageHidden')); ?>
        </div>

        <?php
            if ($members) {
        ?>
        <div class="field last">
            <?php echo $Form->label('pageAccessTags', 'Restrict access to members with tags'); ?>
            <?php echo $Form->tags('pageAccessTags', $Form->get($details, 'pageAccessTags')); ?>
        </div>
<?php
        }


    if (PerchUtil::count($navgroups)) {
        echo '<h2>'.PerchLang::get('Navigation groups').'</h2>';

        echo '<div class="field last">';

            $opts = array();
            
            $vals = $Page->get_navgroup_ids();

            if (!$vals) $vals = array();

            foreach($navgroups as $Group) {
                $opts[] = array('label'=>$Group->groupTitle(), 'value'=>$Group->id());
            }
        
            
            echo $Form->checkbox_set('navgroups', 'Page belongs to', $opts, $vals, $class='', $limit=false);
        
        echo '</div>';

    }

?>
        
<?php
    if ($CurrentUser->has_priv('content.pages.configure')) {
?>        
        <h2><?php echo PerchLang::get('Subpages'); ?></h2>

        <div class="field">
            <?php
                $opts = array();
                $opts[] = array('label'=>PerchLang::get('Everyone'), 'value'=>'*', 'class'=>'single');
                
                $vals = explode(',', $Page->pageSubpageRoles());

                if (PerchUtil::count($roles)) {
                    foreach($roles as $Role) {
                        $tmp = array('label'=>$Role->roleTitle(), 'value'=>$Role->id());

                        if ($Role->roleMasterAdmin()) {
                            $tmp['disabled'] = true;
                            //if (!in_array('*', $vals)) 
                                $vals[] = $Role->id();
                        }

                        $opts[] = $tmp;
                    }
                }
                
                echo $Form->checkbox_set('subpage_roles', 'May be created by', $opts, $vals, $class='', $limit=false);
            
            
            ?>
        </div>
        
    
        <div class="field last">
            <?php echo $Form->label('pageSubpagePath', 'Subpage folder'); ?>
            <?php 
                
            
                echo $Form->text('pageSubpagePath', $Form->get($details, 'pageSubpagePath')); 
            ?>
        </div>
<?php
    } // content.pages.configure
?>

        <p class="submit">
            <?php echo $Form->submit('btnsubmit', 'Submit', 'button'); ?>
        </p>
        
    </form>

    <?php
        if ($created!==false) {
            echo '<img src="'.PerchUtil::html($Page->pagePath()).'" width="1" height="1" />';
        }
    ?>
<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>
