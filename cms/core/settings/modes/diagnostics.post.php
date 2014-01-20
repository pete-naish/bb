<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
    
    <p><?php echo PerchLang::get('When raising a support ticket you should copy and paste the information on this page.'); ?></p>
   
    <h3><p><?php echo PerchLang::get('Understanding this report'); ?></p></h3>
    
    <p><?php echo PerchLang::get('The Diagnostics Report gives you useful advice about your set-up and also your hosting environment.'); ?></p>
    
    <p><?php echo PerchLang::get('The Health Check gives you a quick overview of the state of critical items like software versions and hosting configuration.'); ?></p>
    
    <p><?php echo PerchLang::get('Settings listed under Perch Information are part of Perch and generally things you can change.'); ?></p>
    
    <p><?php echo PerchLang::get('Settings listed under Hosting Settings are part of your hosting environment. Making a change to any of these - for example increasing the maximum allowable file upload size - is something that you would need to ask your hosting company about.'); ?></p>

<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<?php include ('_subnav.php'); ?>
    
    <h1><?php echo PerchLang::get('Viewing Diagnostic Information'); ?></h1>
	


    <?php echo $Alert->output(); ?>
    
    <h2><?php echo PerchLang::get('Diagnostics report'); ?></h2>
    
    <?php
        $max_upload   = (int)(ini_get('upload_max_filesize'));
        $max_post     = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $upload_mb    = min($max_upload, $max_post, $memory_limit);
    ?>

    <div class="info">
        <h3><?php echo PerchLang::get('Health check'); ?></h3>
        <ul class="importables">
            <?php 
                $DB = PerchDB::fetch();

                $messages = array();

                $newest_perch = $Settings->get('on_sale_version')->val();

                if ($newest_perch) {

                    if (version_compare($newest_perch, $Perch->version, '>')) {
                        $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sPerch is out of date.%s You are running Perch %s and the latest is %s. %sUpdate instructions%s', '<strong>', '</strong>', $Perch->version, $newest_perch, '<a href="http://grabaperch.com/update/" class="action">', '</a>'));
                    }

                    if (version_compare($newest_perch, $Perch->version, '<')) {
                        $messages[] = array('type'=>'success', 'text'=>PerchLang::get('%sAhead of the curve!%s You are running a pre-release version of Perch.', '<strong>', '</strong>'));
                    }

                    if (version_compare($newest_perch, $Perch->version, '=')) {
                        $messages[] = array('type'=>'success', 'text'=>PerchLang::get('%sPerch is up to date%s', '<strong>', '</strong>'));
                    }
                }

                if (version_compare(PHP_VERSION, '5.3', '<')) {
                    $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sPHP %s is very out of date.%s %sMore info%s', '<strong>', PHP_VERSION, '</strong>', '<a href="http://docs.grabaperch.com/docs/installing-perch/php" class="action">', '</a>'));
                }else if (version_compare(PHP_VERSION, '5.4', '<')){
                    $messages[] = array('type'=>'success', 'text'=>PerchLang::get('%sPHP %s version is okay, but a little out of date.%s Consider updating soon.', '<strong>', PHP_VERSION, '</strong>'));
                }else{
                    $messages[] = array('type'=>'success', 'text'=>PerchLang::get('%sPHP %s is up to date%s', '<strong>', PHP_VERSION, '</strong>'));
                }


                $mysql_version = $DB->get_server_info();

                if (version_compare($mysql_version, '5.0', '<')) {
                    $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sMySQL %s is supported, but out of date.%s Consider upgrading to at least 5.0', '<strong>', $mysql_version, '</strong>'));
                }else{
                    $messages[] = array('type'=>'success', 'text'=>PerchLang::get('%sMySQL %s is up to date%s', '<strong>', $mysql_version, '</strong>'));
                }

                $gd = extension_loaded('gd');
                $im = extension_loaded('imagick');

                if ($gd || $im) {
                    $messages[] = array('type'=>'success', 'text'=>PerchLang::get('%sImage processing available%s', '<strong>', '</strong>'));
                }else{
                    $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sNo image processing library.%s Consider installing GD or ImageMagick for resizing images.', '<strong>', '</strong>'));
                }

                if (!function_exists('json_encode')) {
                    $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sNo native JSON library.%s Consider installing the PHP JSON library if possible.', '<strong>', '</strong>'));
                }

                if (!is_writable(PERCH_RESFILEPATH)) {
                    $messages[] = array('type'=>'failure', 'text'=>PerchLang::get('%sResources folder is not writable%s', '<strong>', '</strong>'));
                }

                if ($max_upload<8) {
                    $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sFile upload size is low.%s You can only upload files up to %sM.', '<strong>', '</strong>', $max_upload));
                }

                if ($memory_limit<64) {
                    $messages[] = array('type'=>'warning', 'text'=>PerchLang::get('%sMemory limit is low.%s Memory use is limited to %sM, which could cause problems manipulating large images.', '<strong>', '</strong>', $memory_limit));
                }




                foreach($messages as $message) {
                    echo '<li class="icon '.$message['type'].'">'.$message['text'].'</li>';
                }
            ?>
        </ul>

		<h3><?php echo PerchLang::get('Perch information'); ?></h3>
        <ul>
            <li>Perch: <?php echo PerchUtil::html($Perch->version); ?></li>
            <li>Production mode: <?php 
                switch(PERCH_PRODUCTION_MODE) {
                    case PERCH_DEVELOPMENT:
                        echo 'Development';
                        break;
                    case PERCH_STAGING:
                        echo 'Staging';
                        break;
                    case PERCH_PRODUCTION:
                        echo 'Production';
                        break;
                }
                echo ' ('.PERCH_PRODUCTION_MODE.')';
            ?></li>
            <?php
                $apps_list = $Perch->get_apps();
                $apps = array();
                echo '<li>Installed apps: ';
                if (PerchUtil::count($apps_list)) {
                    foreach($apps_list as $app) {
                        $apps[] = PerchUtil::html($app['id'].($app['version'] ? ' ('.$app['version'].')':''));
                    }
                    echo implode(', ', $apps);
                }else{
                    echo 'none.';
                }
                echo '</li>';
            
            ?>
			<?php
                
                echo '<li>DB driver: '.PerchDB::$driver.'</li>';
                $sql = 'SHOW TABLES';
                $rows = $DB->get_rows($sql);
                if (PerchUtil::count($rows)) {
                    $tables = array();
                    
                    foreach($rows as $row) {
                        foreach($row as $key=>$val) {
                            $tables[] =  PerchUtil::html($val);
                        }
                    }
                    echo '<li>DB tables: '.implode(', ', $tables).'</li>';
                }
            ?>
			<li>Users: <?php echo PerchDB::fetch()->get_value('SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'users'); ?></li>        
            <li>App runtimes: <pre><?php
                $file = PerchUtil::file_path(PERCH_PATH.'/config/apps.php');
                echo PerchUtil::html(file_get_contents($file));
            ?></pre></li>
            <?php
                $ScheduledTasks = new PerchScheduledTasks;
                $apps = $ScheduledTasks->get_scheduled();
                if (PerchUtil::count($apps)) {
                    foreach($apps as $appID=>$tasks) {
                        $task_list = array();
                        echo '<li>Scheduled tasks for '.$appID.': ';
                            foreach($tasks as $task) {
                                //print_r($task);
                                $task_list[] = $task['taskKey'].' ('.($task['frequency']/60).' mins)';
                            }
                            echo implode(', ', $task_list);
                        echo '</li>';
                    }
                    
                }
                
            
            ?>
            <?php
                echo '<li>Editor plug-ins: '.implode(', ', PerchUtil::get_dir_contents(PerchUtil::file_path(PERCH_PATH.'/addons/plugins/editors/', true))).'</li>';

            ?>
            <li>H1: <?php echo PerchUtil::html(md5($_SERVER['SERVER_NAME'])); ?></li>
            <li>L1: <?php echo PerchUtil::html(md5(PERCH_LICENSE_KEY)); ?></li>
            
            <?php
                $settings = $Settings->get_as_array();
                if (PerchUtil::count($settings)) {
                    foreach($settings as $key=>$val) {
                        echo '<li>'.PerchUtil::html($key.': '.$val).'</li>';
                    }
                }
            
            ?>
			<?php
                $constants = get_defined_constants(true);
                $ignore = array('PERCH_LICENSE_KEY', 'PERCH_DB_PASSWORD');
                if (PerchUtil::count($constants['user'])) {
                    foreach($constants['user'] as $key=>$val) {
                        if (!in_array($key, $ignore) && substr($key, 0, 5)=='PERCH') echo '<li>'.PerchUtil::html($key.': '.$val).'</li>';
                    }
                }
            ?>
		</ul>
		<h3><?php echo PerchLang::get('Hosting settings'); ?></h3>
		<ul>
            <li>PHP: <?php echo PerchUtil::html(phpversion()); ?></li>
            <li>Zend: <?php echo PerchUtil::html(zend_version()); ?></li>
            <li>OS: <?php echo PerchUtil::html(PHP_OS); ?></li>
            <li>SAPI: <?php echo PerchUtil::html(PHP_SAPI); ?></li>
            <li>Safe mode: <?php echo (ini_get('safe_mode') ? 'detected' : 'not detected'); ?></li>
            <li>MySQL client: <?php echo PerchUtil::html($DB->get_client_info()); ?></li>
            <li>MySQL server: <?php echo PerchUtil::html($DB->get_server_info()); ?></li>
            <li>Extensions: <?php echo PerchUtil::html(implode(', ', get_loaded_extensions())); ?></li>
            <li class="section">GD: <?php echo PerchUtil::html((extension_loaded('gd')? 'Yes' : 'No')); ?></li>
            <li>ImageMagick: <?php echo PerchUtil::html((extension_loaded('imagick')? 'Yes' : 'No')); ?></li>
            <li>PHP max upload size: <?php echo $max_upload; ?>M</li>
            <li>PHP max form post size: <?php echo $max_post; ?>M</li>
            <li>PHP memory limit: <?php echo $memory_limit; ?>M</li>
            <li>Total max uploadable file size: <?php echo $upload_mb; ?>M</li>
            <li>Resource folder writeable: <?php echo is_writable(PERCH_RESFILEPATH)?'Yes':'No'; ?></li>
            
            <li class="section">Session timeout: <?php echo ini_get('session.gc_maxlifetime')/60; ?> minutes</li>
            <li>Native JSON: <?php echo function_exists('json_encode')?'Yes':'No'; ?></li>
            <li>Filter functions: <?php echo function_exists('filter_var')?'Yes':'No (Required for form field type validation)'; ?></li>
            <li>Transliteration functions: <?php echo function_exists('transliterator_transliterate')?'Yes':'No'; ?></li>
            
            <?php
                $first = true;
                foreach($_SERVER as $key=>$val) {
                    if ($key && $val){
                        echo '<li'.($first?' class="section"':'').'>' . PerchUtil::html($key) . ': ' . PerchUtil::html($val).'</li>';
                            $first = false;
                    }
                    
                }
            ?>       
        </ul>
        
    </div>
    

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>