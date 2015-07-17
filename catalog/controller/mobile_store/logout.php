<?php 
class ControllerMobileStoreLogout extends Controller {
	public function index() {
    	if ($this->customer->isLogged()) {
      		$this->customer->logout();
	  		$this->cart->clear();
			
			unset($this->session->data['wishlist']);
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);			
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			
      		$this->redirect($this->url->link('mobile_store/logout', '', 'SSL'));
    	}
 
    	$this->language->load('account/logout');
		
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
        	'text'      => $this->language->get('text_logout'),
			'href'      => $this->url->link('account/logout', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);	
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_message'] = $this->language->get('text_message');

    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('mobile_store/home');
		
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