<?php
include_once(DIR_APPLICATION."controller/weixin/weixin.php");

class ControllerWeixinWarning extends ControllerWeixinWeixin { 
	public function index() {
		
		if ($this->check_notify()) {
			$this->load->model('checkout/order');
			$this->model_checkout_order->update($this->request->post['out_trade_no'], 2);
			$this->response->setOutput("success");
			return;
		}
		$this->response->setOutput("fail");
	}
	
	public function check_notify() {
		return true;
	}
}

?>