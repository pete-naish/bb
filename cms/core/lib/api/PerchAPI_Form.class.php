<?php

class PerchAPI_Form extends PerchForm
{
    public $app_id = false;
    public $version = 1.0;
    
    private $Lang = false;
    
    private $defaults = array();
    
    public $last = false;

    private $hint = false;
    
    function __construct($version=1.0, $app_id, $Lang)
    {
        $this->app_id = $app_id;
        $this->version = $version;
        $this->Lang = $Lang;
        
        // Include editor plugin
        $dir = PERCH_PATH.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'editors'.DIRECTORY_SEPARATOR.PERCH_APPS_EDITOR_PLUGIN;
        if (is_dir($dir) && is_file($dir.DIRECTORY_SEPARATOR.'_config.inc')) {
            $Perch = Perch::fetch();
            $Perch->add_head_content(str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents($dir.DIRECTORY_SEPARATOR.'_config.inc')));
        }
        
        parent::__construct($app_id);
    }
    
    public function form_start($id=false, $class='magnetic-save-bar')
    {
        $r = '<form method="post" action="'.$this->encode($this->action()).'" ' . $this->enctype();
        
        if ($id)    $r .= ' id="'.$this->encode($id).'"';
        if ($class) $r .= ' class="app '.$this->encode($class).'"';
                
        $r .= '>';
        
        return $r;
    }
    
    public function form_end()
    {
        return '</form>';
    }
    
    public function receive($postvars)
	{
	    $data = array();
	    foreach($postvars as $val){
	        if (isset($_POST[$val])) {
	            if (!is_array($_POST[$val])){
	                $data[$val]	= trim(stripslashes($_POST[$val]));
	            }else{
	                $data[$val]	= $_POST[$val];
	            }
	        }
	    } 
	    
	    return $data;
	}
    
    public function require_field($id, $message)
    {
        $this->required[$id] = $message;
    }
    
    public function submitted()
    {
        return $this->posted() && $this->validate();
    }
    
    public function text_field($id, $label, $value='', $class='', $limit=false, $attributes='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->text($id, $this->get_value($id, $value), $class, $limit, 'text', $attributes);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    public function textarea_field($id, $label, $value='', $class='', $use_editor_or_template_tag=true)
    {
        $data_atrs = array();
        
        if (is_object($use_editor_or_template_tag)) {
            $tag = $use_editor_or_template_tag;
            
            $class .= ' large ';
            if ($tag->editor()) $class .= $tag->editor();
            if ($tag->textile()) $class .= ' textile';
            if ($tag->markdown()) $class .= ' markdown';
            if ($tag->size()) $class .= ' '.$tag->size();
            if (!$tag->textile() && !$tag->markdown() && $tag->html()) $class .= ' html';
            
            if ($tag->imagewidth()) $data_atrs['width'] = $tag->imagewidth();
            if ($tag->imageheight()) $data_atrs['height'] = $tag->imageheight();
            if ($tag->imagecrop()) $data_atrs['crop'] = $tag->imagecrop();
            if ($tag->imageclasses()) $data_atrs['classes'] = $tag->imageclasses();
            if ($tag->bucket()) $data_atrs['bucket'] = $tag->bucket();
        }

        if ($use_editor_or_template_tag && !is_object($use_editor_or_template_tag)) {
            $class .= ' large '.PERCH_APPS_EDITOR_PLUGIN.' '.PERCH_APPS_EDITOR_MARKUP_LANGUAGE;
        }
        
        $out = $this->field_start($id); 
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->textarea($id, $this->get_value($id, $value), $class, $data_atrs);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    public function date_field($id, $label, $value='', $time=false)
    {    
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        if ($time) {
            $out .= $this->datetimepicker($id, $this->get_value($id, $value));
        }else{
            $out .= $this->datepicker($id, $this->get_value($id, $value));
        }
        
        $out .= $this->field_end($id);

        return $out;
    }
    
    public function image_field($id, $label, $value='', $basePath='', $class='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->image($id, $value, $basePath, $class);
        if ($value!='') {
            $out .= '<img class="preview" src="'.PerchUtil::html($value).'" alt="'.PerchLang::get('Preview').'" />';
            $out .= '<div class="remove">';
            $out .= $this->checkbox($id.'_remove', '1', 0).' '.$this->label($id.'_remove', PerchLang::get('Remove image'), 'inline');
            $out .= '</div>';
        }
		$out .= $this->field_end($id);

        return $out;
    }
    
    public function file_field($id, $label, $value='', $basePath='', $class='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->image($id, $value, $basePath, $class);
        if ($value!='') {
            $out .= '<div class="file icon">'.PerchUtil::html(str_replace(PERCH_RESPATH.'/', '', $value)).'</div>';
            $out .= '<div class="remove">';
            $out .= $this->checkbox($id.'_remove', '1', 0).' '.$this->label($id.'_remove', PerchLang::get('Remove file'), 'inline');
            $out .= '</div>';
        }
		$out .= $this->field_end($id);

        return $out;
    }
    
    public function select_field($id, $label, $options, $value='', $class='')
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->select($id, $options, $this->get_value($id, $value), $class);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    
    public function checkbox_field($id, $label, $checked_value='1', $value='', $class='', $limit=false)
    {
        $out = $this->field_start($id);
        $out .= $this->label($id, $this->Lang->get($label), '', $colon=false, $translate=false);
        $out .= $this->checkbox($id, $checked_value, $this->get_value($id, $value), $class, $limit);
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    
    public function checkbox_set($id, $label, $options, $values=false, $class='', $limit=false, $container_class='')
    {
        $out = $this->field_start($id);
        
        $out .= '<fieldset class="checkboxes '.$container_class.'"><strong>'.PerchUtil::html($this->Lang->get($label)).'</strong>';
        $i = 0;
        
        foreach($options as $option) {
            $boxid = $id.'_'.$i;
            $checked_value = false;
            if (in_array($option['value'], $values)){
                $checked_value = $option['value'];
            }
            if (PerchUtil::count($_POST)) {
                $checked_value = false;
                if (isset($_POST[$id]) && is_array($_POST[$id])) {
                    if (in_array($option['value'], $_POST[$id])) {
                        $checked_value = $option['value'];
                    }
                }
            }
            
            $out .= '<div class="checkbox">';
            $out .= $this->checkbox($boxid, $option['value'], $checked_value, $class, $id);
            $out .= $this->label($boxid, $option['label'], '', $colon=false, $translate=false);
            $out .= '</div>';
            $i++;
        }
        
        
        $out .= '</fieldset>';
        $out .= $this->field_end($id);
        
        return $out;
    }
    
    
    public function submit_field($id='btnSubmit', $value="Save", $cancel_url=false, $class='button')
    {
        $out = $this->submit_start();
				
		$out .= $this->submit($id, $this->Lang->get($value), $class, $translate=false);
		
		if ($cancel_url) {
		    $out .= ' ' . $this->Lang->get('or') . ' <a href="'.$this->encode($cancel_url).'">' . $this->Lang->get('Cancel'). '</a>'; 
		}		
		        
        $out .= $this->submit_end();
        
        return $out;
    }
        
    public function field_start($id)
    {
        $r = '<div class="field '. $this->error($id, false). ($this->last ? ' last' : '').'">';
        $this->last = false;
        return $r;
    }
    
    public function field_end($id)
    {
        $r = '';
        
        if ($this->hint) $r .= parent::hint($this->hint);
        
        $r .= '</div>';
        
        $this->hint = false;
        
        return $r;
    }

    public function hint($string, $class=false)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        $this->hint = $string;
    }
    
    public function field_help($string)
    {
        $args = func_get_args();
        array_shift($args);

        $string =  $this->Lang->get($string, $args);
        
        return parent::hint($string);
    }
    
    public function submit_start()
    {
        $s = '<p class="submit';
        if (defined('PERCH_NONSTICK_BUTTONS') && PERCH_NONSTICK_BUTTONS) {
            $s .= ' nonstick';
        }
        
        $s .= '">';
        return $s;
    }
    
    public function submit_end()
    {
        return '</p>';
    }
    
    public function encode($string)
    {
        return PerchUtil::html($string);
    }
    
    public function set_defaults($defaults)
    {
        $this->defaults = $defaults;
    }
    
    public function get_value($id, $value, $array=false)
    {
        if (!$array) $array = $this->defaults;
        
        return $this->get($array, $id, $value);
    }
    
    public function set_required_fields_from_template($Template, $seen_tags=array())
    {   
        $tags       = $Template->find_all_tags();
        
        if (is_array($tags)) {
            foreach($tags as $tag) {

                // initialising the field type here makes sure editor plugins are kicked of in the <head>
                $FieldType = PerchFieldTypes::get($tag->type(), $this, $tag, $tags, $this->app_id);

                $item_id = 'perch_'.$tag->id();
                if (!in_array($tag->id(), $seen_tags)) {
                    if (PerchUtil::bool_val($tag->required())) {
                        if ($tag->type() == 'date') {
                            if ($tag->time()) {
                                $this->require_field($item_id.'_minute', "Required");
                            }else{
                                $this->require_field($item_id.'_year', "Required");
                            }
                        }else{
                            $this->require_field($item_id, "Required");
                        }
                    }
                    $seen_tags[] = $tag->id();
                }
            }
        }
    }
    
    public function fields_from_template($Template, $details=array(), $seen_tags=array())
    {    
        $tags   = $Template->find_all_tags();
        
        $Form = $this;
        
        $out = '';        
        
        if (PerchUtil::count($tags)) {
            foreach($tags as $tag) {
            
                $item_id = 'perch_'.$tag->id();
                $raw_id = 'perch_'.$tag->id().'_raw';
                
                $tag->set('input_id', $item_id);
                $tag->set('post_prefix', 'perch_');
            
                if (!in_array($tag->id(), $seen_tags) && $tag->type()!='hidden' && $tag->type()!='slug') {

                    if ($tag->divider_before()) {
                       $out .= '<h2 class="divider">'.PerchUtil::html($tag->divider_before()).'</h2>';
                    }

                    $out .= '<div class="field '.$Form->error($item_id, false).'">';
                
                    $label_text  = PerchUtil::html($tag->label());
                    if ($tag->type() == 'textarea') {
                        if (PerchUtil::bool_val($tag->textile()) == true) {
                            $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/core/help/textile" class="assist">Textile</a></span>';
                        }
                        if (PerchUtil::bool_val($tag->markdown()) == true) {
                            $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/core/help/markdown" class="assist">Markdown</a></span>';
                        }
                    }
                    $Form->disable_html_encoding();
                    $out .= $Form->label($item_id, $label_text, '', false, false);
                    $Form->enable_html_encoding();
        
                        
                        $FieldType = PerchFieldTypes::get($tag->type(), $Form, $tag, $tags, $this->app_id);
                                                
                        $out.= $FieldType->render_inputs($details);
                            
                    if ($tag->help()) {
                        $out .= $Form->field_help($tag->help());
                    }
                
        
                    $out .= '</div>';

                    if ($tag->divider_after()) {
                       $out .= '<h2 class="divider">'.PerchUtil::html($tag->divider_after()).'</h2>';
                    }
        
                    $seen_tags[] = $tag->id();
                }
            }

        }
        
        return $out;
    }
    
    public function receive_from_template_fields($Template, $previous_values, $perch_only=true, $fixed_fields=false)
    {
        $tags   = $Template->find_all_tags();
        
        $Form = $this;
        
        $form_vars = array();
        
        $image_folder_writable = is_writable(PERCH_RESFILEPATH);
        
        if (is_array($tags)) {
                                        
            $seen_tags = array();  
            
            if ($perch_only) {
                $postitems = $Form->find_items('perch_');

                $seen_tags = array_keys($_POST);
                
                //if (!$postitems) $postitems = array();
                //$postitems = array_merge($_POST, $postitems);

                if (!$postitems) {
                    $postitems = $_POST;
                }

            }else{
                $postitems = $_POST;
            }
                        
            foreach($tags as $tag) {
                $item_id = 'perch_'.$tag->id();   
                
                $tag->set('input_id', $item_id);
                
                if (!in_array($tag->id(), $seen_tags)) {
                    $var = false;
                  
                    
                    $FieldType = PerchFieldTypes::get($tag->type(), $Form, $tag, $tags, $this->app_id);
                    
                    $var = $FieldType->get_raw($postitems, $previous_values);
            
                    if ($var) {
                       
                        $form_vars[$tag->id()] = $var;
                        
                        // title
                        if ($tag->title()) {
                            $title_var = $var;
                            
                            if (is_array($var) && isset($var['_title'])) {
                                $title_var = $var['_title'];
                            }
                            
                            if (isset($forms_vars[$i])) {
                                if (isset($form_vars[$i]['_title'])) {
                                    $form_vars[$i]['_title'] .= ' '.$title_var;
                                    $processed_vars[$i]['_title'] = ' '.$title_var;
                                }else{
                                    $form_vars[$i]['_title'] = $title_var;
                                    $processed_vars[$i]['_title'] = $title_var;
                                }
                            }
                            
                        }
                    }
                    $seen_tags[] = $tag->id();
                }
            }
            
                                            

        }
        
        return $form_vars;
    }
    
    public function post_process_field($tag, $value)
    {
        $out = array();

        $out[$tag->id()] = $value;
        
        return $out;
    }

}
?>
