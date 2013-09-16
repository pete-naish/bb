<?php

class PerchContent_Item extends PerchBase
{
    protected $table  = 'content_items';
    protected $pk     = 'itemRowID';

    public function delete()
    {
    	$sql = 'DELETE FROM '.PERCH_DB_PREFIX.'content_index WHERE itemID='.$this->db->pdb($this->id());
    	$this->db->execute($sql);

    	parent::delete();
    }


	public function clear_resources()
	{
		$this->db->delete(PERCH_DB_PREFIX.'content_resources', 'itemRowID', $this->itemRowID());
	}

    public function log_resources($resourceIDs)
    {
    	if (PerchUtil::count($resourceIDs)) {
    		$sql = 'INSERT INTO '.PERCH_DB_PREFIX.'content_resources(`itemRowID`, `resourceID`) VALUES';
    		
    		$vals = array();

    		foreach($resourceIDs as $id) {
    			$vals[] = '('.(int)$this->itemRowID().','.(int)$id.')';
    		}

    		$sql .= implode(',', $vals);

    		$this->db->execute($sql);
    	}
    }

}

?>