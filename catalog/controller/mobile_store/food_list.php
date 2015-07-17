<?php   
class ControllerMobileStoreFoodList extends Controller {
	public function index() {
		
		$this->load->model('qingyou/food');
		
		if (($this->request->server['REQUEST_METHOD'] == 'GET') && isset($this->request->get['menuid'])) {
			$this->data['menu_food'] = $this->model_qingyou_food->getMenuFood($this->request->get['menuid']);
		}
		else {
			$this->data['menu_food'] = $this->model_qingyou_food->getFoodWithPrice(null);
		}
		
		$tfile = "food_list.tpl";
		if ($this->tpl != '')
			$tfile = $this->tpl;
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
	
		foreach($this->data['menu_food'] as &$food) {
			$food['image1'] = $dir_img.$food['image1'];
			$food['image2'] = $dir_img.$food['image2'];
			$food['image3'] = $dir_img.$food['image3'];
			$food['url'] = $this->url->link('mobile_store/food', 'id='.$food['id']);
			$food['price'] = $this->currency->format($food['price']);
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . "/template/mobile_store/$tfile")) {
			$this->template = $this->config->get('config_template') . "/template/mobile_store/$tfile";
		} else {
			$this->template = "default/template/mobile_store/$tfile";
		}
		
		$this->response->setOutput($this->render());
	}
	
	public function sxc() {
		$this->data['heading_title'] = '省心菜';
		$this->tpl = 'shengxc.tpl';
		$this->children = array(
			'mobile_store/header',
			'mobile_store/titlebar',
			'mobile_store/navi',
		);
		$this->index();
	}
}
?>