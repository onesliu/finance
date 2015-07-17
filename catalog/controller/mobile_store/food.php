<?php   
class ControllerMobileStoreFood extends Controller {
	public function index() {
		
		$this->load->model('qingyou/food');
		
		if (!isset($this->request->get['id'])) {
			$this->redirect($this->url->link('mobile_store/home'));
		}
		
		$food = $this->model_qingyou_food->getFood($this->request->get['id']);
		$food['attrs'] = $this->model_qingyou_food->getFoodAttrs($this->request->get['id']);
		$sources = $this->model_qingyou_food->getFoodSources($this->request->get['id']);
		$food['makestep'] = $this->model_qingyou_food->getMakeSteps($this->request->get['id']);
		
		$source_major = array();
		$source_minor = array();
		foreach($sources as &$p) {
			$p['href'] = $this->url->link('mobile_store/product', 'product_id=' . $p['product_id']);
			$p['final_show'] = $p['name'];
			if ((int)$p['weight'] > 0)
				$p['final_show'] .= ' '.((int)$p['weight']) . $p['weight_class'] .'/'. $p['upc'];
				
			if ($p['status'] > 0) {
				$p['price_show'] = $this->currency->format($p['price']);
			}
			else {
				$p['price_show'] = '已下架';
			}
			
			if ($p['source_type'] == 0) {
				$source_major[] = $p;
			}
			else {
				$source_minor[] = $p;
			}
		}
		
		$this->data['source_major'] = $source_major;
		$this->data['source_minor'] = $source_minor;
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
	
		if (isset($food['image1']) && $food['image1'] != '')
			$food['image1'] = $dir_img.$food['image1'];
		else
			$food['image1'] = null;
		if (isset($food['image2']) && $food['image2'] != '')
			$food['image2'] = $dir_img.$food['image2'];
		else
			$food['image2'] = null;
		if (isset($food['image3']) && $food['image3'] != '')
			$food['image3'] = $dir_img.$food['image3'];
		else
			$food['image3'] = null;
		
		$this->data['food'] = $food;
		
		$this->request->get['back'] = true;
		$this->data['logoicon'] = $dir_img.'logoicon.png';
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/food.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/food.tpl';
		} else {
			$this->template = 'default/template/mobile_store/food.tpl';
		}
		
		$this->children = array(
			'mobile_store/navi',
			'mobile_store/header',
			'mobile_store/titlebar'
		);
		
		$this->response->setOutput($this->render());
	}
	
}
?>