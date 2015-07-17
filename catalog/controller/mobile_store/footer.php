<?php
class ControllerMobileStoreFooter extends Controller {
	protected function index() {
		
		$this->language->load('mobile_store/footer');
		
		$this->data['product_general_href']  = $this->url->link('mobile_store/product', 'product_id=');
		
		$this->data['text_information'] = $this->language->get('text_information');
		$this->data['text_service'] = $this->language->get('text_service');
		$this->data['text_extra'] = $this->language->get('text_extra');
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_contact'] = $this->language->get('text_contact');
		$this->data['text_return'] = $this->language->get('text_return');
    	$this->data['text_sitemap'] = $this->language->get('text_sitemap');
		$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['text_voucher'] = $this->language->get('text_voucher');
		$this->data['text_affiliate'] = $this->language->get('text_affiliate');
		$this->data['text_special'] = $this->language->get('text_special');
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_order'] = $this->language->get('text_order');
		$this->data['text_wishlist'] = $this->language->get('text_wishlist');
		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_livestream'] = $this->language->get('text_livestream');
		$this->data['text_login'] = $this->language->get('text_login');
		$this->data['text_register'] = $this->language->get('text_register');
		//$this->data['text_about'] = $this->language->get('text_about') . ' ' . $this->config->get('config_name');
		$this->data['text_desktop_version'] = $this->language->get('text_desktop_version');
		
		$this->load->model('catalog/information');
		
		$this->data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
      		$this->data['informations'][] = array(
        		'title' => $result['title'],
	    		'href'  => $this->url->link('mobile_store/information', 'information_id=' . $result['information_id'])
      		);
    	}

		$this->data['contact'] = $this->url->link('mobile_store/contact');
		$this->data['account'] = $this->url->link('mobile_store/account', '', 'SSL');		
		$this->data['login'] = $this->url->link('mobile_store/login', '', 'SSL');		
		$this->data['register'] = $this->url->link('mobile_store/register', '', 'SSL');		
		$this->data['desktop_version'] = $this->url->link('common/home', 'view_type=desktop', 'SSL');		

		$this->data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/footer.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/footer.tpl';
		} else {
			$this->template = 'default/template/mobile_store/footer.tpl';
		}
		
		$this->render();
		
	}
}
?>