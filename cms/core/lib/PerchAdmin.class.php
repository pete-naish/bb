<?php

class PerchAdmin extends Perch
{
    private $apps         = array();
    private $settings     = array();
    
    private $javascript       = array();
    private $javascript_block = '';
    private $css              = array();
    private $head_content     = '';
    private $foot_content     = '';
    
    public $section           = '';
    public $admin             = true;


    public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}
	

    public function get_apps($for_nav=false)
    {
        if ($for_nav==false) return $this->apps;

        $out = array();
        foreach($this->apps as $app) {
            if ($app['hidden']==false) {
                $out[] = $app;
            }
        }
        return $out;
    }
    
    public function get_app($app_id)
    {
        if (PerchUtil::count($this->apps)) {
            foreach($this->apps as $app) {
                if ($app['id']==$app_id) {
                    return $app;
                }
            }
        }
        return false;
    }
    
    public function app_name($app_id)
    {
        $app = $this->get_app($app_id);
        if ($app) {
            return $app['label'];
        }
        
        return PerchLang::get('System');
    }
    
    public function find_installed_apps($CurrentUser)
    {
        $this->apps = array();
        
        $a = array();
        
        $a[] = array('filename'=>'content', 'path'=>PERCH_CORE.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR. 'content');
        
        
        if (is_dir(PerchUtil::file_path(PERCH_PATH.'/addons/apps'))) {
            if ($dh = opendir(PerchUtil::file_path(PERCH_PATH.'/addons/apps'))) {
                while (($file = readdir($dh)) !== false) {
                    if(substr($file, 0, 1) != '.' && !preg_match($this->ignore_pattern, $file)) {
                        if (is_dir(PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$file))) {
                            $a[] = array('filename'=>$file, 'path'=>PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$file));
                        }
                    }
                }
                closedir($dh);
            }
        }
        
        if (is_array($a)) {            
            foreach($a as &$app) {
                $file = PerchUtil::file_path($app['path'].'/admin.php');
                if (file_exists($file)) {
                    include($file);
                }
            }
        }
        
        $this->apps = PerchUtil::array_sort($this->apps, 'priority');
    }
    
    public function get_section()
    {
        $page = $this->get_page();        
        $page = trim(str_replace(PERCH_LOGINPATH.'/', '/', $page), '/');
        
        $parts  = explode('/', $page);
        
        if (is_array($parts)) {
        
            switch($parts[0]) {
                
                case 'addons':
                    return $parts[0].'/'.$parts[1].'/'.$parts[2];
                    break;
                    
                case 'core':
                    if ($parts[1]=='apps') {
                        return $parts[0].'/'.$parts[1].'/'.$parts[2];
                    }
                
                    return $parts[0].'/'.$parts[1];
                    break;
                    
                    
                default: 
                    return $parts[0];
                    break;
                
            }
            
        }
        
        return $page;
    }

	public function get_nav_page()
	{
		$page = $this->get_page();        
        $page = trim(str_replace(PERCH_LOGINPATH.'/', '/', $page), '/');
        
        $parts  = explode('/', $page);

		array_pop($parts);
		
		return implode('/', $parts);
	}
    
    public function add_javascript($path)
    {
        if (!in_array($path, $this->javascript)) {
            $this->javascript[] = $path;
        }
    }
    
    public function get_javascript()
    {
        return $this->javascript;
    }
    
    public function add_javascript_block($str)
    {
        $this->javascript_block .= $str.PHP_EOL;
    }
    
    public function get_javascript_blocks()
    {
        return $this->javascript_block;
    }
    
    public function add_css($path)
    {
        if (!in_array($path, $this->css)) {
            $this->css[] = $path;
        }
    }
    
    public function get_css()
    {
        return $this->css;
    }
    
    public function add_head_content($str)
    {
        $this->head_content .= $str;
    }
    
    public function get_head_content()
    {
        return $this->head_content;
    }
    
    
    public function add_foot_content($str)
    {
        $this->foot_content .= $str;
    }
    
    public function get_foot_content()
    {
        return $this->foot_content;
    }
    
    
    
    private function register_app($app_id, $label, $priority=10, $desc='', $version=false, $hidden=false)
    {
        if (strpos($app_id, '_')!==false) {
            $Lang = new PerchAPI_Lang(1, $app_id);
            $label = $Lang->get($label);
        }else{
            $label = PerchLang::get($label);
        }
        
        $app    = array();
        $app['id']      = $app_id;
        $app['version'] = $version;
        $app['label']   = $label;
        $app['path']    = PERCH_LOGINPATH . '/addons/apps/' . $app_id;
        $app['priority']= $priority;
        $app['desc']    = $desc;
        $app['active']  = true;
        $app['dashboard']  = false;
        $app['section'] = 'addons/apps/'.$app_id;
        $app['hidden']  = $hidden;
        
        $dash = PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$app_id.'/dashboard.php');
        if (file_exists($dash)) {
            $app['dashboard'] = $dash;
        }
        
        // Content app - special case
        if ($app_id=='content') {
            $app['path']    = PERCH_LOGINPATH . '/core/apps/' . $app_id;
            $app['section'] = 'core/apps/'.$app_id;
            
            $dash = PerchUtil::file_path(PERCH_CORE.'/apps/content/dashboard.php');
            if (file_exists($dash)) {
                $app['dashboard'] = $dash;
            }
        }
        
        $this->apps[]   = $app;
    }
    
    private function add_setting($settingID, $label, $type='text', $value=false, $opts=false, $hint=false)
    {
        $setting = array();
        $setting['type'] = $type;
        $setting['label'] = $label;
        $setting['default'] = $value;
        $setting['hint'] = false;
        $setting['app_id'] = $this->apps[count($this->apps)-1]['id'];
        
        if ($opts) $setting['opts'] = $opts;
        if ($hint) $setting['hint'] = $hint;
        
        $this->settings[$settingID] = $setting;
    }

    private function add_create_page($app_id, $path)
    {
        if (PerchUtil::count($this->apps)) {

            foreach($this->apps as &$app) {
                if ($app['id'] == $app_id) {
                    $app['create_page'] = $path;
                }
            }

        }
    }
    
    
    private function require_version($app_id, $version)
    {
        if (version_compare($this->version, $version, '<')) 
            die('App <em>'.$app_id.'</em> requires <strong>Perch '.$version.'</strong> to run. You have Perch '.$this->version.'.');
    }
    
    public function get_settings()
    {
        return $this->settings;
    }
    
}

?>