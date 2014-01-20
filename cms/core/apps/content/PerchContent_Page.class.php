<?php

class PerchContent_Page extends PerchBase
{
    protected $table  = 'pages';
    protected $pk     = 'pageID';


    /**
     * Calculate the depth of the current page
     *
     * @return void
     * @author Drew McLellan
     */
    public function find_depth()
    {
        $path = str_replace('/'.PERCH_DEFAULT_DOC, '', $this->pagePath());
        
        if ($path=='') return 1;
        
        return substr_count($path, '/');
    }
    
    /**
     * Update the page's position in the tree
     *
     * @param string $parentID 
     * @param string $order 
     * @return void
     * @author Drew McLellan
     */
    public function update_tree_position($parentID, $order=false, $cascade=false)
    {
        PerchUtil::debug('updating tree position');
        
        $Pages = new PerchContent_Pages;
        $ParentPage = $Pages->find($parentID);
        
        $data = array();
        $data['pageParentID'] = $parentID;
        
        if ($order===false) {
            if (is_object($ParentPage)) {
                $data['pageOrder'] = $ParentPage->find_next_child_order();
            }else{
                $data['pageOrder'] = $this->find_next_child_order(0);
            }
            
        }else{
            $data['pageOrder'] = $order;
        }
        
        
        
        if (is_object($ParentPage)) {
            $data['pageDepth'] = ($ParentPage->pageDepth()+1);
            $data['pageTreePosition'] = $ParentPage->pageTreePosition().'-'.str_pad($data['pageOrder'], 3, '0', STR_PAD_LEFT);
        }else{
            PerchUtil::debug('Could not find parent page');
            $data['pageDepth'] = 1;
            $data['pageTreePosition'] = '000-'.str_pad($data['pageOrder'], 3, '0', STR_PAD_LEFT);
            $data['pageParentID'] = 0;
        }
        
        
        $this->update($data);
        
        if ($cascade) {
            $child_pages = $Pages->get_by('pageParentID', $this->id());
            if (PerchUtil::count($child_pages)) {
                foreach($child_pages as $ChildPage) {
                    $ChildPage->update_tree_position($this->id());
                }
            }
        }
        

    }
    
    /**
     * Find the next pageOrder value for subpages of the current page.
     *
     * @return void
     * @author Drew McLellan
     */
    public function find_next_child_order($parentID=false)
    {
        if ($parentID===false) {
            $parentID = $this->id();
        }
        
        $sql = 'SELECT MAX(pageOrder) FROM '.$this->table.' WHERE pageParentID='.$this->db->pdb($parentID);
        $max = $this->db->get_count($sql);
        
        return $max+1;
    }
    
    
    /**
     * Does the given roleID have permission to create a subpage?
     *
     * @param string $roleID 
     * @return void
     * @author Drew McLellan
     */
    public function role_may_create_subpages($User)
    {
        if ($User->roleMasterAdmin()) return true;

        // top level?
        // if ((int)$this->pageParentID() == 0) {
        //     if (get_class($User)=='PerchAuthenticatedUser') {
        //         return $User->has_priv('content.pages.create.toplevel');
        //     }
        // }


        $roleID = $User->roleID();

        $str_roles = $this->pageSubpageRoles();
    
        if ($str_roles=='*') return true;
        
        $roles = explode(',', $str_roles);

        return in_array($roleID, $roles);
    }

    /**
     * Delete the page, along with its file
     * @return nothing
     */
    public function delete()
    {
        $Pages = new PerchContent_Pages;

        $site_path = $Pages->find_site_path();

        $file = PerchUtil::file_path($site_path.'/'.$this->pagePath());
        if (!$this->pageNavOnly() && file_exists($file)) {
            if (defined('PERCH_DONT_DELETE_FILES') && PERCH_DONT_DELETE_FILES==true) {
                // don't delete files!
            }else{
                unlink($file);   
            } 
        }
        return parent::delete();
    }

    /**
     * Get an array of groupIDs of the navgroups this page belongs to.
     * @return [type] [description]
     */
    public function get_navgroup_ids()
    {
        $sql = 'SELECT DISTINCT groupID FROM '.PERCH_DB_PREFIX.'navigation_pages
                WHERE pageID='.$this->db->pdb($this->id());
        return $this->db->get_rows_flat($sql);
    }

    /**
     * Update the page to be in the navgroups given.
     * @param  [type] $groupIDs [description]
     * @return [type]           [description]
     */
    public function update_navgroups($groupIDs)
    {
        if (PerchUtil::count($groupIDs)) {

            // remove any not in this set
            $sql = 'DELETE FROM '.PERCH_DB_PREFIX.'navigation_pages
                    WHERE pageID='.$this->db->pdb($this->id()).' AND groupID NOT IN ('.$this->db->implode_for_sql_in($groupIDs).')';
            $this->db->execute($sql);

            $existing = $this->get_navgroup_ids();
            if (!$existing) $existing = array();

            foreach($groupIDs as $groupID) {
                if (!in_array($groupID, $existing)) {
                    $data = array(
                        'pageID'=>$this->id(),
                        'groupID'=>(int)$groupID,
                        'pageTreePosition'=>'000-000',
                        'pageDepth'=>$this->pageDepth(),
                    );
                    $this->db->insert(PERCH_DB_PREFIX.'navigation_pages', $data);
                }

            }
        }
    }

    /**
     * Delete this page from all navgroups
     * @return [type] [description]
     */
    public function remove_from_navgroups()
    {
        $this->db->delete(PERCH_DB_PREFIX.'navigation_pages', 'pageID', $this->id());
    }


    /**
     * Get an array of the page's access tags
     * @return [type] [description]
     */
    public function access_tags()
    {
        if ($this->details['pageAccessTags']) {
            return explode(',', $this->details['pageAccessTags']);
        }else{
            return array();
        }
    }


    public function to_array($template_ids=false)
    {
        $out = parent::to_array();

        if ($out['pageAttributes'] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out['pageAttributes'], true);
            if (PerchUtil::count($dynamic_fields)) {
                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
            }
            $out = array_merge($dynamic_fields, $out);
        }

        return $out;
    }

    public function template_attributes($opts)
    {
        $Template = new PerchTemplate('pages/attributes/'.$opts['template'], 'pages');
        return $Template->render($this);
    }

    public function template_attribute($id, $opts)
    {

        if ($id=='pageTitle' || $id=='pageNavText') {
            return $this->details[$id]; 
        }

        $Template = new PerchTemplate('pages/attributes/'.$opts['template'], 'pages');
        $tag = $Template->find_tag($id, false, true);
        if ($tag) {
            $Template->load($tag);
            return $Template->render($this);
        }

        if (isset($this->details[$id])){
            return $this->details[$id]; 
        }
        
        return false;
    }

    public function move_file($new_location)
    {

        $new_location = PerchUtil::file_path($new_location);
        $new_location = str_replace(PERCH_LOGINPATH, '/', $new_location);
        $new_location = str_replace('..', '', $new_location);
        $new_location = str_replace('//', '/', $new_location);

        $old_path = PERCH_SITEPATH.$this->pagePath();
        $new_path = PerchUtil::file_path(PERCH_SITEPATH.'/'.ltrim($new_location, '/'));

        if ($old_path!=$new_path) {
            if (file_exists($old_path)) {
                if (!file_exists($new_path)) {
                    $new_dir = PerchUtil::strip_file_name($new_path);
                    if (!file_exists($new_dir)) {
                        mkdir($new_dir, 0755, true);
                    }
                    if (is_writable($new_dir)) {
                        if(rename($old_path, $new_path)) {
                            return array(true, false);
                        }else{
                            return array(false, 'The page could not be moved.');
                        } 
                    }else{
                        return array(false, 'The destination folder could not be written to, so the page cannot be moved.');
                    }
                }else{
                    return array(false, 'A page file already exists at the new location.');
                }
                
            }else{
                return array(false, 'No page file exists at that location to move.');
            }
        }else{
            // It's ok, as the file is already where they want it to be.
            return array(true, false);
        }
    }
}

?>