<?php
    if (version_compare(PHP_VERSION, '5.2.0', '<')) {
        die('Perch requires PHP 5.2. This server is running version ' . PHP_VERSION);
    }

    function perch_autoload($class_name) {
        if (strpos($class_name, 'PerchAPI')!==false) {
            $file = PERCH_CORE . '/lib/api/' . $class_name . '.class.php';
        }else{
            $file = PERCH_CORE . '/lib/' . $class_name . '.class.php';
        }   
        
        if (is_readable($file)) {
            require $file;
            return true;
        }
        return false;
    }
    
    spl_autoload_register('perch_autoload');
        
    if (get_magic_quotes_runtime()) set_magic_quotes_runtime(false);

    if (extension_loaded('mbstring')) mb_internal_encoding('UTF-8');

    if (defined('PERCH_TZ')) {
        date_default_timezone_set(PERCH_TZ);
    }else{
        date_default_timezone_set('UTC');
    }

    if (!defined('PERCH_ERROR_MODE')) define('PERCH_ERROR_MODE', 'DIE');
?>