<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 *   http://example.com/index.php/home
	 * - or -
	 *   http://example.com/index.php/home/index
	 * - or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	public $view_data;
	public $local_menu_id;

	public function __construct () {
		parent::__construct();
		$this->load->model('menus');
		$this->load->model('popups');
		$this->view_data['main_menu'] = $this->menus->build_menu(100);
	}

	public function index()
	{
		//$data['main_menu'] = $this->main_menu;
		$this->local_menu_id = 200;
		$this->view_data['local_menu_id'] = $this->local_menu_id;
		$this->view_data['local_menu'] = $this->menus->build_menu($this->local_menu_id);
		$this->load->view('v_home', $this->view_data);
	}

	public function view($view_name, $menu_id = null) {
		
		$this->local_menu_id = $this->menus->get_view_menu($view_name);
		
		$this->view_data['local_menu_id'] = $this->local_menu_id;
		$this->view_data['local_menu'] = $this->menus->build_menu($this->local_menu_id);
		
		//set the lat lng and zoom of the view
		$this->db->where('menu_id',$this->local_menu_id );
		$query=$this->db->get('menus_views');
		$menu_view =$query->row(); 
		$this->view_data['lat'] = $menu_view->view_center_lat;
		$this->view_data['lng'] = $menu_view->view_center_lng;
		$this->view_data['zoom'] = $menu_view->view_zoom;
		
/*
		$this->local_menu_id = $menu_id;
		$this->view_data['local_menu_id'] = (!empty($this->local_menu_id))? $this->local_menu_id : 0 ;
		if (!empty($this->local_menu_id)) {
			$this->view_data['local_menu'] = $this->menus->build_menu($this->local_menu_id);
		} else {
			$this->view_data['local_menu']=""; //initialize so unset variable does not create a problem in the view
		}
*/

		$query_popups = $this->popups->get_popups($view_name);
		$n=1;
		foreach ($query_popups->result() as $popup) {
			if($popup->iconurl !='none' && !empty ($popup->iconurl)) {
				$this->view_data["iconname"][$n]=$this->popups->build_icon($popup->icon_id);
			}
			$this->view_data["markername"][$n] = $this->popups->build_marker($popup->marker_id);
			$this->view_data["popupname"][$n] = $this->popups->build_popup($popup->popup_id);
			//$this->view_data["bindpopup"][$n] = $popup->markername.".bindPopup(".$popup->popupname.");";
			$this->view_data["bindpopup"][$n] = $this->popups->bind_popup($popup->popupname, $view_name);
			$n++;
		}
		$this->view_data['n']= $n;
		//$this->load->view($view_name, $this->view_data);
		$this->load->view('v_county', $this->view_data);

	}
	
	public function story($caller,$story) {

		$this->view_data['story_include'] = "stories/".$caller."/".$story.".php";
		$this->view_data['return'] =$caller;
		$this->load->view('v_story',$this->view_data);
		
	}
	public function tester() {
		//loads view mapTester for testing functions before implementing on standard maps
		$this->view_data['local_menu']=""; //initialize so unset variable does not create a problem in the view
		$this->load->view('mapTester', $this->view_data);
	
	}
}

/* Location: ./application/controllers/welcome.php */