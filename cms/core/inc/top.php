<?php
    $Settings->get('headerColour')->settingValue();

    // Check for updates
    if (!$auth_page && !$Settings->get('update_'.$Perch->version)->val()) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/update/');
    }

    // Help markup as used by apps etc
    $Perch->help_html = '';
    $help_html = '';

    header('Content-Type: text/html; charset=utf-8');

?><!DOCTYPE html>
<html lang="<?php echo $Settings->get('lang')->settingValue(); ?>">
<head>
	<meta charset="utf-8" />
	<title><?php 
	    echo PerchUtil::html($Perch->page_title);
	    
	    if (!$Settings->get('hideBranding')->settingValue()) {
	        echo PerchUtil::html(' - ' . PerchLang::get('Perch')); 
	    }
	?></title>
<?php
	if ($CurrentUser->logged_in()) {
		echo '<meta name="viewport" content="width=device-width" />';
	}else{
		echo '<meta name="viewport" content="width=540" />';
	}
	
  if ($CurrentUser->logged_in()) { ?>
	<!--[if lt IE 9]><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/iebase.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" /><![endif]-->
	<link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/v2.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/720.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" media="only screen and (min-width: 720px)" />
	<!--[if lt IE 9]><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/720.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/ie7.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" /><![endif]-->
	<!--[if IE 6]><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/ie6.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" /><![endif]-->	
	<script src="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/js/jquery-ui.js" type="text/javascript"></script>
	<script src="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/js/perch.js?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/javascript"></script>
<?php }else{ ?>
	<link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/login.css?v=<?php echo PerchUtil::html($Perch->version); ?>" type="text/css" />   
<?php }
    if (PERCH_DEBUG) { ?><link rel="stylesheet" href="<?php echo PerchUtil::html(PERCH_LOGINPATH); ?>/core/assets/css/debug.css" type="text/css" /><?php  }

	if ($Settings->get('headerColour')->settingValue()) {
		if ($CurrentUser->logged_in() || trim($Settings->get('headerColour')->settingValue())!='#ffffff') {
	?>  <style type="text/css">.topbar { background-color: <?php echo PerchUtil::html(rtrim($Settings->get('headerColour')->settingValue(), ';')); ?>; }</style>
<?php
		}
		$db_error = false;
	}else {
		// header colour is always set - it's part of the install. If it's not there, the database connection is probably down;
		$db_error = true;
	}

    if ($CurrentUser->logged_in()) {
        $javascript = $Perch->get_javascript();
        foreach($javascript as $js) {
            echo "\t".'<script type="text/javascript" src="'.PerchUtil::html($js).'"></script>'."\n";
        }
        
        $stylesheets = $Perch->get_css();
        foreach($stylesheets as $css) {
            echo "\t".'<link rel="stylesheet" href="'.PerchUtil::html($css).'" type="text/css" />'."\n";
        }
        
        echo $Perch->get_head_content();
    }
    
    
    if (file_exists(PERCH_PATH.'/addons/plugins/ui/_config.inc')) {
        include PERCH_PATH.'/addons/plugins/ui/_config.inc';
    }

	if (!$Settings->get('hideBranding')->settingValue()) {
		echo '<link rel="shortcut icon" href="'.PERCH_LOGINPATH.'/core/assets/img/favicon.ico" />';
	}
?>
</head>
<?php
    if ($CurrentUser->logged_in()) {
?>
<body class="<?php
	if (isset($_COOKIE['cmssb']) && $_COOKIE['cmssb']=='1') {
		echo 'sidebar-closed ';
	}else{
		echo 'sidebar-open ';
	}
?>">
<?php
    }else{
?>
<body class="login">
<?php        
    }
?>