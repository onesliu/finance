<?php
class ControllerMobileStoreCoupon extends Controller {
	//查询客户有什么优惠劵，有则显示
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->log->write("coupon: not logined.");
			return;
		}

		$this->load->model('checkout/coupon');
		$coupons = $this->model_checkout_coupon->getCustomerCoupon($this->customer->getId());
		if ($coupons == null) {
			return;
		}
		
		$this->session->data['coupons'] = $coupons;
		$this->data['coupons'] = $coupons;
		$this->data['coupon_url'] = $this->url->link('mobile_store/coupon/calculate');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mobile_store/coupon.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/mobile_store/coupon.tpl';
		} else {
			$this->template = 'default/template/mobile_store/coupon.tpl';
		}

		$this->render();
  	}
	
  	//用户选择使用优惠劵，计算优惠金额
	public function calculate() {
		
		$json = new stdClass();

		if (!isset($this->request->get['order_total']) || !isset($this->request->get['coupon_id']) ||
			!isset($this->session->data['coupons'])) {
			$json->status = -1;
			$this->response->setOutput(json_encode($json));
			return;
		}
		$order_total = $this->request->get['order_total'];
		$coupon_id = $this->request->get['coupon_id'];
		$coupons = $this->session->data['coupons'];
		
		$json->status = 0;
		$json->total = $order_total;
		$json->discount = 0.0;
		
		foreach($coupons as $coupon) {
			if ($coupon['coupon_id'] == $coupon_id) {
				if ($coupon['type'] == 'F') {
					if ($order_total <= $coupon['amount']) {
						$json->discount = $json->total;
						$json->remain = 0;
					}
					else {
						$json->discount = $coupon['amount'];
						$json->remain = $order_total - $coupon['amount'];
					}
				}
				else if ($coupon['type'] == 'P') {
					$json->remain = $json->total * $coupon['discount'] / 100;
					$json->discount = $order_total - $json->remain;
				}
			}
		}
		
		$c["order_total"] = $order_total;
		$c["remain"] = $json->remain;
		$c["discount"] = $json->discount;
		$c['coupon_id'] = $coupon_id;
		$this->session->data['coupon'] = $c;
		
		$this->response->setOutput(json_encode($json));
	}
	
	//订单支付完毕提交优惠金额
	public function commit() {
		
		$json = new stdClass();
		
		if (!isset($this->session->data['order_info']) || !isset($this->request->get['coupon_id']) ||
			!isset($this->session->data['coupons']) || !isset($this->session->data['coupon'])) {
			$json->status = -1;
			$this->response->setOutput(json_encode($json));
			return;
		}
		$order_info = $this->session->data['order_info'];
		$coupon_id = $this->request->get['coupon_id'];
		
		$this->load->model('checkout/coupon');
		$ret = $this->model_checkout_coupon->commitCoupon($this->customer->getId(), $coupon_id,
				$order_info['order_id'], $order_info['total']);
		if ($ret == false)
			$json->status = -1;
		else
			$json->status = 0;

		$this->response->setOutput(json_encode($json));
	}
}
?>