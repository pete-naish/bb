<?php

class PerchFactory
{
    
    protected $db;
    protected $cache = false;

    protected $default_sort_direction = 'ASC';
    
    
    function __construct() 
    {
        $this->db       = PerchDB::fetch();
        
        if (defined('PERCH_DB_PREFIX')) {
            $this->table    = PERCH_DB_PREFIX.$this->table;
        }
        
    }

    public function find($id)
    {
        $sql    = 'SELECT * 
                    FROM ' . $this->table . '
                    WHERE ' . $this->pk . '='. $this->db->pdb($id) .'
                    LIMIT 1';
                    
        $result = $this->db->get_row($sql);
        
        if (is_array($result)) {
            return new $this->singular_classname($result);
        }
        
        return false;
    }
    
    public function all($Paging=false)
    {
        if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
        }else{
            $sql = 'SELECT';
        }
        
        $sql .= ' * 
                FROM ' . $this->table;
                
        if (isset($this->default_sort_column)) {
            $sql .= ' ORDER BY ' . $this->default_sort_column . ' ASC';
        }
        
        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }
        
        $results = $this->db->get_rows($sql);
        
        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }
        
        return $this->return_instances($results);
    }
    
    /**
     * Get one item by the specified column. e.g. get_one_by('widgetID', 232) would select from this table where widgetID=232.
     *
     * @param string $col 
     * @param string $val 
     * @param string $order_by_col 
     * @return void
     * @author Drew McLellan
     */
    public function get_one_by($col, $val, $order_by_col=false)
    {
        $sql    = 'SELECT * FROM ' . $this->table . ' WHERE ' . $col . '='. $this->db->pdb($val) .' '.$this->standard_restrictions();
                    
        if ($order_by_col) $sql .= ' ORDER BY '.$order_by_col;
        
        $sql .= ' LIMIT 1';
                    
        $result = $this->db->get_row($sql);
        
        if (is_array($result)) {
            return new $this->singular_classname($result);
        }
        
        return false;
    }

    /**
     * Get a collection of items where the given column matches the given value. e.g. get_by('catID', 2) would get all rows with catID=2.
     * If $val is an array, does a SQL WHERE IN(array)
     *
     * @param string $col 
     * @param string $val 
     * @param string $order_by_col 
     * @return void
     * @author Drew McLellan
     */
    public function get_by($col, $val, $order_by_col=false, $Paging=false)
    {
        
    	if (is_object($Paging)) {
    		$select = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT ';
    	}else{
    		$select = 'SELECT ';
    	}
    	
    	if (is_array($val)) {
            $sql    = $select . ' * FROM ' . $this->table . ' WHERE ' . $col . ' IN ('. PerchUtil::implode_for_sql_in($val) .') '.$this->standard_restrictions();
        }else{
            $sql    = $select . ' * FROM ' . $this->table . ' WHERE ' . $col . '='. $this->db->pdb($val) .' '.$this->standard_restrictions();
        }
                
                    
        if ($order_by_col) {
            $sql .= ' ORDER BY '.$order_by_col;
        }else{
            if ($this->default_sort_column) $sql .= ' ORDER BY '.$this->default_sort_column.' '.$this->default_sort_direction;
        }
        
        if (is_object($Paging) && $Paging->enabled()){
        	$limit  = ' LIMIT ' . $Paging->lower_bound() . ', ' . $Paging->per_page();
        	$sql    .= $limit;
        }
                    
        $rows = $this->db->get_rows($sql);
        
        if (is_object($Paging) && $Paging->enabled()){
        	$sql	= "SELECT FOUND_ROWS() AS count";
        	$total	= $this->db->get_value($sql);
        	$Paging->set_total($total);
        }
        
        return $this->return_instances($rows);
    }    


    /**
     * Gets recent items, sorted by date, limited by an int or Paging class
     *
     * @param obj $Paging_or_limit Paging class or int for basic limit
     * @param bool $use_modified_date Use the modified date instead of created
     * @return array Array of singular objects
     * @author Drew McLellan
     */
    public function get_recent($Paging_or_limit=10, $use_modified_date=false)
    {
        if ($use_modified_date) {
            if ($this->modified_date_column) {
                $datecol = $this->modified_date_column;
            }else{
                $datecol = str_replace('ID', 'Modified', $this->pk);
            }
        }else{
            if ($this->created_date_column) {
                $datecol = $this->created_date_column;
            }else{
                $datecol = str_replace('ID', 'Created', $this->pk);
            }
        }
        
        $Paging = false;
        $limit  = false;
        
        if (is_object($Paging_or_limit)) {
            $Paging = $Paging_or_limit;
            $select = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT ';
        }else{
            $limit = (int) $Paging_or_limit;
            $select = 'SELECT ';
        }
        
        $sql = $select . ' *
                FROM ' . $this->table .'
                WHERE 1=1 '.$this->standard_restrictions() .' 
                ORDER BY '. $datecol .' DESC ';
        
        
        if (is_object($Paging) && $Paging->enabled()){
            $limit  = ' LIMIT ' . $Paging->lower_bound() . ', ' . $Paging->per_page();
            $sql    .= $limit;      
        }else{
            if ($limit!==false) {
                $sql .= ' LIMIT ' . $limit;
            }
        }
        
        
        $rows   = $this->db->get_rows($sql);
        
        if (is_object($Paging) && $Paging->enabled()){
            $sql    = "SELECT FOUND_ROWS() AS count";
            $total  = $this->db->get_value($sql);
            $Paging->set_total($total);
        }
        
        return $this->return_instances($rows);

    }
    
    
    public function create($data)
    {
        
        $newID  = $this->db->insert($this->table, $data);
        
        if ($newID) {
            $sql    = 'SELECT *
                        FROM ' . $this->table . ' 
                        WHERE ' .$this->pk . '='. $this->db->pdb($newID) .'
                        LIMIT 1';
            $result = $this->db->get_row($sql);
            
            if ($result) {
                return new $this->singular_classname($result);
            }
        }
    }
    
    protected function return_instances($rows)
    {
        if (is_array($rows) && PerchUtil::count($rows) > 0) {
            $out    = array();
            foreach($rows as $row) {
                $out[]  = new $this->singular_classname($row);
            }
            return $out;
        }
        
        return false;
    }
    
    protected function return_instance($row)
    {
        if (is_array($row) && PerchUtil::count($row) > 0) {
            return new $this->singular_classname($row);
        }
        
        return false;
    }
    
    protected function standard_restrictions()
    {
        return '';
    }
    
}

?>