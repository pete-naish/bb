<?php
    define('PERCH_ERROR_MODE', 'SILENT');
	  include(dirname(__FILE__).'/../inc/pre_config.php');
    include(dirname(__FILE__).'/../../config/config.php');
    if (!defined('PERCH_PRODUCTION_MODE')) define('PERCH_PRODUCTION_MODE', PERCH_PRODUCTION);
    include(PERCH_CORE . '/runtime/loader.php');
    include(PERCH_PATH . '/config/apps.php');
    include(PERCH_PATH . '/core/inc/forms.php');
   	
   	if (file_exists(PERCH_PATH . '/config/feathers.php')){
   		include(PERCH_PATH . '/config/feathers.php');
   		include(PERCH_PATH . '/core/inc/feathers.php');
   	}

   		
?>