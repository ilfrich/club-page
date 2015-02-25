<?php
class Page extends DBObject {
        
    public $root = false;
    public $name = "";
    public $default = false;
    public $active = false;
    public $parentPageId = false;
    
    
    public $sections = array();
    public $subPages = array();
    
    
    
    function __construct($id) {
        $this->ID = $id;
        $query = "select name, rootpage, defaultpage, active, parentpageid from page where id = " . $id;

        if ($datasrc = mysql_query($query)) {
            while ($data = mysql_fetch_array($datasrc)) {
                $this->name = $data["name"];
                $this->default = ($data["defaultpage"] == 1) ? true : false;
                $this->root = ($data["rootpage"] == 1);
                $this->active = ($data["active"] == 1);
                $this->parentPageId = ($data["parentpageid"] == "") ? false : $data["parentpageid"];
                
            }
        }
        
        $this->subPages = $this->getSubPages();
        $this->sections = $this->getSections();
    }
    
    
    static function getDefaultPage() {
        $query = "select id from page where defaultpage = 1";
        if ($result = mysql_query($query)) {
            while ($data = mysql_fetch_array($result)) {
                return new Page($data["id"]);
            }
        }
        return false;
    }
    
    function getSections() {
        $list = array();
        
        $query = "select id from section where pageid = " . $this->ID . " order by position";
        if ($datasrc = mysql_query($query)) {
            while ($data = mysql_fetch_array($datasrc)) {
                array_push($list, new Section($data["id"]));
            }
        }
        
        return $list;
    }
    
    function getSubPages() {
    	$list = array();
    	$query = "select id from page where parentpageid = " . $this->ID;
    	if ($result = mysql_query($query)) {
    		while ($data = mysql_fetch_array($result)) {
    			array_push($list, new Page($data["id"]));
    		}
    	}
    	return $list;
    }
    
    function getParent() {
    	if ($this->parentPageId === false) {
    		return false;
    	}
    	else {
    		return new Page($this->parentPageId);
    	}
    }
}
?>