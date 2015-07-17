<?php  
class ControllerMobileStoreHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->data['heading_title'] = $this->config->get('config_title');
		
		$this->set_category();
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/home.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/home.tpl';
		} else {
			$this->template = 'default/template/mobile_store/home.tpl';
		}
		
		$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/navi',
			'mobile_store/header'
		);

		$this->response->setOutput($this->render());
	}
	
	private function set_dm1() {
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
		$this->data['logo'] = $dir_img . 'logo.png';
		$this->data['dir_img'] = $dir_img;
	}
	
	private function set_category() {
		
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		
		$results = $this->model_catalog_category->getCategories(0);
		foreach ($results as $result) {
			$this->data['category'][$result['name']] = array(
				'name'  => $result['name'],
				'href'  => $this->url->link('mobile_store/category', 'fspath=' . $result['category_id']),
				'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'))
			);
		}
		
		/*
		 * config_home_actions json format: [{image: xxx, url: xxx}, {...}]
		 * */
		$actions = array();
		$home_actions = $this->config->get('config_home_actions');
		if ($home_actions != '') {
			$actions = json_decode($home_actions);
			if (count($actions > 0)) {
				foreach($actions as &$act) {
					$act->image = $this->model_tool_image->resize($act->image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
					$act->href =  $this->url->link($act->url);
				}
			}
		}
		$this->data['actions'] = $actions;
	}
}
?>