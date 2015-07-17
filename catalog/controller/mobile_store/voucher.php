<?php 
class ControllerMobileStoreVoucher extends Controller {
	public function index() {
		$this->language->load('total/voucher');
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['entry_voucher'] = $this->language->get('entry_voucher');
		
		$this->data['button_voucher'] = $this->language->get('button_voucher');
				
		if (isset($this->session->data['voucher'])) {
			$this->data['voucher'] = $this->session->data['voucher'];
		} else {
			$this->data['voucher'] = '';
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/voucher.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/voucher.tpl';
		} else {
			$this->template = 'default/template/mobile_store/voucher.tpl';
		}
					
		$this->render();
  	}
		
	public function calculate() {
		$this->language->load('total/voucher');
		
		$json = array();
		
		if (!$this->cart->hasProducts()) {
			$json['redirect'] = $this->url->link('mobile_store/cart');				
		}	
				
		if (isset($this->request->post['voucher'])) {
			$this->load->model('checkout/voucher');
	
			$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);			
			
			if ($voucher_info) {			
				$this->session->data['voucher'] = $this->request->post['voucher'];
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$json['redirect'] = $this->url->link('mobile_store/cart', '', 'SSL');
			} else {
				$json['error'] = $this->language->get('error_voucher');
			}
		}
		
		$this->response->setOutput(json_encode($json));		
	}
}
?>