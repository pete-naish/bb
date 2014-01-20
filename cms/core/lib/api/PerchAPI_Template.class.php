<?php

class PerchAPI_Template
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    private $Template = false;
    
    private $namespace = false;
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id  = $app_id;
        $this->version = $version;
        $this->Lang    = $Lang;
        
    }

    public function set($file, $namespace)
    {    
        $Perch = Perch::fetch(); // called to make sure constants are defined.

        $this->namespace = $namespace;
        
        $local_file = PerchUtil::file_path(PERCH_PATH.'/addons/apps/'.$this->app_id.'/templates/'.$file);
		$user_file = PerchUtil::file_path(PERCH_TEMPLATE_PATH.'/'.$file);

        if (file_exists($user_file)) {
            $template_file= $user_file;
        }else{
            $template_file = $local_file;
        }

        $this->Template = new PerchTemplate($template_file, $namespace, $relative_path=false);    
        $this->Template->enable_encoding();
        $this->Template->apply_post_processing = true;

        return $this->Template->status;
    }
    
    public function render($data)
    {
        return $this->Template->render($data);
    }

    public function render_group($data, $implode=true)
    {
        return $this->Template->render_group($data, $implode);
    }
    
    public function find_all_tags($namespace=false)
    {
        if ($namespace==false) {
            $namespace = $this->namespace;
        }
        
        return $this->Template->find_all_tags($namespace);
    }
    
    public function find_tag($tag)
	{
		return $this->Template->find_tag($tag);
	}
    
    public function find_help()
    {
        return $this->Template->find_help();
    }
    
    public function apply_runtime_post_processing($html, $vars=array())
    {
        if (!$this->Template) {
            $this->Template = new PerchTemplate(); 
        }
        
        return $this->Template->apply_runtime_post_processing($html, $vars);
    }

    public function use_noresults()
    {
        return $this->Template->use_noresults();
    }

}

?>