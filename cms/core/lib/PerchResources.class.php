<?php

class PerchResources extends PerchFactory
{
	protected $singular_classname = 'PerchResource';
    protected $table    = 'resources';
    protected $pk   = 'resourceID';

    private static $logged = array();
	
    public function log($app='content', $bucket='default', $file, $parentID=0, $key='')
    {
    	$data = array(
    		'resourceApp'=>$app,
    		'resourceBucket'=>$bucket,
    		'resourceFile'=>$file,
    		'resourceKey'=>$key,
    		'resourceParentID'=>$parentID,
    		'resourceType'=>$this->_get_type($file),
    		);

    	$newID = $this->db->insert($this->table, $data, true);

    	if ($newID=='0') {
    		$sql = 'SELECT resourceID FROM '.$this->table.' WHERE resourceBucket='.$this->db->pdb($bucket).' AND resourceFile='.$this->db->pdb($file).' LIMIT 1';
    		$newID = $this->db->get_value($sql);
    	}

    	PerchResources::$logged[] = $newID;

    	return $newID;
    }

    public function get_logged_ids()
    {
    	$ids = PerchResources::$logged;

    	PerchResources::$logged = array();

    	return $ids;
    }

    public function get_not_in_subquery($app='content', $subquery)
    {
    	$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'resources
                WHERE resourceApp='.$this->db->pdb($app).' AND resourceID NOT IN ('.$subquery.')';
        $rows = $this->db->get_rows($sql);

        return $this->return_instances($rows);
    }


    private function _get_type($file)
    {
    	return strtolower(substr(PerchUtil::file_extension($file), -4));
    }

}

?>