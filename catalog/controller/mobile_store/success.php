<?php 
class ControllerMobileStoreSuccess extends Controller {  
	public function index() {
    	$this->language->load('account/success');
  
    	$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),       	
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_success'),
			'href'      => $this->url->link('account/success'),
        	'separator' => $this->language->get('text_separator')
      	);

    	$this->data['heading_title'] = $this->language->get('heading_title');

		if (!$this->config->get('config_customer_approval')) {
    		$this->data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('mobile_store/contact'));
		} else {
			$this->data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('mobile_store/contact'));
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');
		
		if ($this->cart->hasProducts()) {
			$this->data['continue'] = $this->url->link('mobile_store/cart', '', 'SSL');
		} else {
			$this->data['continue'] = $this->url->link('mobile_store/account', '', 'SSL');
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/success.tpl';
		} else {
			$this->template = 'default/template/mobile_store/success.tpl';
		}
		
		$this->children = array(
			'mobile_store/content_top',
			'mobile_store/content_bottom',
			'mobile_store/footer',
			'mobile_store/header'	
		);
						
		$this->response->setOutput($this->render());				
  	}
}
?>