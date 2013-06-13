<?php

    define('PERCH_LICENSE_KEY', 'P21207-VSM672-RTG761-UWT394-XHT024');

    define("PERCH_DB_USERNAME", 'bb_root');
    define("PERCH_DB_PASSWORD", 'aoeuhtns1');
    define("PERCH_DB_SERVER", "localhost");
    define("PERCH_DB_DATABASE", "bb_cms_new");
    define("PERCH_DB_PREFIX", "perch2_");
    
    define('PERCH_TZ', 'UTC');

    define('PERCH_EMAIL_FROM', 'pete.naish@gmail.com');
    define('PERCH_EMAIL_FROM_NAME', 'Pete Naish');

    define('PERCH_LOGINPATH', '/cms');
    define('PERCH_PATH', str_replace(DIRECTORY_SEPARATOR.'config', '', dirname(__FILE__)));
    define('PERCH_CORE', PERCH_PATH.DIRECTORY_SEPARATOR.'core');

    define('PERCH_RESFILEPATH', PERCH_PATH . DIRECTORY_SEPARATOR . 'resources');
    define('PERCH_RESPATH', PERCH_LOGINPATH . '/resources');
    
    define('PERCH_HTML5', true);
  
?>