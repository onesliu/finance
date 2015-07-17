<?php   
class ControllerMobileStoreMenuGroup extends Controller {
	public function index() {
		
		$this->load->model('qingyou/menu_group');
		
		$this->data['menu_groups'] = $this->model_qingyou_menu_group->getGroups();
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
		
		foreach($this->data['menu_groups'] as &$group) {
			$group['image'] = $dir_img.$group['image'];
			$group['url'] = $this->url->link('mobile_store/menu', 'id='.$group['id']);
		}
		
		$this->data['menu_search_url'] = $this->url->link('mobile_store/menu_search', '');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/menu_group.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/menu_group.tpl';
		} else {
			$this->template = 'default/template/mobile_store/menu_group.tpl';
		}
		
		$this->children = array(
			'mobile_store/titlebar',
			'mobile_store/navi',
			'mobile_store/header'
		);
		
		$this->response->setOutput($this->render());
	} 	
}
?>