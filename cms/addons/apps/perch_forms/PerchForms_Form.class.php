<?php

class PerchForms_Form extends PerchAPI_Base
{
    protected $table  = 'forms';
    protected $pk     = 'formID';

    public function delete()
    {
        $this->db->delete(PERCH_DB_PREFIX.'forms_responses', 'formID', $this->details[$this->pk]);
        return parent::delete();
    }
    
    public function number_of_responses()
    {
        $sql = 'SELECT COUNT(*) AS qty
                FROM '.PERCH_DB_PREFIX.'forms_responses
                WHERE formID='.$this->db->pdb($this->id()).'
                    AND responseSpam=0';
        return $this->db->get_count($sql);
    }

    public function most_recent_response_date()
    {
        $sql = 'SELECT responseCreated
                FROM '.PERCH_DB_PREFIX.'forms_responses
                WHERE formID='.$this->db->pdb($this->id()).'
                ORDER BY responseCreated DESC
                LIMIT 1';
        return $this->db->get_value($sql);
    }

    public function get_settings()
    {
        return get_object_vars($this->_load_options());
    }

    public function process_response($SubmittedForm)
    {
        $opts = $this->_load_options();
        
        $data = array();
        $data['fields'] = array();
        $data['files'] = array();
        
        // Anti-spam
        $spam = false;
        $antispam = $SubmittedForm->get_antispam_values();
        $environment = $_SERVER;
        
        if (isset($opts->akismet) && $opts->akismet) {
            if (isset($opts->akismetAPIKey) && $opts->akismetAPIKey!='') {
                $spam = $this->_check_for_spam($antispam, $environment, $opts->akismetAPIKey);
            }
        }             
        
        // Files
        if (!$spam && PerchUtil::count($SubmittedForm->files)) {
            if (isset($opts->fileLocation) && $opts->fileLocation!='') {
                foreach($SubmittedForm->files as $key=>&$details) {
                    if ($details['error']=='0' && $details['size']>0) { // no error, upload worked              
                        $attrs = $SubmittedForm->get_template_attributes($key);
                        
                        if (is_uploaded_file($details['tmp_name'])) {
                            $filename = $details['name'];
                            $dest   = rtrim($opts->fileLocation, '\/').DIRECTORY_SEPARATOR;
                            
                            if (file_exists($dest.$filename)) $filename = time().$filename;
                            if (file_exists($dest.$filename)) $filename = time().mt_rand().$filename;
                            
                            if (PerchUtil::move_uploaded_file($details['tmp_name'], $dest.$filename)) {
                                $details['new_path'] = $dest.$filename;
                                $details['new_filename'] = $filename;
                                $file = new stdClass;
                                $file->name = $filename;
                                $file->path = $dest.$filename;
                                $file->size = $details['size'];
                                $file->mime = '';
                                if (isset($SubmittedForm->mimetypes[$key])) $file->mime = $SubmittedForm->mimetypes[$key];
                                $file->attributes = $attrs->get_attributes();
                                
                                $data['files'][$key] = $file;
                            }
                            
                        }            
                    }
                }
            }else{
                PerchUtil::debug('Form '.$SubmittedForm->id.': File save location not set, files discarded.', 'error');
            }
        }
        
        // Fields
        if (PerchUtil::count($SubmittedForm->data)) {
            foreach($SubmittedForm->data as $key=>$value) {
                $attrs = $SubmittedForm->get_template_attributes($key);
                if ($attrs) {
                    $field = new stdClass;
                    $field->attributes = $attrs->get_attributes();
                    $field->value = $value;
                    $data['fields'][$attrs->id()] = $field; 
                }
            }
        }
        
        if (!$spam && isset($opts->email) && $opts->email) {
            $this->_send_email($opts, $data);
        }
        
        if (isset($opts->store) && $opts->store) {
            $json = PerchUtil::json_safe_encode($data);

            $record = array();
            $record['responseJSON'] = $json;
            $record['formID'] = $this->id();
            $record['responseIP'] = $_SERVER['REMOTE_ADDR'];
            
            
            if ($spam) {
                $record['responseSpam'] = '1';
            }
            $spam_data = array();
            $spam_data['fields'] = $antispam;
            $spam_data['environment'] = $environment;
            $record['responseSpamData'] = PerchUtil::json_safe_encode($spam_data);
        

            $Responses = new PerchForms_Responses;
            $Response = $Responses->create($record);
        }
        
        if ($spam || !isset($opts->store) || !$opts->store) {
            // not storing, so drop files
            if (PerchUtil::count($data['files'])) {
                foreach($data['files'] as $file) {
                    if (file_exists($file->path)) {
                        @unlink($file->path);
                    }
                }
            }
        }
            
        
    }
    
    private function _send_email($opts, $data)
    {
        if ($opts->emailAddress) {
            
            $msg = '';
            if ($opts->adminEmailMessage) {
                $msg .= $this->_replace_vars($opts->adminEmailMessage, $data['fields'])."\n\n";
            }
            
            foreach($data['fields'] as $field) {
                
                if (isset($field->attributes['label'])) {
                    $msg .= str_pad($field->attributes['label'].': ', 30);
                }else{
                    $msg .= str_pad($field->attributes['id'].': ', 30);
                }
                
                if ($field->attributes['type']=='textarea') {
                    $msg .= "\n".$field->value."\n\n";
                }else{
                    $msg .= $field->value."\n";
                }
                
            }
            
            $API  = new PerchAPI(1.0, 'perch_forms');
            
            
            $Email = $API->get('Email');
            
            if (isset($opts->adminEmailSubject) && $opts->adminEmailSubject!='') {
                $Email->subject($this->_replace_vars($opts->adminEmailSubject, $data['fields']));
            }else{
                $Email->subject("Website form response");
            }
            
            if (isset($opts->adminEmailFromName) && $opts->adminEmailFromName!='') {
                $Email->senderName($this->_replace_vars($opts->adminEmailFromName, $data['fields']));
            }else{
                $Email->senderName(PERCH_EMAIL_FROM_NAME);
            }
            
            if (isset($opts->adminEmailFromAddress) && $opts->adminEmailFromAddress!='') {
                $Email->senderEmail($this->_replace_vars($opts->adminEmailFromAddress, $data['fields']));
            }else{
                $Email->senderEmail(PERCH_EMAIL_FROM);
            }
            
            $Email->recipientEmail(explode(',', $this->_replace_vars($opts->emailAddress, $data['fields'])));
            
            if (PerchUtil::count($data['files'])) {
                foreach($data['files'] as $File) {
                    $Email->attachFile($File->name, $File->path, $File->mime);
                    
                    if (isset($File->attributes['label'])) {
                        $msg .= str_pad($File->attributes['label'].': ', 30);
                    }else{
                        $msg .= str_pad($File->attributes['id'].': ', 30);
                    }
                        $msg .= $File->name."\n";
                }
            }
            
            $Email->body($msg);

            
            $Email->send();
            
        }
    }
    
    private function _replace_vars($str, $vars)
    {
        if (PerchUtil::count($vars)) {
            foreach($vars as $key=>$val) {
                $str = str_replace('{'.$key.'}', $val->value, $str);
            }
        }
        
        return $str;
    }
    
    
    private function _load_options()
    {
        return PerchUtil::json_safe_decode($this->details['formOptions']);
    }
    
    private function _check_for_spam($fields, $environment, $akismetAPIKey)
    {
        if (PerchForms_Akismet::check_message_is_spam($akismetAPIKey, $fields, $environment)) {
            PerchUtil::debug('Message is spam');
            return true;
        }else{
            PerchUtil::debug('Message is not spam');
        }
        return false;
    }

}

?>