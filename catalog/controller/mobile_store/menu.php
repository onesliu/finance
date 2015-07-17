<?php   
class ControllerMobileStoreMenu extends Controller {
	public function index() {

		$this->getMenu();
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/menu.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/menu.tpl';
		} else {
			$this->template = 'default/template/mobile_store/menu.tpl';
		}
		
		$this->request->get['back'] = true;
		
		$this->children = array(
			'mobile_store/header',
			'mobile_store/navi',
			'mobile_store/titlebar'
		);

		$this->response->setOutput($this->render());
	}
	
	public function mlist() {
		$this->getMenu();
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/menu_list.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/menu_list.tpl';
		} else {
			$this->template = 'default/template/mobile_store/menu_list.tpl';
		}
		
		$this->children = array(
			'mobile_store/header',
			'mobile_store/navi',
			'mobile_store/titlebar'
		);

		$this->response->setOutput($this->render());
	}
	
	private function getMenu() {
		$this->load->model('qingyou/menu');
		$this->load->model('qingyou/food');

		if (isset($this->request->get['id'])) {
			$groupid = $this->request->get['id'];
		}
		
		if (isset($this->request->get['menuid'])) {
			$menuid = $this->request->get['menuid'];
		}
		
		if (isset($groupid))
			$menus = $this->model_qingyou_menu->getMenus($groupid);
		else
			$menus = $this->model_qingyou_menu->getMenus();
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
				
		foreach($menus as &$menu) {
			if (isset($groupid))
				$menu['url'] = $this->url->link('mobile_store/menu', 'menuid='.$menu['id']."&id=".$groupid);
			else
				$menu['url'] = $this->url->link('mobile_store/menu', 'menuid='.$menu['id']);
			
			if (isset($menu['image1']) && $menu['image1'] != '')
				$menu['image1'] = $dir_img.$menu['image1'];
			else
				$menu['image1'] = null;
			if (isset($menu['image2']) && $menu['image2'] != '')
				$menu['image2'] = $dir_img.$menu['image2'];
			else
				$menu['image2'] = null;
			if (isset($menu['image3']) && $menu['image3'] != '')
				$menu['image3'] = $dir_img.$menu['image3'];
			else
				$menu['image3'] = null;

			if (isset($menuid) && ($menu['id'] == $menuid)) {
				$menu['menu_food'] = $this->model_qingyou_food->getMenuFood($menu['id']);
				foreach($menu['menu_food'] as &$food) {
					$food['image1'] = $dir_img.$food['image1'];
					$food['image2'] = $dir_img.$food['image2'];
					$food['image3'] = $dir_img.$food['image3'];
					$food['url'] = $this->url->link('mobile_store/food', 'id='.$food['id']);
				}
				
				$menu['sources'] = $this->model_qingyou_food->getFoodSourcesByMenu($menu['id']);
				foreach($menu['sources'] as &$p) {
					$est = '';
					if ($p['product_type'] == 1) $est = '约';
					$p['weight_show'] = $est. ((int)$p['weight']) . $p['weight_class'].'/'.$p['upc'];
					$p['price_show'] = $est. $this->currency->format($p['mpn']);
				}
				
				$this->data['menu'] = &$menu;
			}
		}
		
		$this->data['menus'] = $menus;
	}
}
?>