<?php 
class ControllerMobileStoreAccount extends Controller { 
	public function index() {
		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('mobile_store/account', '', 'SSL');
	  
	  		$this->redirect($this->url->link('mobile_store/login', '', 'SSL'));
    	} 
	
		$this->language->load('mobile_store/account');

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
		
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_my_account'] = $this->language->get('text_my_account');
		$this->data['text_my_orders'] = $this->language->get('text_my_orders');
		$this->data['text_my_newsletter'] = $this->language->get('text_my_newsletter');
    	$this->data['text_edit'] = $this->language->get('text_edit');
    	$this->data['text_password'] = $this->language->get('text_password');
    	$this->data['text_address'] = $this->language->get('text_address');
		$this->data['text_wishlist'] = $this->language->get('text_wishlist');
    	$this->data['text_order'] = $this->language->get('text_order');
    	$this->data['text_download'] = $this->language->get('text_download');
		$this->data['text_reward'] = $this->language->get('text_reward');
		$this->data['text_return'] = $this->language->get('text_return');
		$this->data['text_transaction'] = $this->language->get('text_transaction');
		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_logout'] = $this->language->get('text_logout');

    	$this->data['edit'] = $this->url->link('mobile_store/edit', '', 'SSL');
    	$this->data['password'] = $this->url->link('mobile_store/password', '', 'SSL');
		$this->data['address'] = $this->url->link('mobile_store/address', '', 'SSL');
		$this->data['logout'] = $this->url->link('mobile_store/logout', '', 'SSL');
		
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/account.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/account.tpl';
		} else {
			$this->template = 'default/template/mobile_store/account.tpl';
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