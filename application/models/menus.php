<?php

/**
 * Model Class Menus  includes functions to manage the menus table and related menu_items table
 *  import from csv
 *  build html menu div
 *
 * @author mneucoll
 */
class Menus extends CI_model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }
    
    public function load_csv() {
        $sql = "TRUNCATE TABLE menus CASCADE";
        $query = $this->db->query($sql);
        $result = "menus table and menu_items table truncated<br />";

        $sql = "COPY menus FROM '".FCPATH."resources/csv/menus.csv' DELIMITER ',' CSV HEADER";
        $query = $this->db->query($sql);
        $result .= $this->db->affected_rows()." record(s) loaded into menus table from menus.csv<br />";

        $sql = "COPY menu_items FROM '".FCPATH."resources/csv/menu_items.csv' DELIMITER ',' CSV HEADER";
        $query = $this->db->query($sql);
        $result .= $this->db->affected_rows()." record(s) loaded into menu_items table from menu_items.csv <br />";

        $sql = "TRUNCATE TABLE menus_views";
        $query = $this->db->query($sql);
        $sql = "COPY menus_views FROM '".FCPATH."resources/csv/menus_views.csv' DELIMITER ',' CSV HEADER";
        $query = $this->db->query($sql);
        $result .= $this->db->affected_rows()." record(s) loaded into menus_views table from menus_views.csv <br />";
        //fred ($result,"result");
        return $result;  
    }
    
    public function build_menu($menu_id) {
        $sql = "SELECT css_id, css_class FROM menus WHERE id = $menu_id";
        $query = $this->db->query($sql);
        
        $menustr = "<div id = '".$query->row()->css_id."' class = '".$query->row()->css_class."'>\n";
        $menustr .= "<ul id = 'level1_menu_".$menu_id."'>\n ";
        
        $sql = "SELECT id, menu_id, menu_item_text, url, target, css_class 
                FROM menu_items 
                WHERE menu_id = $menu_id 
                    AND child_of IS NULL 
                ORDER BY sort ASC";
        $level1 = $this->db->query($sql);
        if ($level1->num_rows() > 0){
            foreach ($level1->result() as $level1_row){
                $menustr .= $this->build_menu_item($level1_row);
                
                $sql = "SELECT id, menu_id, menu_item_text, url, target, css_class 
                        FROM menu_items 
                        WHERE menu_id = $menu_id
                            AND child_of = ".$level1_row->id.
                        "ORDER BY sort ASC" ;
                $level2 = $this->db->query($sql);
                
                if ($level2->num_rows() > 0){
                    $menustr .= "<ul id = 'level2_menu_".$level1_row->id."'>\n ";
                    foreach ($level2->result() as $level2_row){
                        $menustr .= $this->build_menu_item($level2_row);
                        $sql = "SELECT id, menu_id, menu_item_text, url, target, css_class 
                                FROM menu_items 
                                WHERE menu_id = $menu_id
                                    AND child_of = ".$level2_row->id.
                                "ORDER BY sort ASC" ;
                        $level3 = $this->db->query($sql);
                        if ($level3->num_rows() > 0){
                            $menustr .= "<ul id = 'level3_menu_".$level2_row->id."'>\n ";
                            foreach ($level3->result() as $level3_row){
                                $menustr .= $this->build_menu_item($level3_row);            
                                $menustr.="</li>\n";
                            }
                            $menustr .= "</ul>\n";
                        }
                        $menustr.="</li>\n";
                    }
                    $menustr .= "</ul>";    
                }
                $menustr.="</li>\n";
            }           
            $menustr .= "</ul>\n";
        }
        $menustr .= "</div>\n";
        return $menustr;
    }

    function build_menu_item($row) {

    	if (empty($row->url)){ 
    		$url = "#";
    		}
    	elseif (substr($row->url,0,10) == "[base_url]"){
    		$url = base_url(substr($row->url,11));
    		}
		else {
			$url=$row->url;
			}
        $menu_item = "<li ";
	    $menu_item .= "class = ";
        if($row->menu_id!=100){
			$menu_item .= !empty($row->css_class) ? "{$row->css_class}" : "ui-state-disabled";
        } else {
			$menu_item .= !empty($row->css_class) ? "{$row->css_class}" : "disabled";
		}
		
        $menu_item .= ">";
        $menu_item .= "<a href='".$url."' ";
        $menu_item .= !empty($row->css_class)? "class = '".$row->css_class."' " : '';
        $menu_item .= !empty($row->target)? "target = '".$row->target."' " : '';
        $menu_item .= ">".$row->menu_item_text."</a>\n";
        return $menu_item;
    }
    
	function get_view_menu($view) {
		$sql="SELECT menu_id FROM menus_views WHERE view_name = '".$view."'";
		$query = $this->db->query($sql);
		$menu_id = $query->row()->menu_id;
		return !empty($menu_id)? $menu_id : 0;
	}

    
}

?>