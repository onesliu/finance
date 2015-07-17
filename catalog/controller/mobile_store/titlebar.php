<?php  
class ControllerMobileStoreTitlebar extends Controller {
	public function index() {
		
		$this->language->load('mobile_store/header');
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->data['heading_title'] = $this->config->get('config_title');
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_categories'] = $this->language->get('text_categories');
    	$this->data['text_order'] = $this->language->get('text_order');
    	$this->data['text_checkout'] = $this->language->get('text_checkout');
    	$this->data['text_menu'] = $this->language->get('text_menu');
    	$this->data['text_products'] = $this->language->get('text_products');
				
		$this->data['home'] = $this->url->link('mobile_store/home');
		$this->data['category_list'] = $this->url->link('mobile_store/category_list', '', 'SSL');
		$this->data['category'] = $this->url->link('mobile_store/category', '', 'SSL');
		$this->data['product'] = $this->url->link('mobile_store/allproduct', '', 'SSL');
		$this->data['cart'] = $this->url->link('mobile_store/cart', '', 'wxpay');
		$this->data['order'] = $this->url->link('mobile_store/order', '', 'SSL');
		$this->data['menu'] = $this->url->link('mobile_store/menu_group', '', 'SSL');
		
		if (isset($this->request->get['product_page']))
			$this->data['product_page'] = $this->request->get['product_page'];
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$dir_img = $this->config->get('config_ssl') . 'image/';
		} else {
			$dir_img = $this->config->get('config_url') . 'image/';
		}
		$this->data['logo'] = $dir_img . 'logo.png';
		$this->data['home_url'] = $this->url->link('mobile_store/home');
		if (isset($this->request->get['back']))
			$this->data['back'] = $this->request->get['back'];
			
		$options = array();
		$options[] = array(
				'name' => $this->language->get('text_checkout'),
				'url' => $this->url->link('mobile_store/cart', '', 'wxpay'),
			);
		$options[] = array(
				'name' => $this->language->get('text_order'),
				'url' => $this->url->link('mobile_store/order', '', 'SSL'),
			);
		$this->data['options'] = $options;
		
		$this->data['about'] = $this->url->link('mobile_store/about', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/titlebar.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/titlebar.tpl';
		} else {
			$this->template = 'default/template/mobile_store/titlebar.tpl';
		}
		
		$this->response->setOutput($this->render());
	}
}
?>