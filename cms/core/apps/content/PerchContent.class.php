<?php

class PerchContent extends PerchApp
{
    protected $table             = 'content_regions';
    protected $pk                = 'regionID';
    
    private $registered          = array();
    private $raw_content_cache   = array();
    private $custom_region_cache = array();
    
    private $preview             = false;
    private $tmp_url_vars        = false;
    
    private $api;
    
    private $key_requests        = array();
    private $keys_reordered      = array();
    private $new_keys_registered = false;
    
    private $pageID              = false;

    private $Page                = false;
    private $pages_cache         = array();
    
    public static function fetch()
    {       
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }
    
    public function set_preview($contentID, $rev=false)
    {
        $this->preview = true;
        $this->preview_contentID = $contentID;
        $this->preview_rev = $rev;
    }
    
    public function get($key=false)
    {
        if ($key === false) return ' ';
        
        if ($this->cache === false) {
            $this->_populate_cache_with_page_content();
        }
        
        if (!in_array($key, $this->key_requests)) $this->key_requests[] = $key;
        
        $r = '';
        
        if (isset($this->cache[$key])) {
            $r = $this->cache[$key];
        }else{
            $this->_register_new_key($key);
        }
        
        if ($this->new_keys_registered) {
            // re-order keys in light of the new key
            $this->_reorder_keys();
        }
        
        return $r;
    }
    
    public function get_custom($key=false, $opts=false) 
    {
        if ($key === false) return ' ';

        if ($opts===false) return $this->get($key);

        $db     = PerchDB::fetch();
        $Perch  = Perch::fetch();
        
        if (isset($opts['page'])) {
            $page = $opts['page'];
        }else{
            $page   = $Perch->get_page();
        }

        $where = $this->_get_page_finding_where($page);

        $region_key_for_cache = $key;
        if (is_array($key)) $region_key_for_cache = implode('-', $key);

        if (is_array($page)) {
            $cache_key = implode('|', $page).':'.$region_key_for_cache;
        }else{
            $cache_key = $page.':'.$region_key_for_cache;
        }       
                
        if (array_key_exists($cache_key, $this->custom_region_cache)) {
            $regions = $this->custom_region_cache[$cache_key];
        }else{
            $sql    = 'SELECT regionID, regionTemplate, regionPage';
        
            if ($this->preview){ 
                $sql .= ', regionLatestRev AS rev';
            }else{
                $sql .= ', regionRev AS rev';
            }

            $sql    .= ' FROM '.$this->table. ' WHERE ';
            
            if (is_array($key)) {
                $sql .= '(';
                    $key_items = array();
                    foreach($key as $k) {
                        $key_items[] = 'regionKey='.$db->pdb($k);
                    }
                    $sql .= implode(' OR ', $key_items);
                $sql .= ')';
            }else{
                $sql .= 'regionKey='.$db->pdb($key);
            }            
                        

            $sql    .= ' AND ('.implode(' OR ', $where).' OR regionPage='.$db->pdb('*') .')';
            $regions    = $db->get_rows($sql);

            $this->custom_region_cache[$cache_key] = $regions;
        }

        if (!PerchUtil::count($regions)) {
            PerchUtil::debug('No matching content regions found. Check region name ('.$key.') and page path options.', 'error');
        }

        $region_path_cache = array();
        if (PerchUtil::count($regions)) {
            foreach($regions as $region) {
                $region_path_cache[$region['regionID']] = $region['regionPage'];
            }
        }

        $filter_mode = false;

        $content = array();

        // find specific _id
        if (isset($opts['_id'])) {
            $item_id = (int)$opts['_id'];
            $Paging = false;

            $sql = 'SELECT  c.itemID, c.regionID, c.pageID, c.itemJSON FROM '.PERCH_DB_PREFIX.'content_items c WHERE c.itemID='.$this->db->pdb($item_id).' ';

            if (PerchUtil::count($regions)) {
                $where = array();
                foreach($regions as $region) {
                    $where[] = '(c.regionID='.$this->db->pdb($region['regionID']).' AND c.itemRev='.$this->db->pdb($region['rev']).')';
                }
                $sql .= ' AND ('.implode(' OR ', $where).')';
            }else{
                $sql .= ' AND c.regionID IS NULL ';
            }

            $sql .= ' LIMIT 1 ';

            $rows = $db->get_rows($sql);

        }else{
            $sortval = ' idx2.indexValue as sortval ';

            if (isset($opts['paginate'])) {
                if (isset($opts['pagination_var'])) {
                    $Paging = new PerchPaging($opts['pagination_var']);
                }else{
                    $Paging = new PerchPaging();
                }
                $sql = $Paging->select_sql();
            }else{
                $sql = 'SELECT';
            }

            $sql .= ' * FROM ( SELECT  idx.itemID, c.regionID, idx.pageID, c.itemJSON, '.$sortval.' FROM '.PERCH_DB_PREFIX.'content_index idx 
                            JOIN '.PERCH_DB_PREFIX.'content_items c ON idx.itemID=c.itemID AND idx.itemRev=c.itemRev AND idx.regionID=c.regionID
                            JOIN '.PERCH_DB_PREFIX.'content_index idx2 ON idx.itemID=idx2.itemID AND idx.itemRev=idx2.itemRev  ';

            if (isset($opts['sort'])) {
                $sql .= ' AND idx2.indexKey='.$db->pdb($opts['sort']).' ';
            }else{
                $sql .= ' AND idx2.indexKey='.$db->pdb('_order').' ';
            }

            if (PerchUtil::count($regions)) {
                $where = array();
                foreach($regions as $region) {
                    $where[] = '(idx.regionID='.$this->db->pdb($region['regionID']).' AND idx.itemRev='.$this->db->pdb($region['rev']).')';
                }
                $sql .= ' WHERE ('.implode(' OR ', $where).')';
            }else{
                $sql .= ' WHERE idx.regionID IS NULL ';
            }

            // if not picking an _id, check for a filter
            if (isset($opts['filter']) && (isset($opts['value']) || is_array($opts['filter']))) {
                $where = array();

                // if it's not a multi-filter, make it look like one to unify what we're working with
                if (!is_array($opts['filter']) && isset($opts['value'])) {
                    $filters = array(
                                    array(
                                        'filter'=>$opts['filter'],
                                        'value'=>$opts['value'],
                                        'match'=>(isset($opts['match']) ? $opts['match'] : 'eq'),
                                        'match-type'=>(isset($opts['match-type']) ? $opts['match-type'] : 'alpha')
                                    )
                                );
                    $filter_mode = 'AND';
                }else{
                    $filters = $opts['filter'];
                    $filter_mode = 'AND';

                    if (isset($opts['match']) && strtolower($opts['match'])=='or') {
                        $filter_mode = 'OR';
                    }
                }

                foreach($filters as $filter) {                       
                    $key = $filter['filter'];
                    $val = $filter['value'];
                    $match = isset($filter['match']) ? $filter['match'] : 'eq';

                    if (is_numeric($val)) $val = (float) $val;

                    switch ($match) {
                        case 'eq': 
                        case 'is': 
                        case 'exact': 
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue='.$db->pdb($val).')';
                            break;
                        case 'neq': 
                        case 'ne': 
                        case 'not': 
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue != '.$db->pdb($val).')';
                            break;
                        case 'gt':
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue > '.$db->pdb($val).')';
                            break;
                        case 'gte':
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue >= '.$db->pdb($val).')';
                            break;
                        case 'lt':
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue < '.$db->pdb($val).')';
                            break;
                        case 'lte':
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue <= '.$db->pdb($val).')';
                            break;
                        case 'contains':
                            $v = str_replace('/', '\/', $val);
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue REGEXP '.$db->pdb('[[:<:]]'.$v.'[[:>:]]').')';
                            break;
                        case 'regex':
                        case 'regexp':
                            $v = str_replace('/', '\/', $val);
                            $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue REGEXP '.$db->pdb($v).')';
                            break;
                        case 'between':
                        case 'betwixt':
                            $vals  = explode(',', $val);
                            if (PerchUtil::count($vals)==2) {

                                $vals[0] = trim($vals[0]);
                                $vals[1] = trim($vals[1]);

                                if (is_numeric($vals[0]) && is_numeric($vals[1])) {
                                    $vals[0] = (float)$vals[0];
                                    $vals[1] = (float)$vals[1];
                                }

                                $where[] = '(idx.indexKey='.$db->pdb($key).' AND (idx.indexValue > '.$db->pdb($vals[0]).' AND idx.indexValue < '.$db->pdb($vals[1]).'))';
                            }
                            break;
                        case 'eqbetween':
                        case 'eqbetwixt':
                            $vals  = explode(',', $val);
                            if (PerchUtil::count($vals)==2) {
                                $vals[0] = trim($vals[0]);
                                $vals[1] = trim($vals[1]);

                                if (is_numeric($vals[0]) && is_numeric($vals[1])) {
                                    $vals[0] = (float)$vals[0];
                                    $vals[1] = (float)$vals[1];
                                }

                                $where[] = '(idx.indexKey='.$db->pdb($key).' AND (idx.indexValue >= '.$db->pdb($vals[0]).' AND idx.indexValue <= '.$db->pdb($vals[1]).'))';
                                
                            }
                            break;
                        case 'in':
                        case 'within':
                            $vals  = explode(',', $val);
                            if (PerchUtil::count($vals)) {
                                $where[] = '(idx.indexKey='.$db->pdb($key).' AND idx.indexValue IN ('.$db->implode_for_sql_in($vals).'))';                          
                            }
                            break;
                    }
                }
                $sql .= ' AND ('.implode($where, ' OR ').') ';
            }

            $sql .= ' AND idx.itemID=idx2.itemID AND idx.itemRev=idx2.itemRev
                    ) as tbl GROUP BY itemID ';

            if ($filter_mode=='AND' && PerchUtil::count($filters)>1) {
                $sql .= ' HAVING count(*)='.PerchUtil::count($filters).' ';
            }

            // sort
            if (isset($opts['sort'])) {

                $direction = 'ASC';
                if (isset($opts['sort-order'])) {
                    switch($opts['sort-order']) {
                        case 'DESC':
                        case 'desc':
                            $direction = 'DESC';
                            break;

                        case 'RAND':
                        case 'rand':
                            $direction = 'RAND';
                            break;

                        default:
                            $direction = 'ASC';
                            break;
                    }
                }

                if ($direction=='RAND') {
                    $sql .= ' ORDER BY RAND()';
                }else{
                    if (isset($opts['sort-type']) && $opts['sort-type']=='numeric') {
                        $sql .= ' ORDER BY sortval * 1 '.$direction .' ';
                    }else{
                        $sql .= ' ORDER BY sortval '.$direction .' ';
                    }
                }
               
            }else{
                if (isset($opts['sort-type']) && $opts['sort-type']=='numeric') {
                    $sql .= ' ORDER BY sortval * 1 ASC ';
                }else{
                    $sql .= ' ORDER BY sortval ASC ';
                }
            }

            // Pagination
            if (isset($opts['paginate'])) {
                if (isset($opts['pagination_var'])) {
                    $Paging = new PerchPaging($opts['pagination_var']);
                }else{
                    $Paging = new PerchPaging();
                }
                
                $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);
                
                $opts['count'] = $Paging->per_page();
                $opts['start'] = $Paging->lower_bound()+1;
                
            }else{
                $Paging = false;
            }
                    
            // limit
            if (isset($opts['count']) || isset($opts['start'])) {

                // count
                if (isset($opts['count'])) {
                    $count = (int) $opts['count'];
                }else{
                    $count = false;
                }
                
                // start
                if (isset($opts['start'])) {
                    $start = ((int) $opts['start'])-1; 
                }else{
                    $start = 0;
                }

                if (is_object($Paging)) {
                    $sql .= $Paging->limit_sql();
                }else{
                    $sql .= ' LIMIT '.$start; 
                    if ($count) $sql .= ', '.$count;
                }
            }
   
            $rows = $db->get_rows($sql);

            if (is_object($Paging)) {
                $total_count = $this->db->get_value($Paging->total_count_sql());
                $Paging->set_total($total_count);
            }
        }

        // transform json
        if (PerchUtil::count($rows)) {
            $content = array();
            foreach($rows as $item) {
                if (trim($item['itemJSON'])!='') {
                    $tmp = PerchUtil::json_safe_decode($item['itemJSON'], true);
                    if (isset($region_path_cache[$item['regionID']])) {
                        $tmp['_page'] = $region_path_cache[$item['regionID']];
                        $tmp['_pageID'] = $item['pageID'];
                        //PerchUtil::debug($item);
                    }
                    if (isset($item['sortval'])) $tmp['_sortvalue'] = $item['sortval'];

                    // 'each' callback
                    if (isset($opts['each'])) {
                        if (is_callable($opts['each'])) {
                            $tmp = $opts['each']($tmp);
                        }
                    }
                    $content[] = $tmp;
                }
            }
        }

        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            if (isset($opts['raw']) && $opts['raw']==true) {
                if (PerchUtil::count($content)) {
                    foreach($content as &$item) {
                        if (PerchUtil::count($item)) {
                            foreach($item as &$field) {
                                if (is_array($field) && isset($field['raw'])) {
                                    $field = $field['raw'];
                                }
                            }
                        }
                    }
                }
                return $content; 
            }
        }
        
        // template
        if (isset($opts['template'])) {
            $template = $opts['template'];
        }else{
            $template = $regions[0]['regionTemplate'];
        }
        
        $Template = new PerchTemplate('content/'.$template, 'content');
        
        if (!$Template->file) {
            return 'The template <code>' . PerchUtil::html($template) . '</code> could not be found.';
        }
        
        // post process
        $tags   = $Template->find_all_tags('content');
        $processed_vars = array();
        $used_items = array();
        foreach($content as $item) {
            $tmp = $item;
            if (PerchUtil::count($tags)) {
                foreach($tags as $Tag) {
                    if (isset($item[$Tag->id])) {                         
                        $used_items[] = $item;
                    }
                }
            }
            if ($tmp) $processed_vars[] = $tmp;
        }
        
        // Paging to template
        if (is_object($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
            foreach($processed_vars as &$item) {
                foreach($paging_array as $key=>$val) {
                    $item[$key] = $val;
                }
            }
        }
        
        if (PerchUtil::count($processed_vars)) {
            $html = $Template->render_group($processed_vars, true);
        }else{
            $Template->use_noresults();
            $html = $Template->render(array());
        }
        
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $out = array();

            if (PerchUtil::count($processed_vars)) {
                foreach($processed_vars as &$item) {
                    if (PerchUtil::count($item)) {
                        foreach($item as &$field) {
                            if (is_array($field) && isset($field['processed'])) {
                                $field = $field['processed'];
                            }
                            if (is_array($field) && isset($field['_default'])) {
                                $field = $field['_default'];
                            }
                        }
                    }
                }
            }

            for($i=0; $i<PerchUtil::count($content); $i++) {
                $out[] = array_merge($content[$i], $processed_vars[$i]);
            }

            if (isset($opts['return-html'])&& $opts['return-html']==true) $out['html'] = $html;

            return $out;
        }
        
        return $html;
    }

    public function create_region($key=false, $opts=array())
    {
        if ($key === false) return false;
        
        if ($this->cache === false) {
            $this->_populate_cache_with_page_content();
        }
        
        if (!in_array($key, $this->key_requests)) $this->key_requests[] = $key;
            
        if (isset($this->cache[$key])) {
            return false;
        }else{
            $this->_register_new_key($key, $opts);
        }
        
        if ($this->new_keys_registered) {
            // re-order keys in light of the new key
            $this->_reorder_keys();

            // for shared regions, repopulate cache so that it gets picked up.
            if (isset($opts['shared']) && $opts['shared']==true) {
                $this->_populate_cache_with_page_content();
            }
            return true;
        }
        return false;
    }
    
    public function search_content($key, $opts)
    {
        PerchUtil::debug('Search term: '.$key);
        $this->mb_fallback();
        
        $search_handlers = PerchSystem::get_registered_search_handlers();

        $search_content = true;

        if (PerchUtil::count($opts['apps'])) {
            $search_content = in_array('PerchContent', $opts['apps']);
            
            if (PerchUtil::count($search_handlers)) {
                $new_handlers = array();
                foreach($search_handlers as $handler) {
                    $short = str_replace('_SearchHandler', '', $handler);
                    if (in_array($short, $opts['apps'])) {
                        $new_handlers[] = $handler;
                    }
                }
                $search_handlers = $new_handlers;
            }    
        }
        
        $out = array();

        if ($key!='') {
            if (!$this->api) {
                $this->api = new PerchAPI(1.0, 'content');
            }
            
            $encoded_key = str_replace('"', '', PerchUtil::json_safe_encode($key));
        
            $Paging = $this->api->get('Paging');
        
            if (isset($opts['count'])) {
                $Paging->set_per_page($opts['count']);
                if (isset($opts['start']) && $opts['start']!='') {
                    $Paging->set_start_position($opts['start']);
                }
            }else{
                $Paging->disable();
            }
        
            
                // Proper query using FULLTEXT
                $sql = $Paging->select_sql(); 
            
            if ($search_content) {            
                $sql .= '   \'content\' AS source, MATCH(ci.itemSearch) AGAINST('.$this->db->pdb($key).') AS score, 
                        r.regionPage AS col1, ci.itemSearch AS col2, ci.itemJSON AS col3, r.regionOptions AS col4, p.pageNavText AS col5, p.pageTitle AS col6, regionTemplate AS col7, r.regionKey AS col8
                        FROM '.$this->table.' r, '.PERCH_DB_PREFIX.'content_items ci, '.PERCH_DB_PREFIX.'pages p
                        WHERE r.regionID=ci.regionID AND r.regionRev=ci.itemRev AND r.pageID=p.pageID AND r.regionPage!=\'*\' AND r.regionSearchable=1 
                            AND (MATCH(ci.itemSearch) AGAINST('.$this->db->pdb($key).') OR MATCH(ci.itemSearch) AGAINST('.$this->db->pdb($encoded_key).') )
                            AND r.regionPage LIKE '.$this->db->pdb($opts['from-path'].'%').' ';
            }else{
                $sql .= ' \'content\' AS source, \'\' AS score, \'\' AS col1, \'\' AS col2, \'\' AS col3, \'\' AS col4, \'\' AS col5, \'\' AS col6, \'\' AS col7, \'\' AS col8 FROM '.$this->table.' WHERE 1=0 ';
            }

            if (PerchUtil::count($search_handlers)) {
                foreach($search_handlers as $handler) {    
        
                    $handler_sql = call_user_func(array($handler, 'get_search_sql'), $key);
                    if ($handler_sql) {
                        $sql .= ' 
                        UNION 
                        '.$handler_sql.' ';
                    }
                    $handler_sql = false;
                }
            }
                        
            $sql .= ' ORDER BY score DESC';
                
            if ($Paging->enabled()) {
                $sql .= ' '.$Paging->limit_sql();
            }        
                
            $rows = $this->db->get_rows($sql);
        
            if (PerchUtil::count($rows)==0) {
            
                if ($search_content) { 
                    // backup query using REGEXP
                    $sql = $Paging->select_sql() . ' \'content\' AS source, 0-(LENGTH(r.regionPage)-LENGTH(REPLACE(r.regionPage, \'/\', \'\'))) AS score, 
                            r.regionPage AS col1, ci.itemSearch AS col2, ci.itemJSON AS col3, r.regionOptions AS col4, p.pageNavText AS col5, p.pageTitle AS col6, regionTemplate AS col7, r.regionKey AS col8
                            FROM '.$this->table.' r, '.PERCH_DB_PREFIX.'content_items ci, '.PERCH_DB_PREFIX.'pages p
                            WHERE r.regionID=ci.regionID AND r.regionRev=ci.itemRev AND r.pageID=p.pageID AND r.regionPage!=\'*\' AND r.regionSearchable=1 
                                AND ci.itemSearch REGEXP '.$this->db->pdb('[[:<:]]'.$key.'[[:>:]]').' 
                                AND r.regionPage LIKE '.$this->db->pdb($opts['from-path'].'%').' ';
                }else{
                    $sql = $Paging->select_sql() . ' \'content\' AS source, \'\' AS score, \'\' AS col1, \'\' AS col2, \'\' AS col3, \'\' AS col4, \'\' AS col5, \'\' AS col6, \'\' AS col7, \'\' AS col8 FROM '.$this->table.' WHERE 1=0 ';
                }
                            
                if (PerchUtil::count($search_handlers)) {
                    foreach($search_handlers as $handler) {
                        $handler_sql = call_user_func(array($handler, 'get_backup_search_sql'), $key);
                        if ($handler_sql) {
                            $sql .= ' 
                            UNION 
                            '.$handler_sql.' ';
                        }
                        $handler_sql = false;
                    }
                }
                
                $sql .= ' ORDER BY score ASC ';

                if ($Paging->enabled()) {
                    $sql .= ' '.$Paging->limit_sql();
                }        

                $rows = $this->db->get_rows($sql);
            }
        
            if ($Paging->enabled()) {
                $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
            }
                
            if (PerchUtil::count($rows)) {
                foreach($rows as $row) {
                    switch($row['source']) {
                            case 'content':
                                $r = $this->format_search_result($key, $opts, $row);
                                if ($r) $out[] = $r;
                                break;
                            default:
                                $className = $row['source'];
                                $r = call_user_func(array($className, 'format_result'), $key, $opts, $row);
                                if ($r) {
                                    $r['source'] = str_replace('_SearchHandler', '', $row['source']);

                                    // duplicate vals
                                    foreach($r as $k=>$val) {
                                        $r['result_'.$k] = $val;
                                        if ($opts['no-conflict']) {
                                            unset($r[$k]);
                                        }
                                    }
                                      
                                    $r['search_key'] = $key;
                                    if (!$opts['no-conflict']) {
                                        $r['key'] = $key; 
                                    }
                                    $out[] = $r; 
                                } 
                                break;
                    }
                }




            }
        }
        
        if (isset($opts['skip-template']) && $opts['skip-template']) {
            return $out;
        }
        
        $Template = new PerchTemplate('search/'.$opts['template'], 'search');
        $Template->enable_encoding();
        
        if (PerchUtil::count($out)) {
            foreach($out as &$row) {

                // hide default doc
                if ($opts['hide-default-doc']) {
                    $row['url'] = preg_replace('/'.preg_quote(PERCH_DEFAULT_DOC).'$/', '', $row['url']);
                }

                if ($opts['hide-extensions'] && strpos($row['url'], '.')) {
                    $parts = explode('.', $row['url']);
                    $ext = array_pop($parts);
                    $query = '';
                    if (strpos($ext, '?')!==false) {
                        $qparts = explode('?', $ext);
                        array_shift($qparts);
                        if (PerchUtil::count($qparts)) {
                            $query = '?'.implode('?', $qparts);
                        }
                    }
                    $row['url'] = implode('.', $parts).$query;
                }

                // trailing slash
                if ($opts['add-trailing-slash']) {
                    $row['url'] = rtrim($row['url'], '/').'/';
                }
            }

            if (isset($Paging) && $Paging->enabled()) {
                $paging_array = $Paging->to_array($opts);
                // merge in paging vars
                foreach($out as &$item) {
                    foreach($paging_array as $key=>$val) {
                        $item[$key] = $val;
                    }
                }
            }
            return $Template->render_group($out, 1);
        }else{
            $Template->use_noresults();
            return $Template->render(array('search_key'=>$key, 'key'=>$key));
        }
    }
    
    public function get_page()
    {
        if ($this->Page) return $this->Page;

        $Pages = new PerchContent_Pages;
        $Perch = Perch::fetch();
        $Page = $Pages->find_by_path($Perch->get_page());

        if (is_object($Page)) $this->Page = $Page;

        return $this->Page;
    }

    public function get_page_by_id($id)
    {
        if (isset($this->pages_cache[$id])) return $this->pages_cache[$id];

        $Pages = new PerchContent_Pages;
        $Page = $Pages->find($id);

        if (is_object($Page)) $this->pages_cache[$id] = $Page;

        return $this->pages_cache[$id];
    }

    public function get_page_by_path($path)
    {
        if (isset($this->pages_cache[$path])) return $this->pages_cache[$path];

        $Pages = new PerchContent_Pages;
        $Page = $Pages->find_by_path($path);

        if (is_object($Page)) $this->pages_cache[$path] = $Page;

        return $this->pages_cache[$path];
    }


    /**
     * Load all content for page, and cache it.
     *
     * @return void
     * @author Drew McLellan
     */
    private function _populate_cache_with_page_content()
    {
        if ($this->preview) {
            if ($this->preview_contentID != 'all') {
                $this->cache = $this->get_content_latest_revision();
            }else{
                $this->cache = $this->get_content_latest_revision();
            }
        }else{
            $this->cache = $this->_get_content();
        }
    }
    
    /**
     * Get all content for the given page, or this page.
     *
     * @param string $page 
     * @return void
     * @author Drew McLellan
     */
    private function _get_content($page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }
        
        $db     = PerchDB::fetch();

        $sql    = 'SELECT regionKey, regionHTML FROM '.PERCH_DB_PREFIX.'content_regions
                    WHERE regionPage='.$db->pdb($page).' OR regionPage='.$db->pdb('*');
        $results    = $db->get_rows($sql);
        
        if (PerchUtil::count($results) > 0) {
            $out = array();
            foreach($results as $row) {
                if (!array_key_exists($row['regionKey'], $out)) {
                    $out[$row['regionKey']] = $row['regionHTML'];
                }
            }
            return $out;
        }else{
            return array();
        }
    }
    
    private function get_content_latest_revision($page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }        
        
        $Regions = new PerchContent_Regions;
        $regions = $Regions->get_for_page_path($page);
        
        if (PerchUtil::count($regions)) {
            $out  = array();
            foreach($regions as $Region) {
                $out[$Region->regionKey()] = $Region->render();
            }
            return $out;
        }else{
            return array();
        }
    }
    
    private function get_content_raw($key, $page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }

        if (is_array($page)) {
            $cache_key = implode('|', $page).':'.$key;
        }else{
            $cache_key = $page.':'.$key;
        }       
        
        if (array_key_exists($cache_key, $this->raw_content_cache)) {
            return $this->raw_content_cache[$cache_key];
        }else{
            $db     = PerchDB::fetch();
            $sql    = 'SELECT regionID, regionTemplate';
            
            if ($this->preview){ 
                $sql .= ', regionLatestRev AS rev';
            }else{
                $sql .= ', regionRev AS rev';
            }

            $where = $this->_get_page_finding_where($page);
            
            $sql    .= ' FROM '.$this->table. '
                        WHERE regionKey='.$db->pdb($key).' AND ('.implode(' OR ', $where).' OR regionPage='.$db->pdb('*') .')';
            $regions    = $db->get_rows($sql);
            
            if (PerchUtil::count($regions)) {

                $where = array();
                foreach($regions as $region) {
                    $where[] = '(regionID='.$this->db->pdb($region['regionID']).' AND itemRev='.$this->db->pdb($region['rev']).')';
                }
                
                $sql = 'SELECT itemJSON FROM '.PERCH_DB_PREFIX.'content_items WHERE '.implode(' OR ', $where).' ORDER BY itemOrder ASC';
                $result = $this->db->get_rows($sql);
                
                if (PerchUtil::count($result)) {
                    
                    $content = array();
                    foreach($result as $item) {
                        if (trim($item['itemJSON'])!='') $content[] = $item['itemJSON'];
                    }
                    $region['content'] = '['.implode(',', $content).']';
                    
                    $this->raw_content_cache[$cache_key] = $region;
                    return $region;
                }
            }
        }
        return false;
    }

    private function _get_page_finding_where($page=false)
    {
        $Perch  = Perch::fetch();
        $db     = PerchDB::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }

        $where = array();

        if (PerchUtil::count($page)) {
            foreach($page as $p) {
                if (strpos($p, '*')!==false) {
                    $where[] = 'regionPage LIKE '.$db->pdb(str_replace('*', '%', $p));
                }else{
                    $where[] = 'regionPage='.$db->pdb($p);
                }
            }
        }else{
            if (strpos($page, '*')!==false) {
                $where[] = 'regionPage LIKE '.$db->pdb(str_replace('*', '%', $page));
            }else{
                $where[] = 'regionPage='.$db->pdb($page);    
            }
        }

        return $where;
    }
    
    /**
     * Add a new key to the regions table
     *
     * @param string $key 
     * @return void
     * @author Drew McLellan
     */
    private function _register_new_key($key, $opts=false)
    {
        if (!isset($this->registered[$key])) {      
            
            $Perch  = Perch::fetch();
            $page   = $Perch->get_page();

            $data   = array();
            $data['regionKey']     = $key;
            $data['regionPage']    = $page;
            $data['regionHTML']    = '<!-- Undefined content: '.PerchUtil::html($key).' -->';
            $data['regionOptions'] = '';
            
            if (is_array($opts)) {

                if ($opts['page'])   $data['regionPage'] = $opts['page'];
                if ($opts['shared']) $data['regionPage'] = '*';
                
                if ($opts['template']) {
                    $data['regionTemplate'] = $opts['template']; 
                    $data['regionNew'] = 0; 
                } 
                
                if ($opts['multiple']) {
                    $data['regionMultiple'] = 1;  
                }else{
                    $data['regionMultiple'] = 0;
                }

                if ($opts['searchable']) {
                    $data['regionSearchable'] = 1;  
                }else{
                    $data['regionSearchable'] = 0;
                }

                if ($opts['roles']) $data['regionEditRoles'] = $opts['roles'];

                $regionOptions = array();

                if ($opts['sort'])              $regionOptions['sortField']     = $opts['sort'];
                if ($opts['sort-order'])        $regionOptions['sortOrder']     = $opts['sort-order'];
                if ($opts['edit-mode'])         $regionOptions['edit_mode']     = $opts['edit-mode'];
                if ($opts['search-url'])        $regionOptions['searchURL']     = $opts['search-url'];
                if ($opts['add-to-top'])        $regionOptions['addToTop']      = $opts['add-to-top'];
                if ($opts['limit'])             $regionOptions['limit']         = $opts['limit'];
                if ($opts['title-delimiter'])   $regionOptions['title_delimit'] = $opts['title-delimiter'];
                if ($opts['columns'])           $regionOptions['column_ids']    = $opts['columns'];

                $data['regionOptions'] = PerchUtil::json_safe_encode($regionOptions);
            }

            $data['pageID'] = $this->_find_or_create_page($data['regionPage']);
        
            $db = PerchDB::fetch();
            
            $cols   = array();
            $vals   = array();

            foreach($data as $key => $value) {
                $cols[] = $key;
                $vals[] = $db->pdb($value).' AS '.$key;
            }

            $sql = 'INSERT INTO ' . $this->table . '(' . implode(',', $cols) . ') 
                    SELECT '.implode(',', $vals).' 
                    FROM (SELECT 1) AS dtable
                    WHERE (
                            SELECT COUNT(*) 
                            FROM '.$this->table.' 
                            WHERE regionKey='.$db->pdb($data['regionKey']).' 
                                AND regionPage='.$db->pdb($data['regionPage']).'
                            )=0
                    LIMIT 1';
                            
            $db->execute($sql);
            
            $this->registered[$key] = true;
            $this->new_keys_registered = true;
        }
    }
    
    
    /**
     * Find the page by its path, or create it if it's new.
     *
     * @param string $path 
     * @return void
     * @author Drew McLellan
     */
    private function _find_or_create_page($path)
    {
        if ($path=='*') return 1;

        if ($this->pageID) return $this->pageID;
        
        $db = PerchDB::fetch();
        $table = PERCH_DB_PREFIX.'pages';
        $sql = 'SELECT pageID FROM '.$table.' WHERE pagePath='.$db->pdb($path).' LIMIT 1';
        $pageID = $db->get_value($sql);
        
        if ($pageID) {
            $this->pageID = $pageID;
            return $pageID;
        }
        
        $data = array();
        $data['pagePath']       = $path;
        $data['pageTitle']      = PerchUtil::filename($path, false, false);
        $data['pageNavText']    = $data['pageTitle'];
        $data['pageNew']        = 1;
        $data['pageDepth']      = 0;
        $data['pageModified']   = date('Y-m-d H:i:s');
        $data['pageAttributes'] = '';
        
        return $db->insert($table, $data);
    }
    
    /**
     * Reorder keys into source order
     *
     * @return void
     * @author Drew McLellan
     */
    private function _reorder_keys()
    {
        if (PerchUtil::count($this->key_requests)) {
            $Perch  = Perch::fetch();
            $page   = $Perch->get_page();
            $db = PerchDB::fetch();
            $i = 0;
            foreach($this->key_requests as $key) {
                if (!in_array($key, $this->keys_reordered)) {
                    $sql = 'UPDATE '.$this->table.' SET regionOrder='.$i.' WHERE regionPage='.$db->pdb($page).' AND regionKey='.$db->pdb($key).' LIMIT 1';
                    $db->execute($sql);
                    $this->keys_reordered[] = $key;
                }
                $i++;
            }
        }
    }
    
    // Used for custom searchURLs e.g. /example.php?id={_id}
    private function substitute_url_vars($matches)
    {
        $url_vars = $this->tmp_url_vars;
        if (isset($url_vars[$matches[1]])){
            return $url_vars[$matches[1]];
        }
    }

    private function format_search_result($key, $opts, $row)
    {
        $_contentPage    = 'col1';
        $_contentSearch  = 'col2';
        $_contentJSON    = 'col3';
        $_contentOptions = 'col4';
        $_pageNavText    = 'col5';
        $_pageTitle      = 'col6';
        $_regionTemplate = 'col7';
        $_regionKey      = 'col8';
                
        $lowerkey = strtolower($key);
        $item = PerchUtil::json_safe_decode($row[$_contentJSON], 1);

        if (PerchUtil::count($item)) {
    
            $loweritem     = strtolower($row[$_contentSearch]);
            $excerpt_chars = (int) $opts['excerpt-chars'];
            $first_portion = floor(($excerpt_chars/4));
            
            $out = array();
            $out['url'] = $row[$_contentPage];
        
            $regionOptions = PerchUtil::json_safe_decode($row[$_contentOptions]);
            if ($regionOptions) {
                if (isset($regionOptions->searchURL) && $regionOptions->searchURL!='') {
                    $out['url'] = $regionOptions->searchURL;
                    $this->tmp_url_vars = $item;
                    $out['url'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $out['url']);
                    $this->tmp_url_vars = false;
                }
            }
        
            if (isset($item['_title'])) {
                $out['title'] = $item['_title'];
            }else{
                $out['title'] = $row[$_pageNavText];
            }

            $html = strip_tags(html_entity_decode($row[$_contentSearch]));

            $html = preg_replace('/\s{2,}/', ' ', $html);
            $pos = mb_stripos($html, $key);
            if ($pos<$first_portion){
                $lower_bound = 0;
            }else{
                $lower_bound = $pos-$first_portion;
            }
        
            $html = mb_substr($html, $lower_bound, $excerpt_chars);
        
            // trim broken works
            $parts = explode(' ', $html);
            array_pop($parts);
            array_shift($parts);
            $html = implode(' ', $parts);
        
            // keyword highlight
            $html = preg_replace('/('.preg_quote($key, '/').')/i', '<em class="keyword">$1</em>', $html);
            
            $out['excerpt']     = $html;
                   
            $out['pageTitle']   = $row[$_pageTitle];
            $out['pageNavText'] = $row[$_pageNavText];
            $out['source']      = $row['source'];
            $out['region_key']  = $row[$_regionKey];

            // duplicate vals
            foreach($out as $k=>$val) {
                $out['result_'.$k] = $val;
                if ($opts['no-conflict']) {
                    unset($out[$k]);
                }
            }
              
            $out['search_key'] = $key;

            if (!$opts['no-conflict']) {
                $out['key'] = $key; 
            }

            

            $out = array_merge($out, $item);
            
            return $out;         
        }
        return false;
    }
 
    private function mb_fallback()
    {
        if (!function_exists('mb_stripos')) {
            function mb_stripos($a, $b) {
                return stripos($a, $b);
            }
        }
        
        if (!function_exists('mb_substr')) {
            function mb_substr($a, $b, $c) {
                return substr($a, $b, $c);
            }
        }
    }

    private function _resolve_to_value($val)
    {
        if (!is_array($val)) {
            return trim($val);
        }
        if (is_array($val)) {
            if (isset($val['_default'])) {
                return trim($val['_default']);
            }
            if (isset($val['processed'])) {
                return trim($val['processed']);
            }
        }

        return $val;
    }
}
?>