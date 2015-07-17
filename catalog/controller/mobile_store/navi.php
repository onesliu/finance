<?php   
class ControllerMobileStoreNavi extends Controller {
	protected function index() {
		
		$this->language->load('mobile_store/header');
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_categories'] = $this->language->get('text_categories');
    	$this->data['text_order'] = $this->language->get('text_order');
    	$this->data['text_checkout'] = $this->language->get('text_checkout');
    	$this->data['text_menu'] = $this->language->get('text_menu');
    	$this->data['text_clean'] = "省心菜";
    	$this->data['text_products'] = $this->language->get('text_products');
				
		$this->data['home'] = $this->url->link('mobile_store/home');
		$this->data['category_list'] = $this->url->link('mobile_store/category_list', '', 'SSL');
		$this->data['category'] = $this->url->link('mobile_store/category', '', 'SSL');
		$this->data['product'] = $this->url->link('mobile_store/allproduct', '', 'SSL');
		$this->data['cart'] = $this->url->link('mobile_store/cart', '', 'wxpay');
		$this->data['order'] = $this->url->link('mobile_store/order', '', 'SSL');
		$this->data['menu'] = $this->url->link('mobile_store/food_list/sxc', '', 'SSL');
		$this->data['clean'] = $this->url->link('mobile_store/allproduct', 'category_name=省心菜', 'SSL');
		
		$this->session->data['error_msg'] = '商城建设中，敬请期待...';
		$this->session->data['url_continue'] = $this->url->link('mobile_store/allproduct', '', 'SSL');
		$this->session->data['text_continue'] = '转到买菜页';
				
		if (isset($this->request->get['product_page']))
			$this->data['product_page'] = $this->request->get['product_page'];
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/navi.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/navi.tpl';
		} else {
			$this->template = 'default/template/mobile_store/navi.tpl';
		}
		
		$this->children = array(
			'mobile_store/category_list',
		);
		
    	$this->render();
	} 	
}
?>