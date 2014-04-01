<?php


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);



require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');



class menu {

	/**
	 *	css and other attributes used for twitter bootstrap menu
	 */
	public $twbs_menu_attr = array(
		'navbar' => array(
						'ul' => 'class="nav navbar-nav"', 
						'li' => '',
						'a' => '',
						'separater' => 'class="divider"',
						'active' => 'class="active"',
						'disable' => '', 
						'li_sub' => 'class="dropdown"',
						'a_sub' => 'class="dropdown-toggle" data-toggle="dropdown"',
						'icon_sub' => '<span class="caret"></span>'
		),
		'dropdown' => array(
							'ul' => 'class="dropdown-menu"',
							'li' => '',
							'a' => '',
							'separater' => 'class="divider"',
							'active' => 'class="active"',
							'disable' => '',
							'li_sub' => '',
							'a_sub'	=> 'style="padding-left:10px"',
							'icon_sub' => ''							
						)
	);




	/**
	 * the css styles of the menu 
	 */
	private $menu_attr = array();

	/**
	 *	array of menu items
	 */
	private $menu_items = array(); 

	/**
	 *	the HTML code of the finished menu
	 */
	private $html_menu = ""; 
	

	/**
	 *	
	 */
	private $menu_type = "navbar";



	/**
	 * Navigation menu constructor. 
	 * @param $menu_items array of menu items used for constructing the menu
	 * @param $menu_type string "navbar" or "dropdown" menu
	 * @param $menu_attr array styles to be used for construcing menu
	 */
	public function __construct($menu_items, $menu_type="navbar", $menu_attr=array()){

		if (isset($menu_attr) && count($menu_attr) > 1) {
			$this->menu_attr = $menu_attr; 
		} else {
			$this->menu_attr = $this->twbs_menu_attr; 

		}


		if (isset($menu_items) && count($menu_items)>=1){
			$this->menu_items = $menu_items; 			
		} else {
			throw new Exception("Menu construction error: empty array, no menu items specified.");      	
    		exit;
		}

		$this->menu_type = $menu_type; 

		$this->html_menu = $this->construct_menu($menu_items, $menu_type);

	}




	public function construct_menu($menu_array, $type){

		global $Text; 
		global $firephp; 

		//$firephp->log($menu_array, "array");
		
		

		$tmp_html = '<ul '.$this->menu_attr[$type]['ul'].'>'; 

		//$firephp->log($tmp_html, "tmp_html");


		foreach ($menu_array as $nav_item) {
		
			//check access rights here for each item. 

			//construct URL
			$url = $nav_item['path'] . "?" . $nav_item['params'];

			//this item has submenu
			if (isset($nav_item['subnav']) && count($nav_item['subnav'])>1) {
				
				$tmp_html .= '<li '.$this->menu_attr[$type]['li_sub'].'>';
				$tmp_html .= '<a '.$this->menu_attr[$type]['a_sub'].' href="'.$url.'">';
				$tmp_html .= $Text[$nav_item['i18n']] .' '. $this->menu_attr[$type]['icon_sub'];
				$tmp_html .= $this->construct_menu($nav_item['subnav'], 'dropdown');
				$tmp_html .='</a></li>';

			//normal li item
			} else {

				$tmp_html .= '<li '.$this->menu_attr[$type]['li'].'>';
				$tmp_html .= '<a '.$this->menu_attr[$type]['a'].' href="'.$url.'">'.$Text[$nav_item['i18n']].'</a></li>';

			}


		}  

		$tmp_html .= '</ul>';

		return $tmp_html;

	}



	public function get_menu(){
		if ($this->html_menu == ""){
			throw new Exception("Menu is empty, nothing to return");
		} else {
			return $this->html_menu; 
		}
	}

}

?>